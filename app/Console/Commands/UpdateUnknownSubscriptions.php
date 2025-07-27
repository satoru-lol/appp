<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateUnknownSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:fix-unknown {--dry-run : Only show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace unknown subscription levels with trial subscription';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $trialProduct = Product::where('name', 'Пробная')->first();
        
        if (!$trialProduct) {
            $this->error('Пробная подписка не найдена в продуктах!');
            return 1;
        }
        
        $trialLevel = $trialProduct->level;
        $this->info("Уровень пробной подписки: {$trialLevel}");
        
        // Получаем список всех существующих уровней подписок из продуктов
        $validLevels = Product::pluck('level')->toArray();
        $this->info("Допустимые уровни подписок: " . implode(', ', $validLevels));
        
        // Находим подписки с неизвестными уровнями
        $unknownSubscriptions = Subscription::whereNotIn('level', $validLevels)->get();
        $unknownCount = $unknownSubscriptions->count();
        
        $this->info("Найдено {$unknownCount} подписок с неизвестными уровнями.");
        
        if ($unknownCount == 0) {
            $this->info("Нет подписок для обновления.");
            return 0;
        }
        
        // Показываем список уровней неизвестных подписок
        $unknownLevels = $unknownSubscriptions->pluck('level')->unique();
        $this->info("Неизвестные уровни подписок: " . implode(', ', $unknownLevels->toArray()));
        
        // Группировка по уровням для статистики
        $levelCounts = [];
        foreach ($unknownSubscriptions as $sub) {
            if (!isset($levelCounts[$sub->level])) {
                $levelCounts[$sub->level] = 0;
            }
            $levelCounts[$sub->level]++;
        }
        
        foreach ($levelCounts as $level => $count) {
            $this->line("Уровень {$level}: {$count} подписок");
        }
        
        if ($isDryRun) {
            $this->warn("Это тестовый запуск. Изменения не будут сохранены.");
            
            // Показываем первые 10 пользователей для проверки
            $this->info("Примеры пользователей с неизвестными подписками:");
            $this->table(
                ['ID', 'Имя', 'Email', 'Текущий уровень'],
                $unknownSubscriptions->take(10)->map(function ($sub) {
                    $user = User::find($sub->user_id);
                    return [
                        'id' => $sub->user_id,
                        'name' => $user ? $user->name : 'Н/Д',
                        'email' => $user ? $user->email : 'Н/Д',
                        'level' => $sub->level
                    ];
                })->toArray()
            );
            
            return 0;
        }
        
        // Подтверждение от пользователя перед обновлением
        if (!$this->confirm("Вы действительно хотите обновить {$unknownCount} подписок на пробный уровень ({$trialLevel})?")) {
            $this->info('Операция отменена.');
            return 0;
        }
        
        // Обновляем подписки
        DB::beginTransaction();
        try {
            $progressBar = $this->output->createProgressBar($unknownCount);
            $updatedCount = 0;
            
            foreach ($unknownSubscriptions as $subscription) {
                $subscription->level = $trialLevel;
                $subscription->is_active = 1;
                $subscription->test_period = 1;
                $subscription->expired_at = null; // Без ограничения по времени
                $subscription->save();
                
                $updatedCount++;
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            DB::commit();
            $this->info("Успешно обновлено {$updatedCount} подписок на пробный уровень.");
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Произошла ошибка при обновлении подписок: " . $e->getMessage());
            Log::error("Ошибка обновления неизвестных подписок: " . $e->getMessage());
            return 1;
        }
    }
} 