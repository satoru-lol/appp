<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class SubscriptionService
{
    private $productLevels;

    public function __construct()
    {
        // Загружаем уровни один раз для избежания повторных вызовов
        $this->productLevels = collect(Config::get('subscriptions.products'));
    }

    /**
     * Получает статус подписки для указанного пользователя.
     *
     * @param User|null $user
     * @return object
     */
    public function getSubscriptionStatusForUser(?User $user): object
    {
        $defaultStatus = (object) [
            'isActive' => false,
            'hasClubAccess' => false,
            'hasVideoStreamAccess' => false,
            'level' => 0,
            'subscription' => null
        ];

        if (!$user) {
            return $defaultStatus;
        }

        $subscription = Subscription::where('user_id', $user->id)->first();
        if (!$subscription) {
            return $defaultStatus;
        }

        $now = Carbon::now()->startOfDay();
        $expiredAt = $subscription->expired_at ? Carbon::parse($subscription->expired_at)->startOfDay() : null;
        
        // Подписка считается активной, если у нее есть флаг is_active и не истек срок (если он установлен)
        $isActive = $subscription->is_active && ($expiredAt ? !$now->gt($expiredAt) : true);

        $currentLevel = $subscription->level;
        
        // Определяем доступ к клубам
        $clubAccessLevels = [
            config('subscriptions.products.trial.level'),
            config('subscriptions.products.basic.level'),
            config('subscriptions.products.premium.level')
        ];
        $hasClubAccess = $isActive && in_array($currentLevel, $clubAccessLevels);

        // Определяем доступ к видеотеке
        $videoAccessLevels = [
            config('subscriptions.products.trial.level'),
            config('subscriptions.products.basic.level'),
            config('subscriptions.products.premium.level'),
        ];
        $hasVideoStreamAccess = $isActive && in_array($currentLevel, $videoAccessLevels);

        return (object) [
            'isActive' => $isActive,
            'hasClubAccess' => $hasClubAccess,
            'hasVideoStreamAccess' => $hasVideoStreamAccess,
            'level' => $currentLevel,
            'subscription' => $subscription
        ];
    }

    /**
     * Возвращает массив доступных уровней курсов на основе уровня пользователя.
     *
     * @param int $userLevel
     * @return array
     */
    public function getAccessibleCourseLevels(int $userLevel): array
    {
        $accessibleLevels = [];

        $premiumLevel = $this->productLevels->get('premium')['level'] ?? null;
        $basicLevel = $this->productLevels->get('basic')['level'] ?? null;
        $trialLevel = $this->productLevels->get('trial')['level'] ?? null;

        if ($userLevel === $premiumLevel) {
            // Премиум получает доступ ко всем курсам
            $accessibleLevels = array_filter([$premiumLevel, $basicLevel, $trialLevel]);
        } elseif ($userLevel === $basicLevel) {
            // Базовый - к базовым и триальным
            $accessibleLevels = array_filter([$basicLevel, $trialLevel]);
        } elseif ($userLevel === $trialLevel) {
            // Триальный - только к триальным
            $accessibleLevels = array_filter([$trialLevel]);
        }

        return $accessibleLevels;
    }
} 