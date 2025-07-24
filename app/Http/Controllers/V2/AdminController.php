<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function subscriptionDiagnostics()
    {
        // Загружаем конфигурацию подписок
        $subscriptionProducts = config('subscriptions.products');
        $levelToNameMap = collect($subscriptionProducts)->mapWithKeys(function ($product, $name) {
            return [$product['level'] => ucfirst($name)];
        })->toArray();

        // Добавляем названия для системных уровней
        $levelToNameMap[0] = 'Без подписки (deactivated)';


        // 1. Сводка по подпискам
        $subscriptionSummary = Subscription::select('level', DB::raw('count(*) as user_count'))
            ->groupBy('level')
            ->get();

        // 2. Пользователи с проблемами синхронизации
        // Находим последние успешные платежи для каждого пользователя
        $latestSuccessfulPayments = SubscriptionPays::select('user_id', DB::raw('MAX(id) as max_id'))
            ->where('active', 1)
            ->groupBy('user_id');

        $payments = SubscriptionPays::joinSub($latestSuccessfulPayments, 'latest_pays', function ($join) {
            $join->on('subscription_pays.id', '=', 'latest_pays.max_id');
        })
        ->with(['user.subscription', 'product'])
        ->get();

    $usersWithPaymentIssues = [];
    foreach ($payments as $payment) {
        $user = $payment->user;
        if (!$user || !$payment->product) {
            continue;
        }

        $subscription = $user->subscription;
        $problem = null;

        if (!$subscription) {
            $problem = 'Отсутствует запись о подписке, хотя есть активный платеж.';
        } elseif (!$subscription->is_active) {
            $problem = 'Подписка неактивна, хотя есть последняя активная оплата.';
        } elseif ($subscription->level != $payment->product->level) {
            $problem = "Уровень подписки ({$subscription->level}) не соответствует последней оплате ({$payment->product->level}).";
        }

        if ($problem) {
            $usersWithPaymentIssues[] = [
                'user' => $user->toArray(),
                'issue' => $problem,
                'payment_product' => $payment->product->name,
                'payment_date' => $payment->created_at,
                'payment_id' => $payment->id
            ];
        }
    }
    $usersWithPaymentIssues = collect($usersWithPaymentIssues);

    return view('v2.admin.subscription-diagnostics', compact(
        'subscriptionSummary',
        'usersWithPaymentIssues',
        'levelToNameMap'
    ));
}

    public function fixSubscription(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|integer|exists:subscription_pays,id',
        ]);

        $payment = SubscriptionPays::with('product')->findOrFail($request->payment_id);
        $user = User::findOrFail($payment->user_id);
        $subscription = $user->subscription;

        if (!$subscription) {
            // Если подписки нет - создаем
            $subscription = new Subscription();
            $subscription->user_id = $user->id;
        }

        $subscription->level = $payment->product->level;
        $subscription->is_active = 1;
        
        // Попытка установить дату истечения срока, если она есть в платеже.
        // Это поле может отсутствовать, поэтому делаем проверку.
        if (isset($payment->expired_at)) {
             $subscription->expired_at = $payment->expired_at;
        }

        $subscription->save();

        return back()->with('success', 'Подписка пользователя ' . $user->email . ' успешно исправлена.');
    }

    public function revokeTransitionalSubscriptions(Request $request)
    {
        $transitionalLevel = 8; // Уровень "Transitional"

        $updatedCount = Subscription::where('level', $transitionalLevel)
            ->update([
                'level' => 0,
                'is_active' => 0
            ]);

        if ($updatedCount > 0) {
            return back()->with('success', "Успешно отменено {$updatedCount} транзитных подписок.");
        }

        return back()->with('info', 'Не найдено активных транзитных подписок для отмены.');
    }
} 