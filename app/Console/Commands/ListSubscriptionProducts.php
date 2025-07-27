<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListSubscriptionProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:list-products {--stats : Показать статистику по количеству подписок}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отображает список всех продуктов и их уровней подписок';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $showStats = $this->option('stats');
        
        $this->info("Список всех продуктов и их уровней подписок:");
        
        $products = Product::orderBy('level')->get();
        
        if ($products->isEmpty()) {
            $this->error("Продукты не найдены в базе данных.");
            return 1;
        }
        
        $headers = ['ID', 'Название', 'Уровень', 'Цена', 'Описание'];
        
        if ($showStats) {
            $headers[] = 'Кол-во подписок';
        }
        
        $rows = [];
        
        foreach ($products as $product) {
            $row = [
                'id' => $product->id,
                'name' => $product->name,
                'level' => $product->level,
                'price' => $product->price ?? 'Не указана',
                'description' => $product->description ?? 'Нет описания'
            ];
            
            if ($showStats) {
                $count = Subscription::where('level', $product->level)->count();
                $row['count'] = $count;
            }
            
            $rows[] = $row;
        }
        
        $this->table($headers, $rows);
        
        // Показываем статистику по уровням подписок, которых нет в продуктах
        if ($showStats) {
            $this->info("Статистика по уровням подписок, которых нет в продуктах:");
            
            $productLevels = $products->pluck('level')->toArray();
            
            $unknownLevels = DB::table('subscriptions')
                ->select('level', DB::raw('count(*) as count'))
                ->whereNotIn('level', $productLevels)
                ->groupBy('level')
                ->orderBy('level')
                ->get();
            
            if ($unknownLevels->isEmpty()) {
                $this->info("Все подписки имеют корректные уровни, соответствующие продуктам.");
            } else {
                $unknownHeaders = ['Уровень', 'Количество подписок'];
                $unknownRows = [];
                
                foreach ($unknownLevels as $level) {
                    $unknownRows[] = [
                        'level' => $level->level,
                        'count' => $level->count
                    ];
                }
                
                $this->table($unknownHeaders, $unknownRows);
            }
        }
        
        return 0;
    }
} 