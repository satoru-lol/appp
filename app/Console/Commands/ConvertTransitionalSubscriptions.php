<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConvertTransitionalSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:convert-transitional 
                            {--dry-run : Только показать, что будет изменено, без фактических изменений}
                            {--email=* : Список email-адресов пользователей для обработки}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Преобразует переходные подписки (уровень 7) в пробные подписки (уровень 1)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $emails = $this->option('email');
        
        // Если передан один email с запятыми, разбиваем его на массив
        if (count($emails) === 1 && strpos($emails[0], ',') !== false) {
            $emails = array_map('trim', explode(',', $emails[0]));
        }
        
        // Проверяем, существует ли пробная подписка
        $trialProduct = Product::where('level', 1)->first();
        if (!$trialProduct) {
            $this->error("Продукт с уровнем 1 (пробная подписка) не найден в базе данных!");
            return 1;
        }
        
        // Строим запрос
        $query = Subscription::where('level', 7);
        
        // Если указаны email-адреса, фильтруем по ним
        if (!empty($emails)) {
            $userIds = User::whereIn('email', $emails)->pluck('id')->toArray();
            if (empty($userIds)) {
                $this->error("Пользователи с указанными email не найдены");
                return 1;
            }
            $query->whereIn('user_id', $userIds);
        }
        
        $totalCount = $query->count();
        
        if ($totalCount === 0) {
            $this->info("Переходные подписки (уровень 7) не найдены.");
            return 0;
        }
        
        $this->info("Найдено {$totalCount} переходных подписок (уровень 7).");
        
        // Получаем подписки с информацией о пользователях
        $subscriptions = $query->get();
        $userIds = $subscriptions->pluck('user_id')->toArray();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');
        
        // Показываем информацию о подписках
        $headers = ['ID', 'Email', 'Имя', 'Фамилия', 'Телефон', 'Текущий уровень', 'Будет установлен'];
        $rows = [];
        
        foreach ($subscriptions as $subscription) {
            $user = $users->get($subscription->user_id);
            if (!$user) continue;
            
            $rows[] = [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name ?? 'Не указано',
                'surname' => $user->surname ?? 'Не указано',
                'phone' => $user->phone ?? 'Не указан',
                'current_level' => '7 (Переходная)',
                'new_level' => '1 (Пробная)'
            ];
        }
        
        $this->table($headers, $rows);
        
        if ($isDryRun) {
            $this->info("Это тестовый запуск. Изменения не будут сохранены.");
            return 0;
        }
        
        if (!$this->confirm("Вы действительно хотите преобразовать {$totalCount} переходных подписок в пробные?")) {
            $this->info('Операция отменена.');
            return 0;
        }
        
        // Обновляем подписки
        DB::beginTransaction();
        try {
            $updatedCount = 0;
            
            foreach ($subscriptions as $subscription) {
                $subscription->level = 1; // Пробная подписка
                $subscription->is_active = 1;
                $subscription->save();
                $updatedCount++;
            }
            
            DB::commit();
            $this->info("Успешно преобразовано {$updatedCount} подписок из {$totalCount}.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Произошла ошибка при обновлении подписок: " . $e->getMessage());
            Log::error("Ошибка обновления подписок: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 