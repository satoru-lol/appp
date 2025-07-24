<?php

namespace App\Services\V2;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductPermission;
use App\Repositories\V2\UserRepository;
use App\Services\SubscriptionService;
use App\Services\V2\ClubService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProfileService
{
    public function __construct(
        private UserRepository $userRepository,
        private SubscriptionService $subscriptionService,
        private ClubService $clubService
    ) {}

    public function getUserDashboardData(int $userId, string $activeTab = 'profile'): array
    {
        $user = $this->userRepository->findWithRelations($userId);
        
        // Получаем статус подписки через сервис
        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser($user);
        $subscription = $subscriptionStatus->subscription;

        // Создаем пробную подписку если её нет
        if (!$subscription) {
            $subscription = $this->createTrialSubscription($user);
            $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser($user);
        }

        // Рассчитываем дни до окончания
        $expiredAt = $subscription->expired_at ? Carbon::parse($subscription->expired_at)->startOfDay() : null;
        $daysLeft = $expiredAt && $subscriptionStatus->isActive 
            ? Carbon::now()->startOfDay()->diffInDays($expiredAt, false) 
            : 0;

        // Получаем доступные продукты
        $products = $this->getAvailableProducts($subscription->level);
        
        // Название текущей подписки
        $subscriptionTxt = $this->getCurrentSubscriptionName($subscription->level);

        // Получаем разрешения для продукта
        $productPermission = ProductPermission::where('product_id', $subscription->level)->first();

        // Получаем курсы если есть доступ
        $courseContents = collect();
        if ($subscriptionStatus->isActive && $productPermission?->course) {
            $courseContents = \App\Models\CourseContent::with('course')->get();
        }

        // Получаем клубы если есть доступ
        $clubs = collect();
        if ($subscriptionStatus->isActive && $subscriptionStatus->hasClubAccess) {
            $clubs = $this->clubService->getClubsByUserLevel($subscription->level);
        }

        // Получаем транзакции и платежи
        $mergedDataPaginated = $this->getMergedTransactions($userId);

        // Получаем встречи пользователя
        $meetings = $this->userRepository->getUserMeetings($userId);

        return compact(
            'user', 'subscription', 'daysLeft', 'products', 'subscriptionTxt',
            'productPermission', 'courseContents', 'clubs', 'mergedDataPaginated',
            'meetings', 'subscriptionStatus', 'activeTab'
        );
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $user = User::findOrFail($userId);
        return $this->userRepository->updateProfile($user, $data);
    }

    public function updateAvatar(int $userId, $file): array
    {
        $user = User::findOrFail($userId);
        
        try {
            $filename = $this->userRepository->saveAvatar($user, $file);
            $avatarUrl = route('v2.avatar', ['userId' => $user->id]) . '?t=' . time();
            
            return [
                'success' => true,
                'message' => 'Аватар успешно обновлен.',
                'avatar_url' => $avatarUrl
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка при загрузке аватара: ' . $e->getMessage()
            ];
        }
    }

    public function removeAvatar(int $userId): array
    {
        $user = User::findOrFail($userId);
        $deleted = $this->userRepository->removeAvatar($user);
        
        return [
            'success' => $deleted,
            'message' => $deleted ? 'Аватар удален.' : 'Аватар не найден.'
        ];
    }

    public function getAvatarResponse(int $userId): \Symfony\Component\HttpFoundation\Response
    {
        $user = User::findOrFail($userId);
        $avatarPath = $this->userRepository->getAvatarPath($user);
        
        if ($avatarPath && file_exists(public_path('img/avatars/' . $avatarPath))) {
            $fullPath = public_path('img/avatars/' . $avatarPath);
            return response()->file($fullPath, [
                'Cache-Control' => 'public, max-age=3600',
                'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 3600)
            ]);
        }
        
        // Возвращаем дефолтный аватар
        $defaultAvatar = public_path('img/default-avatar.png');
        if (file_exists($defaultAvatar)) {
            return response()->file($defaultAvatar);
        }
        
        // Если нет дефолтного аватара, создаем простое изображение
        return $this->generateDefaultAvatar($user);
    }

    private function createTrialSubscription(User $user): \App\Models\Subscription
    {
        $trialLevel = config('subscriptions.products.trial.level', 1);
        
        return \App\Models\Subscription::create([
            'user_id' => $user->id,
            'level' => $trialLevel,
            'auto' => 0,
            'is_active' => 1,
            'expired_at' => Carbon::now()->addDays(7),
        ]);
    }

    private function getAvailableProducts(int $currentLevel): Collection
    {
        $transitionalLevel = config('subscriptions.products.transitional.level', 8);
        $trialLevel = config('subscriptions.products.trial.level', 1);

        return Product::where('visible', true)
            ->whereNotIn('level', [$transitionalLevel, $trialLevel])
            ->get()
            ->map(function ($product) use ($currentLevel) {
                $product->current_subscription = ($product->level == $currentLevel);
                return $product;
            });
    }

    private function getCurrentSubscriptionName(int $level): string
    {
        $product = Product::where('visible', true)
            ->where('level', $level)
            ->first();
            
        return $product?->name ?? 'Нет подписки';
    }

    private function getMergedTransactions(int $userId): LengthAwarePaginator
    {
        $transactions = $this->userRepository->getUserTransactions($userId, 50);
        $subscriptionPays = $this->userRepository->getUserSubscriptionPays($userId, 50);
        
        $mergedData = $transactions->concat($subscriptionPays)->sortByDesc('created_at');
        
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page', 1);
        $perPage = 5;
        
        return new LengthAwarePaginator(
            $mergedData->forPage($page, $perPage),
            $mergedData->count(),
            $perPage,
            $page,
            ['path' => route('v2.profile.transactions')]
        );
    }

    private function generateDefaultAvatar(User $user): \Symfony\Component\HttpFoundation\Response
    {
        // Создаем простое изображение с инициалами
        $initials = strtoupper(substr($user->firstname ?? 'U', 0, 1) . substr($user->lastname ?? 'U', 0, 1));
        
        $img = imagecreate(100, 100);
        $bgColor = imagecolorallocate($img, 150, 150, 150);
        $textColor = imagecolorallocate($img, 255, 255, 255);
        
        imagestring($img, 5, 25, 40, $initials, $textColor);
        
        ob_start();
        imagepng($img);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        imagedestroy($img);
        
        return response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}