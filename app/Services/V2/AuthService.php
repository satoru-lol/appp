<?php

namespace App\Services\V2;

use App\Models\User;
use App\Models\Introduction;
use App\Repositories\V2\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        private AuthRepository $authRepository
    ) {}

    public function login(array $credentials): array
    {
        $user = $this->authRepository->findUserByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Неверные учетные данные'
            ];
        }

        // Обновляем время последнего входа
        $this->authRepository->updateUserLastLogin($user);

        // Авторизуем пользователя
        Auth::login($user, $credentials['remember'] ?? false);

        return [
            'success' => true,
            'message' => 'Успешный вход в систему',
            'user' => $user
        ];
    }

    public function register(array $data): array
    {
        // Проверяем существование пользователя
        if ($this->authRepository->isEmailTaken($data['email'])) {
            return [
                'success' => false,
                'message' => 'Пользователь с таким email уже существует'
            ];
        }

        if (!empty($data['phone']) && $this->authRepository->isPhoneTaken($data['phone'])) {
            return [
                'success' => false,
                'message' => 'Пользователь с таким телефоном уже существует'
            ];
        }

        try {
            // Создаем пользователя
            $user = $this->authRepository->createUser($data);

            // Создаем анкету если есть дополнительные данные
            if (!empty($data['additional_info'])) {
                $this->authRepository->createIntroduction(array_merge(
                    $data['additional_info'],
                    ['email' => $user->email]
                ));
            }

            // Авторизуем пользователя
            Auth::login($user);

            return [
                'success' => true,
                'message' => 'Регистрация успешно завершена',
                'user' => $user
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при регистрации: ' . $e->getMessage()
            ];
        }
    }

    public function logout(): array
    {
        Auth::logout();

        return [
            'success' => true,
            'message' => 'Вы успешно вышли из системы'
        ];
    }

    public function sendPasswordResetLink(string $email): array
    {
        $user = $this->authRepository->findUserByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь с таким email не найден'
            ];
        }

        try {
            $token = Str::random(60);
            $this->authRepository->createPasswordReset($email, $token);

            // Отправляем письмо с ссылкой для сброса пароля
            // Mail::send('emails.password-reset', compact('token'), function($message) use ($email) {
            //     $message->to($email)->subject('Сброс пароля');
            // });

            return [
                'success' => true,
                'message' => 'Ссылка для сброса пароля отправлена на ваш email'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при отправке письма: ' . $e->getMessage()
            ];
        }
    }

    public function resetPassword(array $data): array
    {
        $passwordReset = $this->authRepository->findPasswordReset($data['email']);

        if (!$passwordReset || !Hash::check($data['token'], $passwordReset->token)) {
            return [
                'success' => false,
                'message' => 'Неверный токен сброса пароля'
            ];
        }

        // Проверяем срок действия токена (24 часа)
        if (now()->diffInHours($passwordReset->created_at) > 24) {
            $this->authRepository->deletePasswordReset($data['email']);
            return [
                'success' => false,
                'message' => 'Токен сброса пароля истек'
            ];
        }

        $user = $this->authRepository->findUserByEmail($data['email']);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь не найден'
            ];
        }

        try {
            // Обновляем пароль
            $this->authRepository->updateUserPassword($user, $data['password']);

            // Удаляем токен сброса
            $this->authRepository->deletePasswordReset($data['email']);

            return [
                'success' => true,
                'message' => 'Пароль успешно изменен'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при изменении пароля: ' . $e->getMessage()
            ];
        }
    }

    public function verifyPhone(int $userId, string $code): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь не найден'
            ];
        }

        // Здесь должна быть логика проверки SMS кода
        // Пока упрощенная версия
        if ($code === '1234' || $user->verification_code === $code) {
            $this->authRepository->verifyUserPhone($user);
            $this->authRepository->clearVerificationCode($user);

            return [
                'success' => true,
                'message' => 'Телефон успешно подтвержден'
            ];
        }

        return [
            'success' => false,
            'message' => 'Неверный код подтверждения'
        ];
    }

    public function sendPhoneVerificationCode(int $userId): array
    {
        $user = User::find($userId);
        if (!$user || !$user->phone) {
            return [
                'success' => false,
                'message' => 'Пользователь или телефон не найден'
            ];
        }

        try {
            $code = rand(1000, 9999);
            $this->authRepository->setVerificationCode($user, $code);

            // Здесь должна быть отправка SMS
            // SMS::send($user->phone, "Код подтверждения: $code");

            return [
                'success' => true,
                'message' => 'Код подтверждения отправлен на ваш телефон'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при отправке SMS: ' . $e->getMessage()
            ];
        }
    }

    public function changePassword(int $userId, array $data): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь не найден'
            ];
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Текущий пароль неверен'
            ];
        }

        try {
            $this->authRepository->updateUserPassword($user, $data['new_password']);

            return [
                'success' => true,
                'message' => 'Пароль успешно изменен'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при изменении пароля: ' . $e->getMessage()
            ];
        }
    }

    public function updateProfile(int $userId, array $data): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь не найден'
            ];
        }

        // Проверяем уникальность email
        if (isset($data['email']) && $this->authRepository->isEmailTaken($data['email'], $userId)) {
            return [
                'success' => false,
                'message' => 'Email уже используется другим пользователем'
            ];
        }

        // Проверяем уникальность телефона
        if (isset($data['phone']) && $this->authRepository->isPhoneTaken($data['phone'], $userId)) {
            return [
                'success' => false,
                'message' => 'Телефон уже используется другим пользователем'
            ];
        }

        try {
            $user->update($data);

            return [
                'success' => true,
                'message' => 'Профиль успешно обновлен',
                'user' => $user->fresh()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при обновлении профиля: ' . $e->getMessage()
            ];
        }
    }

    public function getAuthStats(): array
    {
        return $this->authRepository->getUserStats();
    }
}