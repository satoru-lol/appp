<?php

namespace app\Http\Controllers;

use App\Http\Requests\IntroductionRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\Club;
use App\Models\Course;
use App\Models\Introduction as ModelsIntroduction;
use App\Models\Specialist;
use App\Models\User;
use App\Models\ViewParts;
use App\Service\IntroductionService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;


class HomeController extends Controller
{

    private $introductionService;

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
        if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["reg"]) && $perms["add"]["reg"] == true) {
                $isPermittedAdd = true;
            }
        }
		return view('home.reg', compact('user', 'blogs', 'categories', 'isPermittedAdd'));
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
        $specialists = Specialist::where('status', 1)->orderBy('views')->limit(10)->get();

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

        return view('home.index', compact('user', 'specialists', 'courses', 'blogs'));
    }

    public function about(Request $request): View
    {
        return view('home.about');
    }
    public function pages(string $page, Request $request)
    {
        if(view()->exists('pages.'.$page))
            return view('pages.'.$page)->render();

        die('');
    }

    public function introduction(): View
    {
        return view('home.introduction');
    }

    public function introductionSend(IntroductionRequest $request): RedirectResponse
    {

        $request->validated();
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

        $introduction = ModelsIntroduction::where('hash', $hash)->first();
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

		if(!User::where('phone',$introduction->phone)->exists()){
			User::create([
				'firstname' => $introduction->firstname,
				'lastname' => $introduction->lastname,
				'email' => $introduction->email,
				'password' => Hash::make(Str::random(15)),
				'phone' => $introduction->phone,
                'permissions' => $userPermissions
			]);
		}

        return redirect(route('login'))->with('success', __('Вы успешно вступили в ассоциацию, теперь может войти в личный кабинет по номеру телефона'));
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
}
