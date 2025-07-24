<?php

/**
 * @deprecated Этот контроллер устарел. Используйте App\Http\Controllers\V2\Refactored\AuthController
 * 
 * ВНИМАНИЕ: Данный файл будет удален в будущих версиях.
 * Новая архитектура авторизации находится в app/Http/Controllers/V2/Refactored/AuthController.php
 * 
 * Миграция:
 * - Старые маршруты: /login, /register, /logout (основные)
 * - Новые маршруты: /v2/refactored/auth/* (полная система авторизации)
 * 
 * Новая архитектура включает:
 * - Form Requests для валидации
 * - AuthService для бизнес-логики
 * - AuthRepository для работы с данными
 * - Поддержка AJAX и API
 * 
 * @see App\Http\Controllers\V2\Refactored\AuthController
 */

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Introduction as ModelsIntroduction;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Course;
use App\Models\Club;
use App\Models\ParticipantActions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Lang;


use App\Models\Product;
use App\Models\Roles;

use App\Models\SubscriptionPays;
use App\Models\Transactions;

use Illuminate\View\View;
use Carbon\Carbon;

use DB;


class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login-v2');
    }

    /**
     * Show the form for requesting a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function passwordReset()
    {
        return view('auth.password-email-v2'); // Создайте этот файл
    }

    /**
     * Send a password reset link to the given email address.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function passwordEmail(Request $request)
    {
        $this->validateEmail($request);

        $response = Password::sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }

    protected function sendResetLinkResponse($response)
    {
        return Redirect::route('password.request')
            ->with('status', trans($response));
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return Redirect::route('password.request')
            ->withErrors(
                ['email' => trans($response)]
            );
    }


    public function confirmCode(Request $request)
    {

        /*   $request->validate([
               'code' => 'required|string',
               'password' => 'required|string', // Проверка пароля
           ]);*/

        $code = $request->input('code');
        $password = $request->input('password'); // Получаем пароль

        $user = User::where("id", $request->get("user_id"))->first();

        $codeIsValid = false;

        if ($code == $user->verification_code) {
            $user->verification_code = null;
            $user->save();

            if (!$user->userPhoneVerified()) {
                $user->phoneVerifiedAt();
            }
            $codeIsValid = true;
        }


        //return response()->json(['success' => true]);

        if ($codeIsValid) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Неверный код.']);
        }

        return response()->json(['success' => false, 'message' => 'Произошла ошибка.']);

    }

    /**
     * Handle an authentication attempt.
     */
    public function Auth(AuthRequest $request): RedirectResponse
    {
        Log::info('Auth attempt started.');

        // Получаем email и пароль из запроса
        $credentials = $request->only('email', 'password');
        Log::info('Credentials received:', ['email' => $credentials['email'] ?? 'not_provided']);

        // Находим пользователя по email
        $user = User::where('email', $credentials['email'])->first();

        // 1. Проверяем, существует ли пользователь и правильный ли пароль
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            Log::warning('Auth failed: Invalid credentials.', ['email' => $credentials['email']]);
            return back()->withErrors(['email' => 'Предоставленные учетные данные неверны.'])->withInput($request->except('password'));
        }
        
        Log::info('Password check passed. Logging in user.', ['user_id' => $user->id]);

        // 2. Телефон не проверяем, сразу авторизуем пользователя.
        Auth::login($user, true);
        $request->session()->regenerate();
        
        // Эта логика добавляет пользователю курсы и клубы при входе.
        // Оставим ее здесь, так как она, видимо, нужна.
        Log::info('Adding courses and clubs for user.', ['user_id' => $user->id]);
        foreach ([50, 68] as $course) {
            if (!ParticipantActions::where("user_id", $user->id)->where("object_name", "courses")->where("object_id", $course)->exists()) {
                ParticipantActions::create([
                    "object_name" => "courses",
                    "object_id" => $course,
                    "user_id" => $user->id
                ]);
            }
        }

        foreach ([26, 27] as $club) {
            if (!ParticipantActions::where("user_id", $user->id)->where("object_name", "clubs")->where("object_id", $club)->exists()) {
                ParticipantActions::create([
                    "object_name" => "clubs",
                    "object_id" => $club,
                    "user_id" => $user->id
                ]);
            }
        }

        // Перенаправляем на нужную страницу (например, в профиль)
        Log::info('Redirecting to profile.', ['user_id' => $user->id]);
        return redirect()->intended('/profile');
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

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
