<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSubscriptionHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:history 
                            {level : Уровень подписки для проверки, например -1}
                            {--limit=20 : Лимит подписок для отображения}
                            {--order=desc : Порядок сортировки по дате (asc или desc)}
                            {--email= : Фильтр по email пользователя}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет историю создания подписок определенного уровня';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $level = $this->argument('level');
        $limit = $this->option('limit');
        $order = $this->option('order');
        $email = $this->option('email');
        
        $this->info("Проверка истории подписок с уровнем {$level}...");
        
        // Строим запрос
        $query = Subscription::where('level', $level);
        
        // Если указан email, фильтруем по нему
        if ($email) {
            $userIds = User::where('email', 'like', "%{$email}%")->pluck('id')->toArray();
            if (empty($userIds)) {
                $this->error("Пользователи с email '{$email}' не найдены");
                return 1;
            }
            $query->whereIn('user_id', $userIds);
        }
        
        // Считаем общее количество
        $totalCount = $query->count();
        
        if ($totalCount === 0) {
            $this->info("Подписки с уровнем {$level} не найдены");
            return 0;
        }
        
        // Получаем подписки с сортировкой по дате создания
        $subscriptions = $query->orderBy('created_at', $order)
                              ->limit($limit)
                              ->get();
        
        $this->info("Найдено {$totalCount} подписок с уровнем {$level}. Показано: " . count($subscriptions));
        
        // Статистика по датам создания
        $this->info("Статистика по датам создания подписок с уровнем {$level}:");
        
        $dateStats = DB::table('subscriptions')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('level', $level)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
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
        
        // Показываем подробную информацию о подписках
        $this->info("Подробная информация о подписках с уровнем {$level}:");
        
        $headers = ['ID', 'ID пользователя', 'Email', 'Имя', 'Дата создания', 'Дата обновления', 'Активна', 'Срок действия'];
        $rows = [];
        
        foreach ($subscriptions as $subscription) {
            $user = User::find($subscription->user_id);
            
            $rows[] = [
                'id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'email' => $user ? $user->email : 'Пользователь не найден',
                'name' => $user ? ($user->name ?? 'Не указано') : 'Пользователь не найден',
                'created_at' => $subscription->created_at ? $subscription->created_at->format('Y-m-d H:i:s') : 'Не указана',
                'updated_at' => $subscription->updated_at ? $subscription->updated_at->format('Y-m-d H:i:s') : 'Не указана',
                'is_active' => $subscription->is_active ? 'Да' : 'Нет',
                'expired_at' => $subscription->expired_at ? $subscription->expired_at->format('Y-m-d H:i:s') : 'Бессрочно'
            ];
        }
        
        $this->table($headers, $rows);
        
        // Первая и последняя подписка
        if ($totalCount > 0) {
            $firstSubscription = Subscription::where('level', $level)->orderBy('created_at', 'asc')->first();
            $lastSubscription = Subscription::where('level', $level)->orderBy('created_at', 'desc')->first();
            
            $this->info("Первая подписка с уровнем {$level} была создана: " . 
                ($firstSubscription->created_at ? $firstSubscription->created_at->format('Y-m-d H:i:s') : 'Дата не указана'));
            
            $this->info("Последняя подписка с уровнем {$level} была создана: " . 
                ($lastSubscription->created_at ? $lastSubscription->created_at->format('Y-m-d H:i:s') : 'Дата не указана'));
        }
        
        return 0;
    }
} 