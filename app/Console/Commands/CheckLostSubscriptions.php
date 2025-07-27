<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use Carbon\Carbon;

class CheckLostSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-lost {--months=6}';
    protected $description = 'Показать пользователей без подписки, у которых были покупки подписок за последние N месяцев';

    public function handle()
    {
        $months = (int)$this->option('months');
        $since = Carbon::now()->subMonths($months);

        $this->info("Проверяем пользователей без подписки за последние $months месяцев (с $since)...");

        // Найти всех пользователей без подписки (level = 0, NULL, is_active = 0, или нет подписки), исключая пробную (level = 1)
        $users = User::leftJoin('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->where(function($q) {
                $q->whereNull('subscriptions.level')
                  ->orWhere('subscriptions.level', 0)
                  ->orWhere('subscriptions.is_active', 0)
                  ->orWhereNull('subscriptions.id');
            })
            ->where(function($q) {
                $q->whereNull('subscriptions.level')
                  ->orWhere('subscriptions.level', '!=', 1);
            })
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        $found = 0;
        foreach ($users as $user) {
            $lastPay = \DB::table('subscription_pays')
                ->where('user_id', $user->id)
                ->where(function($q) {
                    $q->where('action', 'buy')->orWhereNull('action');
                })
                ->where('created_at', '>=', $since)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastPay) {
                $found++;
                $this->line("{$user->name} ({$user->email}) — Покупка: {$lastPay->created_at}, product_id: {$lastPay->product_id}");
            }
        }

        $this->info("Всего найдено пользователей без подписки, но с покупками за последние $months месяцев: $found");
    }
} 