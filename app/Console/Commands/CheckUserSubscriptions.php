<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use App\Models\SubscriptionPays;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckUserSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-users {emails?* : Список email-адресов пользователей для проверки} {--all : Проверить всех пользователей с пробными подписками}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка подписок конкретных пользователей по email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emails = $this->argument('emails');
        $checkAll = $this->option('all');

        if (empty($emails) && !$checkAll) {
            $this->error('Необходимо указать хотя бы один email или использовать опцию --all');
            return 1;
        }

        // Получаем уровни из продуктов для проверки
        $validProducts = Product::all();
        $validLevels = $validProducts->pluck('level')->toArray();
        $productsMap = $validProducts->pluck('name', 'level')->toArray();
        
        $users = [];
        
        if ($checkAll) {
            // Проверяем всех пользователей с пробным уровнем
            $trialProduct = Product::where('name', 'Пробная')->first();
            if ($trialProduct) {
                $this->info("Проверка всех пользователей с пробной подпиской (уровень {$trialProduct->level})");
                
                $users = User::whereHas('subscription', function ($query) use ($trialProduct) {
                    $query->where('level', $trialProduct->level);
                })->get();
                
                $this->info("Найдено {$users->count()} пользователей с пробной подпиской");
            } else {
                $this->error("Продукт 'Пробная' не найден");
                return 1;
            }
        } else {
            // Проверяем конкретных пользователей по email
            $emails = array_map('trim', $emails);
            $this->info("Проверка подписок для указанных email-адресов: " . implode(", ", $emails));
            
            $users = User::whereIn('email', $emails)->get();
            
            if ($users->count() == 0) {
                $this->error("Пользователи с указанными email не найдены");
                return 1;
            }
            
            $this->info("Найдено {$users->count()} пользователей из " . count($emails) . " указанных email");
            
            // Проверяем, какие email не найдены
            $foundEmails = $users->pluck('email')->toArray();
            $notFoundEmails = array_diff($emails, $foundEmails);
            
            if (!empty($notFoundEmails)) {
                $this->warn("Не найдены пользователи со следующими email: " . implode(", ", $notFoundEmails));
            }
        }
        
        $headers = ['ID', 'Имя', 'Email', 'Телефон', 'Уровень', 'Название подписки', 'Активна', 'Срок действия', 'Последняя оплата', 'Продукт'];
        $rows = [];
        
        foreach ($users as $user) {
            $subscription = Subscription::where('user_id', $user->id)->first();
            
            // Получаем информацию о последней оплате
            $lastPayment = SubscriptionPays::where('user_id', $user->id)
                ->where('action', 'buy')
                ->orderBy('created_at', 'desc')
                ->first();
                
            $lastPaymentInfo = $lastPayment ? 
                $lastPayment->created_at->format('Y-m-d H:i') . ' (ID: ' . $lastPayment->product_id . ')' : 
                'Нет данных';
                
            // Получаем название продукта
            $levelName = isset($productsMap[$subscription->level ?? -999]) ? 
                $productsMap[$subscription->level] : 
                'Неизвестный уровень (' . ($subscription->level ?? 'null') . ')';
                
            // Проверяем, является ли уровень подписки действительным
            $isValidLevel = in_array($subscription->level ?? null, $validLevels);
            $levelDisplay = $subscription ? ($subscription->level ?? 'null') : 'Нет подписки';
            
            // Если уровень недействительный, добавляем пометку
            if (!$isValidLevel && $subscription) {
                $levelDisplay .= ' (!)';
            }
                
            $rows[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? 'Не указан',
                'level' => $levelDisplay,
                'name' => $levelName,
                'is_active' => $subscription ? ($subscription->is_active ? 'Да' : 'Нет') : 'Нет подписки',
                'expired_at' => $subscription && $subscription->expired_at ? 
                    $subscription->expired_at->format('Y-m-d H:i') : 
                    ($subscription ? 'Бессрочно' : 'Нет подписки'),
                'last_payment' => $lastPaymentInfo,
                'product_id' => $lastPayment ? $lastPayment->product_id : 'Нет данных'
            ];
        }
        
        $this->table($headers, $rows);
        
        // Выводим информацию о проблемных подписках
        $invalidSubscriptions = 0;
        foreach ($users as $user) {
            $subscription = Subscription::where('user_id', $user->id)->first();
            if ($subscription && !in_array($subscription->level, $validLevels)) {
                $invalidSubscriptions++;
            }
        }
        
        if ($invalidSubscriptions > 0) {
            $this->warn("Обнаружено {$invalidSubscriptions} подписок с неизвестными уровнями!");
            
            // Предложение восстановить подписки при необходимости
            if ($this->confirm('Хотите восстановить подписки с неизвестными уровнями?')) {
                $this->call('subscriptions:restore');
            }
        } else {
            $this->info('Все проверенные подписки имеют корректные уровни.');
        }
        
        return 0;
    }
} 