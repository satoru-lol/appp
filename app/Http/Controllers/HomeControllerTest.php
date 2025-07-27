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
use Carbon\Carbon;
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

class HomeControllerTest extends Controller
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

    public function takePart($objectName, $objectId)
    {

        if (!ParticipantActions::where('object_id', $objectId)->where("object_name", $objectName)->where("user_id", auth()->user()->id)->exists()) {
            ParticipantActions::create([
                "user_id" => auth()->user()->id,
                "object_id" => $objectId,
                "object_name" => $objectName
            ]);

        }

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

    public function introductionTest(): View
    {
        return view('home-test.introduction-test');
    }

    public function introductionSendTest(Request $request): RedirectResponse
    {

        // $request->validated();
        try {
            $introductionService = new IntroductionService();
            $introductionService->storeIntroductionTest($request->all());
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

    public function introductionConfirmTest(string $hash, Request $request): RedirectResponse
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
            $user = User::where('email',$introduction->email)->first();
            if (!$user){
                $user = User::create([
                    'firstname' => $introduction->firstname,
                    'lastname' => $introduction->lastname,
                    'email' => $introduction->email,
                    'password' => $hashedPassword,
                    'phone' => $introduction->phone,
                    'permissions' => $userPermissions,
                ]);
                Subscription::create([
                    'user_id' => $user->id,
                    'level' => 0,
                    "is_active" => false,
                ]);

                $user->phoneVerifiedAt();

                $participant = [
                    [
                        "object_name" => "courses",
                        "object_id" => 50,
                        "user_id" => $user->id
                    ],
                    [
                        "object_name" => "courses",
                        "object_id" => 68,
                        "user_id" => $user->id
                    ],
                    [
                        "object_name" => "clubs",
                        "object_id" => 26,
                        "user_id" => $user->id
                    ],
                    [
                        "object_name" => "clubs",
                        "object_id" => 27,
                        "user_id" => $user->id
                    ]
                ];
                $p = ParticipantActions::insert($participant);
            }
        }

        return redirect(route('login-test'))->with('success', __("Вы успешно вступили в ассоциацию, теперь можете войти в личный кабинет по эл. почте и паролю который был отправлен на эл. почту $introduction->email"));
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

    public function authTest(){
        return view('auth.login-test');
    }

    public function authLogin(Request $request){
        $mail = $request->input('mail');
        $password = $request->input('password');
        $user = User::where('email',$mail)->first();
        if (!$user){
            return back()->with([
                'email_error' => __('Пользователь не найден'),
            ]);
        }
        if (Hash::check($password, $user->password)) {
            if ($user->phone_verified_at !== null) {
                Auth::login($user, true);
                return redirect("/profile");
            }
        }else{
            $code = rand(100000, 999999);
            $this->sendSms($user->phone, $code);
            $user->verification_code = $code;
            $user->save();
            return back()->with([
                'phone_confirm' => __('На указанный номер телефона было отправлено сообщение с кодом подтверждения'),
                'class' => 'success',
                'user_id' => $user->id,
                'hidden' => true,
                'phone' => $user->phone,
            ]);
        }
        return back()->with([
            'email_error' => __('Логин или пароль некорректный'),
        ]);
    }

    private function sendSms(string $phone, string $code)
    {
//        $smsAeroMessage = new \SmsAero\SmsAeroMessage(config('settings.smsaero_email'), config('settings.smsaero_apikey'));
        $smsAeroMessage = new \SmsAero\SmsAeroMessage(env('SMS_LOGIN'), env('SMS_API_KEY'));
        Log::info('smsAero',['sms'=>$smsAeroMessage]);
        // Отправка SMS сообщений
        $response = $smsAeroMessage->send(['number' => $phone, 'text' => 'Ваш код подтверждения: ' . $code, 'sign' => env('SMS_SIGN')]);

        Log::debug($response);

        return $response['success'] ? true : false;
    }
}
