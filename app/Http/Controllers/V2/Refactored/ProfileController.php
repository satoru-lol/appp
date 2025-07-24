<?php

namespace App\Http\Controllers\V2\Refactored;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Profile\UpdateProfileRequest;
use App\Http\Requests\V2\Profile\UpdateAvatarRequest;
use App\Services\V2\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'profile');
        $userId = auth()->id();
        
        $data = $this->profileService->getUserDashboardData($userId, $activeTab);
        
        return view('v2.user.profile', $data);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $userId = auth()->id();
        $validated = $request->validated();
        
        $success = $this->profileService->updateProfile($userId, $validated);
        
        if ($success) {
            return redirect()
                ->route('v2.profile.index')
                ->with('success', 'Профиль успешно обновлен.');
        }
        
        return redirect()
            ->back()
            ->with('error', 'Ошибка при обновлении профиля.');
    }

    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $file = $request->file('avatar');
        
        $result = $this->profileService->updateAvatar($userId, $file);
        
        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function removeAvatar(): JsonResponse
    {
        $userId = auth()->id();
        
        $result = $this->profileService->removeAvatar($userId);
        
        return response()->json($result);
    }

    public function getAvatar(int $userId): Response
    {
        return $this->profileService->getAvatarResponse($userId);
    }

    public function fetchTransactions(): JsonResponse
    {
        $userId = auth()->id();
        
        // Получаем только данные транзакций
        $data = $this->profileService->getUserDashboardData($userId);
        
        return response()->json([
            'success' => true,
            'data' => $data['mergedDataPaginated']
        ]);
    }

    public function generateQrLink(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:100000',
        ]);

        $userId = auth()->id();
        $amount = $request->input('amount');
        
        // Здесь должна быть логика генерации QR-кода
        // Пока возвращаем заглушку
        $qrLink = "https://example.com/pay?user={$userId}&amount={$amount}";
        
        return response()->json([
            'success' => true,
            'qr_link' => $qrLink,
            'message' => 'QR-код сгенерирован'
        ]);
    }

    public function handleSubscription(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'payment_method' => 'required|string|in:robokassa,balance'
        ]);

        $userId = auth()->id();
        $productId = $request->input('product_id');
        $paymentMethod = $request->input('payment_method');
        
        try {
            // Здесь должна быть логика обработки подписки
            // Пока возвращаем успешный результат
            
            return redirect()
                ->route('v2.profile.index')
                ->with('pay_success', 'Подписка успешно оформлена!');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('v2.profile.index', ['tab' => 'subscription'])
                ->with('pay_error', 'Произошла ошибка при оформлении подписки: ' . $e->getMessage());
        }
    }

    public function addBalance(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:100000',
        ]);

        $userId = auth()->id();
        $amount = $request->input('amount');
        
        try {
            // Здесь должна быть логика пополнения баланса
            // Пока возвращаем успешный результат
            
            return redirect()
                ->route('v2.profile.index', ['tab' => 'balance'])
                ->with('pay_success', 'Ваш платеж обрабатывается. Баланс будет пополнен в течение нескольких минут.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('v2.profile.index', ['tab' => 'balance'])
                ->with('pay_error', 'Произошла ошибка во время оплаты. Пожалуйста, попробуйте снова или обратитесь в поддержку.');
        }
    }
}