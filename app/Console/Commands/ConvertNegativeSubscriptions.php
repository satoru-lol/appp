<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConvertNegativeSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:convert-negative 
                            {--dry-run : Только показать, что будет изменено, без фактических изменений}
                            {--batch-size=100 : Размер пакета для обработки за один раз}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Преобразует все подписки с уровнем -1 в пробные подписки (уровень 1)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $batchSize = $this->option('batch-size');
        
        // Проверяем, существует ли пробная подписка
        $trialProduct = Product::where('level', 1)->first();
        if (!$trialProduct) {
            $this->error("Продукт с уровнем 1 (пробная подписка) не найден в базе данных!");
            return 1;
        }
        
        $this->info("Поиск подписок с уровнем -1...");
        
        $totalCount = Subscription::where('level', -1)->count();
        
        if ($totalCount === 0) {
            $this->info("Подписки с уровнем -1 не найдены.");
            return 0;
        }
        
        $this->info("Найдено {$totalCount} подписок с уровнем -1.");
        
        if ($isDryRun) {
            $this->info("Это тестовый запуск. Изменения не будут сохранены.");
            
            // Показываем статистику по датам создания
            $this->info("Статистика по датам создания подписок с уровнем -1:");
            
            $dateStats = DB::table('subscriptions')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->where('level', -1)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();
            
            $dateHeaders = ['Дата', 'Количество подписок'];
            $dateRows = [];
            
            foreach ($dateStats as $stat) {
                $dateRows[] = [
                    'date' => $stat->date,
                    'count' => $stat->count
                ];
            }
            
            $this->table($dateHeaders, $dateRows);
            
            return 0;
        }
        
        if (!$this->confirm("Вы действительно хотите преобразовать все {$totalCount} подписок с уровнем -1 в пробные подписки (уровень 1)?")) {
            $this->info('Операция отменена.');
            return 0;
        }
        
        // Обрабатываем подписки пакетами для экономии памяти
        $processedCount = 0;
        $this->output->progressStart($totalCount);
        
        try {
            // Обрабатываем подписки пакетами
            Subscription::where('level', -1)
                ->chunkById($batchSize, function ($subscriptions) use (&$processedCount) {
                    DB::beginTransaction();
                    try {
                        foreach ($subscriptions as $subscription) {
                            $subscription->level = 1;
                            $subscription->is_active = 1;
                            $subscription->save();
                            $processedCount++;
                            $this->output->progressAdvance();
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                });
            
            $this->output->progressFinish();
            $this->info("Успешно преобразовано {$processedCount} подписок из {$totalCount}.");
            
        } catch (\Exception $e) {
            $this->output->progressFinish();
            $this->error("Произошла ошибка при обновлении подписок: " . $e->getMessage());
            Log::error("Ошибка обновления подписок: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 