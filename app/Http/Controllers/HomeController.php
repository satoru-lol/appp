<?php

namespace App\Http\Controllers;

use App\Http\Requests\IntroductionRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\ParticipantActions;
use App\Models\Club;
use App\Models\Course;
use App\Models\Specialist;
use App\Models\Introduction;
use App\Models\User;
use App\Models\ViewParts;
use App\Service\IntroductionService;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Mail\SendPassword;
use App\Models\Subscription;
use App\Models\Video;
use App\Models\Category;

class HomeController extends Controller
{

    private $introductionService;
    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function deleteEntRow($id, $entity, Request $request)
    {
        if (!empty($id) && !empty($entity)) {
            if ($entity == "blog_comments") {
                BlogComment::destroy($id);
                return back();
            } else if ($entity == "club") {
                Club::destroy($id);
                return back();
            }
        }

    }

    public function hideCourse($id, $action, Request $request)
    {
        $course = Course::where("id", $id)->first();
        if (!empty($course)) {
            $course->is_hidden = $action == "hide" ? true : false;
            $course->save();
            return back();
        }
        return back();

    }

    public function hideClub($id, $action, Request $request)
    {
        $club = Club::where("id", $id)->first();
        if (!empty($club)) {
            $club->is_hidden = $action == "hide" ? true : false;
            $club->save();
            return back();
        }
        return back();

    }


	public function test(Request $request){

		Auth::loginUsingId(1);
		/*echo User::create([
				'firstname' => '111',
				'lastname' => '22',
				'email' => 'nf.morkovin@gmail.com',
				'password' => Hash::make('1234'),
				'phone' => '+79158625892'
			]);*/
			//print_r(User::all());


		//echo ModelsIntroduction::where('email', 'nf.morkovin@gmail.com')->delete();
		//echo ModelsIntroduction::where('phone', '+79158625891')->delete();
		//echo User::where('phone', '+79158625891')->delete();
	}

	public function regmerop(Request $request, int $blog_category_id = 0){

		 $user = $request->user();

        if($blog_category_id) {
            if($user && $user->group === 'admin')
                $blogs = Blog::where('blog_category_id', $blog_category_id)->where('reg', '1')->paginate(15);
            else
                $blogs = Blog::where('blog_category_id', $blog_category_id)->where('reg', '1')->where('status', 1)->paginate(15);
        } else {
            if($user && $user->group === 'admin')
                $blogs = Blog::where('reg', '1')->paginate(15);
            else
                $blogs = Blog::where('status', 1)->where('reg', '1')->paginate(15);
        }

        if($user && $user->group === 'admin')
            $categories = BlogCategory::all();
        else
            $categories = BlogCategory::where('status', 1)->get();

        $isPermittedAdd = false;
        /*if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["reg"]) && $perms["add"]["reg"] == true) {
                $isPermittedAdd = true;
            }
        }*/

        if ($user && $user->group === 'admin'){
            $isPermittedAdd = true;
        }
		return view('home.reg', compact('user', 'blogs', 'categories', 'isPermittedAdd'));
	}

    public function takePart($objectName, $objectId)
    {
        $quantity = Blog::where('id',$objectId)->value('quantity');

        if (is_null($quantity) || !ParticipantActions::where('object_id', $objectId)->where("object_name", $objectName)->where("user_id", auth()->user()->id)->exists()) {
            $count = ParticipantActions::where('object_id', $objectId)->where("object_name", $objectName)->count();
            if (is_numeric($quantity) && $count == $quantity){
                return redirect()->back()->withErrors('На встречу записалось максимальное количество участников');
            }
            ParticipantActions::create([
                "user_id" => auth()->user()->id,
                "object_id" => $objectId,
                "object_name" => $objectName
            ]);
            return back();
        }
        return back();
    }

    public function takePartDelete($objectId, $userId)
    {

        if (ParticipantActions::where('user_id', $userId)->where("object_id", $objectId)->exists()) {
            ParticipantActions::where('user_id', $userId)->where("object_id", $objectId)->delete();
            return back();
        }

        return back();
    }

