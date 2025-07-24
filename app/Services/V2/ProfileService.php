<?php

namespace App\Services\V2;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductPermission;
use App\Repositories\V2\UserRepository;
use App\Services\SubscriptionService;
use App\Services\AvatarService;
use App\Services\V2\ClubService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProfileService
{
    public function __construct(
        private UserRepository $userRepository,
        private SubscriptionService $subscriptionService,
        private ClubService $clubService,
        private AvatarService $avatarService
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
        
        return $this->avatarService->uploadAvatar($user, $file);
    }

    public function removeAvatar(int $userId): array
    {
        $user = User::findOrFail($userId);
        
        return $this->avatarService->deleteAvatar($user);
    }

    public function getAvatarResponse(int $userId): \Symfony\Component\HttpFoundation\Response
    {
        $user = User::findOrFail($userId);
        
        // Если есть аватар в новой системе
        if ($user->avatar && \Storage::exists('public/' . $user->avatar)) {
            $fullPath = storage_path('app/public/' . $user->avatar);
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
        
        // Генерируем аватар через redirect на внешний сервис
        $avatarUrl = $this->avatarService->generateAvatarUrl($user);
        return redirect($avatarUrl);
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

    /**
     * Отмена ежемесячной подписки
     */
    public function cancelMonthlySubscription(int $userId): array
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \Exception('Пользователь не найден');
        }

        $subscription = $user->subscription;
        if (!$subscription || !$subscription->is_active) {
            throw new \Exception('У вас нет активной подписки');
        }

        // Логика отмены подписки
        $subscription->update([
            'is_active' => false,
            'cancelled_at' => now(),
            'cancel_reason' => 'Отменено пользователем'
        ]);

        return [
            'success' => true,
            'message' => 'Подписка успешно отменена'
        ];
    }

    /**
     * Генерация QR ссылки для быстрого доступа
     */
    public function generateQrLink(int $userId): array
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \Exception('Пользователь не найден');
        }

        // Создаем уникальную ссылку для пользователя
        $token = hash('sha256', $user->id . $user->email . time());
        $url = url("/quick-access/{$token}");

        // Сохраняем токен в базе данных (если нужно)
        // $user->update(['quick_access_token' => $token]);

        return [
            'url' => $url,
            'qr_code' => "data:image/svg+xml;base64," . base64_encode($this->generateQrCodeSvg($url)),
            'token' => $token
        ];
    }

    /**
     * Генерация простого QR кода в SVG формате
     */
    private function generateQrCodeSvg(string $data): string
    {
        // Простая заглушка для QR кода
        // В реальном проекте используйте библиотеку типа endroid/qr-code
        return '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
            <rect width="200" height="200" fill="white"/>
            <text x="100" y="100" text-anchor="middle" fill="black">QR Code</text>
            <text x="100" y="120" text-anchor="middle" fill="gray" font-size="10">Use QR library</text>
        </svg>';
    }
}