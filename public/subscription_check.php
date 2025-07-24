<?php
/**
 * Скрипт для проверки подписки текущего пользователя
 * Этот файл безопасен для прямого доступа, так как показывает информацию только для авторизованного пользователя
 */

// Загрузка Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Инициализация приложения для использования моделей
$app->boot();

// Проверка авторизации
if (!auth()->check()) {
    header('HTTP/1.1 401 Unauthorized');
    exit('Необходимо авторизоваться для просмотра информации о подписке');
}

// Получаем текущего пользователя
$user = auth()->user();

// Получаем подписку пользователя
$subscription = \App\Models\Subscription::where('user_id', $user->id)->first();

// Получаем список продуктов для определения названий уровней
$products = \App\Models\Product::all()->pluck('name', 'level')->toArray();

// Получаем историю платежей
$payments = \App\Models\SubscriptionPays::where('user_id', $user->id)
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

// Простая функция для форматирования даты
function formatDate($date) {
    if (!$date) return 'Не указана';
    return $date instanceof \DateTime ? $date->format('d.m.Y H:i') : $date;
}

// Информация о подписке
$subscriptionInfo = [
    'Пользователь' => $user->name . ' (' . $user->email . ')',
    'Телефон' => $user->phone ?? 'Не указан',
    'Уровень подписки' => $subscription ? ($products[$subscription->level] ?? 'Уровень ' . $subscription->level) : 'Нет подписки',
    'Активна' => $subscription ? ($subscription->is_active ? 'Да' : 'Нет') : 'Нет подписки',
    'Срок действия до' => $subscription ? (
        $subscription->expired_at ? formatDate($subscription->expired_at) : 'Бессрочно'
    ) : 'Нет подписки',
    'Тестовый период' => $subscription ? ($subscription->test_period ? 'Да' : 'Нет') : 'Нет подписки'
];

// Определяем формат ответа
$format = $_GET['format'] ?? 'html';

if ($format === 'json') {
    header('Content-Type: application/json');
    echo json_encode([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ],
        'subscription' => $subscription ? [
            'level' => $subscription->level,
            'level_name' => $products[$subscription->level] ?? 'Уровень ' . $subscription->level,
            'is_active' => (bool)$subscription->is_active,
            'expired_at' => $subscription->expired_at ? formatDate($subscription->expired_at) : null,
            'test_period' => (bool)$subscription->test_period,
        ] : null,
        'payments' => $payments->map(function($payment) {
            return [
                'id' => $payment->id,
                'created_at' => formatDate($payment->created_at),
                'product_id' => $payment->product_id,
                'action' => $payment->action,
                'price' => $payment->price,
            ];
        }),
    ]);
    exit;
}

// HTML ответ по умолчанию
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о подписке</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-header {
            font-weight: bold;
            background-color: #f1f3f5;
        }
        .table th {
            width: 30%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Информация о подписке</h1>
        
        <div class="card">
            <div class="card-header">
                Ваша подписка
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        <?php foreach ($subscriptionInfo as $key => $value): ?>
                        <tr>
                            <th><?php echo htmlspecialchars($key); ?></th>
                            <td><?php echo htmlspecialchars($value); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($payments && $payments->count() > 0): ?>
        <div class="card">
            <div class="card-header">
                История платежей
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Продукт</th>
                            <th>Действие</th>
                            <th>Цена</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(formatDate($payment->created_at)); ?></td>
                            <td><?php 
                                $product = \App\Models\Product::find($payment->product_id);
                                echo $product ? htmlspecialchars($product->name) : 'ID: ' . htmlspecialchars($payment->product_id);
                            ?></td>
                            <td><?php echo htmlspecialchars($payment->action); ?></td>
                            <td><?php echo htmlspecialchars($payment->price ?? 'Не указана'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 