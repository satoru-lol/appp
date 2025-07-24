<?php

namespace App\Services;

use App\Models\Transactions;
use App\Models\User;
use App\Models\SubscriptionPays;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentService
{
    /**
     * Создает транзакцию для пополнения баланса.
     *
     * @param int $userId
     * @param float $amount
     * @param string $gatewayName
     * @return Transactions
     */
    public function createBalanceTransaction(int $userId, float $amount, string $gatewayName): Transactions
    {
        return Transactions::create([
            'user_id' => $userId,
            'sum' => $amount,
            'state' => 'pending',
            'shop' => $gatewayName,
        ]);
    }

    /**
     * Обрабатывает успешный платеж пополнения баланса.
     *
     * @param int $transactionId
     * @return bool
     */
    public function processSuccessfulBalancePayment(int $transactionId): bool
    {
        return DB::transaction(function () use ($transactionId) {
            $transaction = Transactions::lockForUpdate()->find($transactionId);

            if (!$transaction || $transaction->state !== 'pending') {
                Log::warning('Balance transaction not found or already processed.', ['id' => $transactionId]);
                return $transaction && $transaction->state === 'success';
            }

            $user = User::find($transaction->user_id);
            if (!$user) {
                Log::error('User not found for transaction.', ['transaction_id' => $transactionId]);
                return false;
            }

            $user->balance += $transaction->sum;
            $user->save();

            $transaction->state = 'success';
            $transaction->save();

            Log::info('Balance updated successfully.', [
                'user_id' => $user->id,
                'amount' => $transaction->sum,
                'transaction_id' => $transaction->id
            ]);

            return true;
        });
    }

    /**
     * Обрабатывает успешную оплату подписки.
     *
     * @param int $paymentId ID записи в SubscriptionPays
     * @return bool
     */
    public function processSuccessfulSubscriptionPayment(int $paymentId): bool
    {
        // Эту логику можно будет реализовать позже, если потребуется.
        // Сейчас основной фокус на пополнении баланса.
        $subPay = SubscriptionPays::find($paymentId);
        if ($subPay) {
            $subPay->active = 1;
            $subPay->save();
            return true;
        }
        return false;
    }
} 