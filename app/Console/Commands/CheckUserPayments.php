<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use App\Models\SubscriptionPays;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckUserPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-payments {emails?* : Список email-адресов пользователей для проверки} {--days=365 : Количество дней для поиска платежей}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка истории платежей пользователей по email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emails = $this->argument('emails');
        $days = $this->option('days');

        if (empty($emails)) {
            $this->error('Необходимо указать хотя бы один email');
            return 1;
        }

        // Получаем информацию о продуктах
        $products = Product::all();
        $productsMap = $products->pluck('name', 'id')->toArray();
        
        // Проверяем конкретных пользователей по email
        $emails = array_map('trim', $emails);
        $this->info("Проверка платежей для указанных email-адресов: " . implode(", ", $emails));
        
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
        
        // Ищем платежи за указанный период
        $startDate = Carbon::now()->subDays($days);
        $this->info("Поиск платежей с " . $startDate->format('Y-m-d') . " по настоящее время");
        
        $headers = ['ID пользователя', 'Имя', 'Email', 'Телефон', 'Дата платежа', 'Действие', 'ID продукта', 'Название продукта', 'Сумма'];
        $rows = [];
        $userIds = $users->pluck('id')->toArray();
        
        $payments = SubscriptionPays::whereIn('user_id', $userIds)
            ->where('created_at', '>=', $startDate)
            ->orderBy('user_id')
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($payments->isEmpty()) {
            $this->warn("Платежи за указанный период не найдены");
            
            // Проверим, есть ли платежи вообще (без ограничения по дате)
            $allTimePayments = SubscriptionPays::whereIn('user_id', $userIds)
                ->orderBy('user_id')
                ->orderBy('created_at', 'desc')
                ->get();
                
            if ($allTimePayments->isEmpty()) {
                $this->error("Платежи для указанных пользователей не найдены вообще");
                return 1;
            } else {
                $this->info("Найдены платежи за пределами указанного периода. Показываем все платежи:");
                $payments = $allTimePayments;
            }
        }
        
        $this->info("Найдено {$payments->count()} платежей");
        
        foreach ($payments as $payment) {
            $user = $users->firstWhere('id', $payment->user_id);
            
            $productName = isset($productsMap[$payment->product_id]) ? 
                $productsMap[$payment->product_id] : 
                "Неизвестный продукт (ID: {$payment->product_id})";
                
            $rows[] = [
                'user_id' => $payment->user_id,
                'name' => $user ? $user->name : 'Неизвестно',
                'email' => $user ? $user->email : 'Неизвестно',
                'phone' => $user ? ($user->phone ?? 'Не указан') : 'Неизвестно',
                'date' => $payment->created_at->format('Y-m-d H:i:s'),
                'action' => $payment->action,
                'product_id' => $payment->product_id,
                'product_name' => $productName,
                'amount' => $payment->amount ?? 'Не указана'
            ];
        }
        
        $this->table($headers, $rows);
        
        // Группировка пользователей с платежами
        $usersWithPayments = $payments->pluck('user_id')->unique();
        $usersWithoutPayments = $users->whereNotIn('id', $usersWithPayments)->pluck('email')->toArray();
        
        if (!empty($usersWithoutPayments)) {
            $this->warn("Следующие пользователи не имеют платежей: " . implode(", ", $usersWithoutPayments));
        }
        
        // Предложение восстановить подписки
        if ($this->confirm('Хотите восстановить подписки на основе истории платежей?')) {
            $this->call('subscriptions:restore');
        }
        
        return 0;
    }
} 