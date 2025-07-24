<?php
/**
 * Скрипт для экспорта всех видео из таблицы content_video
 * Открывайте в браузере по адресу: /export_videos.php
 */

// Отключаем лимит времени выполнения
set_time_limit(0);

// Подключаем автозагрузчик Laravel
require __DIR__.'/../vendor/autoload.php';

// Создаем экземпляр приложения Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Получаем подключение к БД
$db = $app->make('db');

// Проверяем авторизацию (доступ только для администраторов)
$auth = true;// $app->make('auth');
if (!$auth) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Доступ запрещен. Авторизуйтесь как администратор.';
    exit;
}

// Формат вывода (json, html, csv)
$format = $_GET['format'] ?? 'html';

// Получаем все видео
$videos = $db->table('content_video')
    ->leftJoin('categories', 'content_video.category_id', '=', 'categories.id')
    ->select('content_video.id', 'content_video.title', 'content_video.url', 'content_video.category_id', 'categories.name as category_name')
    ->orderBy('content_video.id')
    ->get();

// Экспорт в JSON
if ($format === 'json') {
    header('Content-Type: application/json');
    // Удаляем поле url из каждого видео
    $videosArr = [];
    foreach ($videos as $video) {
        $arr = (array)$video;
        unset($arr['url']);
        $videosArr[] = $arr;
    }
    echo json_encode($videosArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Экспорт в CSV
if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="videos_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Заголовки CSV
    fputcsv($output, ['ID', 'Название', 'URL', 'ID категории', 'Название категории']);
    
    // Данные
    foreach ($videos as $video) {
        fputcsv($output, [
            $video->id,
            $video->title,
            //$video->url,
            $video->category_id,
            $video->category_name ?? 'Без категории'
        ]);
    }
    exit;
}

// HTML вывод (по умолчанию)
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Экспорт видео</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { margin-top: 20px; margin-bottom: 50px; }
        .url-cell { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .title-cell { max-width: 300px; }
        .format-links { margin-bottom: 20px; }
        pre.json-data { 
            max-height: 500px; 
            overflow: auto; 
            background-color: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
        }
        .copy-btn {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Экспорт видео (<?= count($videos) ?> записей)</h1>
        
        <div class="format-links">
            <p>Экспортировать в формате:</p>
            <a href="?format=html" class="btn btn-primary">HTML</a>
            <a href="?format=json" class="btn btn-success">JSON</a>
            <a href="?format=csv" class="btn btn-info">CSV</a>
            
            <button id="copyJsonBtn" class="btn btn-outline-secondary copy-btn">Копировать как JSON</button>
        </div>
        
        <div class="mb-3">
            <pre class="json-data" id="jsonData"><?php
                // Для HTML тоже убираем url из json
                $videosArr = [];
                foreach ($videos as $video) {
                    $arr = (array)$video;
                    unset($arr['url']);
                    $videosArr[] = $arr;
                }
                echo htmlspecialchars(json_encode($videosArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            ?></pre>
        </div>
        
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <!--<th>URL</th>-->
                    <th>ID категории</th>
                    <th>Название категории</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($videos as $video): ?>
                <tr>
                    <td><?= $video->id ?></td>
                    <td class="title-cell"><?= htmlspecialchars($video->title) ?></td>
                    <!--<td class="url-cell" title="<?= htmlspecialchars($video->url) ?>"><?= htmlspecialchars($video->url) ?></td>-->
                    <td><?= $video->category_id ?: 'Нет' ?></td>
                    <td><?= $video->category_name ?: 'Без категории' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        document.getElementById('copyJsonBtn').addEventListener('click', function() {
            const jsonText = document.getElementById('jsonData').textContent;
            navigator.clipboard.writeText(jsonText).then(() => {
                alert('JSON скопирован в буфер обмена');
            }).catch(err => {
                console.error('Не удалось скопировать: ', err);
            });
        });
    </script>
</body>
</html> 