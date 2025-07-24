<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Check and deactivate expired subscriptions';

    public function handle()
    {
        $now = Carbon::now();

        $expired = Subscription::whereNotNull('expired_at')
            ->where('expired_at', '<', $now)
            ->get();

        foreach ($expired as $subscription) {
            // Пропускаем все пробные/бесплатные подписки (уровни 1, -1 и т.д.)
            if ($subscription->level <= 1 && $subscription->level != 0) {
                // Просто деактивируем, не меняя уровень
                $subscription->expired_at = null; 
                $subscription->save();
                continue;
            }
            
            $product = Product::where('level', $subscription->level)->first();
            $user = User::find($subscription->user_id);
            
            if ($user && $product && $user->balance >= $product->price && $subscription->auto && $subscription->level != -1) {
                $user->balance -= $product->price;
                $user->save();
                $subscription->expired_at = Carbon::now()->addDays(30);
                $subscription->save();
            } else {
                $subscription->level = 0;
                $subscription->expired_at = null;
                $subscription->save();
            }
        }

        // $this->info("Обработано: {$expired->count()} подписок.");
    
    }
}
