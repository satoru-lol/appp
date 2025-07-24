<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixUnknownSubscriptionLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:fix-unknown-levels 
                            {--dry-run : Только показать, что будет изменено, без фактических изменений}
                            {--level=1 : Уровень подписки для установки вместо неизвестных}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Исправляет неизвестные уровни подписок (-1) на пробные подписки';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $newLevel = $this->option('level');
        
        // Проверяем, существует ли указанный уровень подписки
        $product = Product::where('level', $newLevel)->first();
        if (!$product) {
            $this->error("Продукт с уровнем {$newLevel} не найден в базе данных!");
            
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
            return 1;
        }
        
        $this->info("Поиск подписок с неизвестным уровнем (-1)...");
        
        // Находим все подписки с уровнем -1
        $unknownSubscriptions = Subscription::where('level', -1)->get();
        $count = $unknownSubscriptions->count();
        
        if ($count === 0) {
            $this->info("Подписки с уровнем -1 не найдены.");
            return 0;
        }
        
        $this->info("Найдено {$count} подписок с уровнем -1.");
        
        // Показываем пользователей с такими подписками
        $userIds = $unknownSubscriptions->pluck('user_id')->toArray();
        $users = User::whereIn('id', $userIds)->get();
        
        $headers = ['ID', 'Имя', 'Email', 'Телефон', 'Текущий уровень', 'Будет установлен'];
        $rows = [];
        
        foreach ($users as $user) {
            $subscription = $unknownSubscriptions->where('user_id', $user->id)->first();
            
            $rows[] = [
                'id' => $user->id,
                'name' => $user->name ?? 'Не указано',
                'email' => $user->email,
                'phone' => $user->phone ?? 'Не указан',
                'current_level' => $subscription->level,
                'new_level' => $newLevel . ' (' . $product->name . ')'
            ];
        }
        
        $this->table($headers, $rows);
        
        if ($isDryRun) {
            $this->info("Это тестовый запуск. Изменения не будут сохранены.");
            return 0;
        }
        
        if (!$this->confirm('Вы действительно хотите изменить уровень этих подписок на ' . $newLevel . ' (' . $product->name . ')?')) {
            $this->info('Операция отменена.');
            return 0;
        }
        
        // Обновляем подписки
        DB::beginTransaction();
        try {
            $updated = 0;
            
            foreach ($unknownSubscriptions as $subscription) {
                $subscription->level = $newLevel;
                $subscription->is_active = 1;
                $subscription->save();
                $updated++;
            }
            
            DB::commit();
            $this->info("Успешно обновлено {$updated} подписок.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Произошла ошибка при обновлении подписок: " . $e->getMessage());
            Log::error("Ошибка обновления подписок: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 