    public function deleteRegmerop(int $id, Request $request): RedirectResponse
    {

        if (Blog::where("id", $id)->exists()) {
            Blog::where("id", $id)->delete();
        }
        return back()->with("success", __("Блог удален"));
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        if($user && $user->group == 'block') {
            auth()->logout();
        }

        $viewParts = ViewParts::all();
        $specialists = Specialist::with(['user', 'category'])
            ->active()
            ->orderBy('views', 'asc')
            ->limit(10)
            ->get();

        if ($viewParts->isNotEmpty()) {
            foreach ($viewParts as $viewPart) {
                if ($viewPart->role_content == "Courses") {
                    $courses = Course::where('course_category_id', $viewPart->category_id)->orderBy('views')->limit(10)->get();
                } else if ($viewPart->role_content == "Blog") {
                    $blogs = Blog::where('blog_category_id', $viewPart->category_id)->limit(10)->get();
                }
            }
        } else {
            if (auth()->user() && \auth()->user()->group == "admin") {
                $courses = Course::all();
            } else {
                $courses = Course::where("is_hidden", null)->get();
            }

            $blogs = Blog::where('status', 1)->limit(10)->get();
        }

        $title = 'АЧПП - Ассоциация частнопрактикующих психологов и психотерапевтов';
        $description = 'Официальный сайт Ассоциации частнопрактикующих психологов и психотерапевтов (АЧПП). Присоединяйтесь к нашему сообществу для профессионального роста и поддержки.';
        $keywords = 'АЧПП, ассоциация психологов, психотерапевты, профессиональное сообщество, психологическая помощь, супервизия';

        return view('v2.home.index', compact('user', 'specialists', 'courses', 'blogs', 'title', 'description', 'keywords'));
    }

    public function sitemap()
    {
        return response()->view('sitemap')->header('Content-Type', 'text/xml');
    }

    public function about(Request $request): View
    {
        $title = 'Об Ассоциации';
        $description = 'Узнайте больше об Ассоциации частнопрактикующих психологов и психотерапевтов (АЧПП), нашей миссии, ценностях и команде.';
        $keywords = 'о нас, миссия АЧПП, ценности ассоциации, команда психологов, история ассоциации, цели АЧПП';

        return view('home.about-v2', compact('title', 'description', 'keywords'));
    }
    public function pages(string $page, Request $request)
    {
        if(view()->exists('pages.'.$page))
            return view('pages.'.$page)->render();

        die('');
    }

    public function introduction(): View
    {
        return view('home.introduction-v2');
    }

    public function introductionSend(Request $request): RedirectResponse
    {

       // $request->validated();
        try {
            $introductionService = new IntroductionService();
            $introductionService->storeIntroduction($request->all());
            return back()->with('success', __('На вашу почту было отправлено письмо со ссылкой для подтверждения вступления.'));
        } catch (\Exception $exception) {
            return back()->withErrors([
                'error' => $exception->getMessage()
            ]);
        }
    }


	private function sendMail($temp, $data, $to_name, $to_email){
        Mail::send($temp, $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)->subject('11111111111111');
            $message->from('appp-psy@yandex.ru','YeMorkovin.ru');
        });
    }

    public function introductionConfirm(string $hash, Request $request): RedirectResponse
    {

        $introduction = Introduction::where('hash', $hash)->first();
		if(!$introduction){
			return redirect(route('introduction'))->with('error', __('Ваш хеш код не правильный , возможно вы подтвердили ваш аккаунт из старых писем'));
		}

        $userPermissions = [
           "add" => [
               "blog" => false,
               "course" => false,
               "club" => false,
               "forum" => true,
               'polygon' => false,
               'reg' => false
           ],
            "edit" => [
                "blog" => false,
                "course" => false,
                "club" => false,
                "forum" => false
            ],
        ];
        $userPermissions = json_encode($userPermissions);
        $password = Str::random(15);
        $hashedPassword = Hash::make($password);
        
        Mail::to($introduction->email)->send(new SendPassword($password));
        
        if(!empty($introduction->email)){
            $user = User::where('phone',$introduction->phone)->first();
            if (!$user){
                $user = User::create([
                    'firstname' => $introduction->firstname,
                    'lastname' => $introduction->lastname,
                    'email' => $introduction->email,
                    'password' => $hashedPassword,
                    'phone' => $introduction->phone,
                    'permissions' => $userPermissions
                ]);
                Subscription::create([
                    'user_id' => $user->id,
                    'level' => 0,
                    "is_active" => false,
                ]);
            }
        }

        return redirect(route('login'))->with('success', __("Вы успешно вступили в ассоциацию, теперь можете войти в личный кабинет по эл. почте и паролю который был отправлен на эл. почту $introduction->email"));
    }

    public function privacy_policy(): View
    {
        return view('home.privacy_policy');
    }

    public function terms(): View
    {
        return view('home.terms');
    }

    public function VerificationCheck(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('home');
    }

    public function VerificationNotice(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return __('На вашу почту была отправлена ссылка для подтверждения вступления');
    }
    
    public function videostream(Request $request){
        $user = auth()->user();
        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser($user);

        // Используем новый ключ для доступа к видеотеке
        $hasAccess = $subscriptionStatus->hasVideoStreamAccess;

        return view('videostream', [
            'user' => $user,
            'hasAccess' => $hasAccess
        ]);
    }
}
