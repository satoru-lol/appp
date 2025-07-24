<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateSpecificSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:update-specific 
                            {emails?* : Список email-адресов пользователей для обновления} 
                            {--level=1 : Уровень подписки для установки} 
                            {--active=1 : Активность подписки (1 - активна, 0 - неактивна)} 
                            {--dry-run : Только показать, что будет изменено, без фактических изменений}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление подписок конкретных пользователей';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emails = $this->argument('emails');
        $level = $this->option('level');
        $isActive = $this->option('active');
        $isDryRun = $this->option('dry-run');

        if (empty($emails)) {
            $this->error('Необходимо указать хотя бы один email');
            return 1;
        }

        // Проверяем, существует ли указанный уровень подписки
        $validLevels = Product::pluck('level')->toArray();
        if (!in_array($level, $validLevels) && $level != 0) {
            $this->warn("Указанный уровень подписки ($level) не найден в таблице продуктов!");
            
            // Показываем доступные уровни
            $products = Product::all();
            $this->info("Доступные уровни подписок:");
            $headers = ['ID', 'Название', 'Уровень'];
            $rows = [];
            
            foreach ($products as $product) {
                $rows[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'level' => $product->level
                ];
            }
            
            $this->table($headers, $rows);
            
            if (!$this->confirm('Продолжить с указанным уровнем подписки?')) {
                return 1;
            }
        }

        // Получаем информацию о продукте
        $product = Product::where('level', $level)->first();
        $productName = $product ? $product->name : "Уровень $level";

        // Проверяем пользователей по email
        $emails = array_map('trim', $emails);
        $this->info("Обновление подписок для указанных email-адресов: " . implode(", ", $emails));
        
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

        // Показываем текущие подписки
        $headers = ['ID', 'Email', 'Текущий уровень', 'Активна', 'Будет установлен'];
        $rows = [];
        
        foreach ($users as $user) {
            $subscription = Subscription::where('user_id', $user->id)->first();
            
            $currentLevel = $subscription ? $subscription->level : 'Нет подписки';
            $currentActive = $subscription ? ($subscription->is_active ? 'Да' : 'Нет') : 'Нет подписки';
            
            $rows[] = [
                'id' => $user->id,
                'email' => $user->email,
                'current_level' => $currentLevel,
                'is_active' => $currentActive,
                'new_level' => $level . ' (' . $productName . ')'
            ];
        }
        
        $this->table($headers, $rows);
        
        if ($isDryRun) {
            $this->info("Это тестовый запуск. Изменения не будут сохранены.");
            return 0;
        }
        
        if (!$this->confirm('Вы действительно хотите обновить подписки для указанных пользователей?')) {
            $this->info('Операция отменена.');
            return 0;
        }
        
        // Обновляем подписки
        DB::beginTransaction();
        try {
            $updatedCount = 0;
            $createdCount = 0;
            
            foreach ($users as $user) {
                $subscription = Subscription::where('user_id', $user->id)->first();
                
                if ($subscription) {
                    // Обновляем существующую подписку
                    $subscription->level = $level;
                    $subscription->is_active = $isActive;
                    $subscription->expired_at = null; // Без ограничения по времени
                    $subscription->save();
                    $updatedCount++;
                } else {
                    // Создаем новую подписку
                    $subscription = new Subscription();
                    $subscription->user_id = $user->id;
                    $subscription->level = $level;
                    $subscription->is_active = $isActive;
                    $subscription->expired_at = null;
                    $subscription->save();
                    $createdCount++;
                }
            }
            
            DB::commit();
            $this->info("Успешно обновлено: $updatedCount подписок, создано: $createdCount подписок.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Произошла ошибка при обновлении подписок: " . $e->getMessage());
            Log::error("Ошибка обновления подписок: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 