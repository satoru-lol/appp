<?php

namespace App\Http\Controllers\V2\Refactored;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Auth\LoginRequest;
use App\Http\Requests\V2\Auth\RegisterRequest;
use App\Services\V2\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    // Показать форму входа
    public function showLogin(Request $request): View
    {
        if (auth()->check()) {
            return redirect()->intended('/dashboard');
        }

        $title = 'Вход в систему - АЧПП';
        $description = 'Войдите в свой аккаунт Ассоциации частнопрактикующих психологов и психотерапевтов';
        
        return view('refactored.auth.login', compact('title', 'description'));
    }

    // Обработка входа
    public function login(LoginRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect' => $result['redirect'] ?? '/dashboard'
                ]);
            }

            return redirect()->intended($result['redirect'] ?? '/dashboard')
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 401);
            }

            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', $e->getMessage());
        }
    }

    // Показать форму регистрации
    public function showRegister(Request $request): View
    {
        if (auth()->check()) {
            return redirect()->intended('/dashboard');
        }

        $title = 'Регистрация - АЧПП';
        $description = 'Зарегистрируйтесь в Ассоциации частнопрактикующих психологов и психотерапевтов';
        
        return view('refactored.auth.register', compact('title', 'description'));
    }

    // Обработка регистрации
    public function register(RegisterRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $result = $this->authService->register(
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect' => '/dashboard'
                ]);
            }

            return redirect('/dashboard')
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', $e->getMessage());
        }
    }

    // Выход из системы
    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $result = $this->authService->logout(auth()->id());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return redirect('/login')
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при выходе из системы'
                ], 500);
            }

            return redirect('/login');
        }
    }

    // Показать форму восстановления пароля
    public function showResetForm(Request $request): View
    {
        $title = 'Восстановление пароля - АЧПП';
        $description = 'Восстановите доступ к своему аккаунту в АЧПП';
        
        return view('refactored.auth.password-reset', compact('title', 'description'));
    }

    // Отправка ссылки для сброса пароля
    public function sendResetLink(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Неверный формат email',
            'email.exists' => 'Пользователь с таким email не найден'
        ]);

        try {
            $result = $this->authService->sendPasswordResetLink($request->input('email'));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return redirect()->back()
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    // Сброс пароля
    public function resetPassword(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'token.required' => 'Токен восстановления обязателен',
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Неверный формат email',
            'password.required' => 'Пароль обязателен для заполнения',
            'password.min' => 'Пароль должен содержать минимум 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);

        try {
            $result = $this->authService->resetPassword($request->all());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return redirect('/login')
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                ->withInput($request->only('email'))
                ->with('error', $e->getMessage());
        }
    }

    // Смена пароля (для авторизованных пользователей)
    public function changePassword(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Текущий пароль обязателен',
            'password.required' => 'Новый пароль обязателен для заполнения',
            'password.min' => 'Новый пароль должен содержать минимум 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);

        try {
            $result = $this->authService->changePassword(
                auth()->id(),
                $request->input('current_password'),
                $request->input('password')
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            }

            return redirect()->back()
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    // Подтверждение телефона
    public function verifyPhone(Request $request): JsonResponse
    {
        $request->validate([
            'verification_code' => 'required|string|size:6'
        ], [
            'verification_code.required' => 'Код подтверждения обязателен',
            'verification_code.size' => 'Код должен содержать 6 символов'
        ]);

        try {
            $result = $this->authService->verifyPhone(
                auth()->id(),
                $request->input('verification_code')
            );

            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Отправка кода подтверждения телефона
    public function sendVerificationCode(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->sendPhoneVerificationCode(auth()->id());

            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Получение статистики авторизации (для админов)
    public function getAuthStats(Request $request): JsonResponse
    {
        $this->authorize('view-auth-stats');

        try {
            $stats = $this->authService->getAuthStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении статистики'
            ], 500);
        }
    }

    // Проверка доступности email (AJAX)
    public function checkEmailAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $isAvailable = $this->authService->isEmailAvailable($request->input('email'));

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Email доступен' : 'Email уже занят'
        ]);
    }

    // Проверка доступности телефона (AJAX)
    public function checkPhoneAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $isAvailable = $this->authService->isPhoneAvailable($request->input('phone'));

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Телефон доступен' : 'Телефон уже занят'
        ]);
    }
}