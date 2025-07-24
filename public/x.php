<?php
/**
 * Скрипт для очистки данных Telescope
 * Этот скрипт можно запускать через браузер или через cron
 * 
 * Использование:
 * clear_telescope.php?days=7 - очистит данные старше 7 дней
 * clear_telescope.php?all=1 - очистит все данные
 */

// Настройки базы данных - поменяйте на ваши
$dbHost = '127.0.0.1';
$dbName = 'klimovmedp';
$dbUser = 'klimovmedp';
$dbPass = 'Y_HDWyQ9Z13XRS8S';

// Получаем параметры из запроса
$days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
$allData = isset($_GET['all']) && $_GET['all'] == 1;

// Устанавливаем заголовки для вывода в браузер
header('Content-Type: text/html; charset=utf-8');

echo '<h1>Очистка данных Telescope</h1>';

try {
    // Подключаемся к базе данных
    $pdo = new PDO("mysql:host={$dbHost};port=3308;dbname={$dbName}", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    echo '<p>Подключение к базе данных установлено.</p>';
    
    // Временно отключаем проверку внешних ключей
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    echo '<p>Проверка внешних ключей отключена.</p>';
    
    if ($allData) {
        // Полная очистка всех данных
        echo '<p>Удаление ВСЕХ данных Telescope...</p>';
        $pdo->exec('TRUNCATE TABLE telescope_entries_tags');
        $pdo->exec('TRUNCATE TABLE telescope_monitoring');
        $pdo->exec('TRUNCATE TABLE telescope_entries');
        echo '<p>Все таблицы очищены.</p>';
    } else {
        // Удаляем данные старше указанного количества дней
        $date = new DateTime();
        $date->modify("-{$days} days");
        $dateStr = $date->format('Y-m-d H:i:s');
        
        echo "<p>Удаление данных старше {$dateStr}...</p>";
        
        // Сначала удаляем связанные записи в telescope_entries_tags
        $stmt = $pdo->prepare("DELETE FROM telescope_entries_tags WHERE entry_uuid IN (SELECT uuid FROM telescope_entries WHERE created_at < ?)");
        $stmt->execute([$dateStr]);
        $deletedTags = $stmt->rowCount();
        
        echo "<p>Удалено {$deletedTags} записей из telescope_entries_tags</p>";
        
        // Затем удаляем записи в telescope_entries
        $stmt = $pdo->prepare("DELETE FROM telescope_entries WHERE created_at < ?");
        $stmt->execute([$dateStr]);
        $deletedEntries = $stmt->rowCount();
        
        echo "<p>Удалено {$deletedEntries} записей из telescope_entries</p>";
    }
    
    // Включаем проверку внешних ключей обратно
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo '<p>Проверка внешних ключей включена.</p>';
    
    // Оптимизируем таблицы
    $pdo->exec('OPTIMIZE TABLE telescope_entries_tags');
    $pdo->exec('OPTIMIZE TABLE telescope_monitoring');
    $pdo->exec('OPTIMIZE TABLE telescope_entries');
    echo '<p>Таблицы оптимизированы.</p>';
    
    echo '<h2 style="color:green">Очистка завершена успешно.</h2>';
    
} catch (Exception $e) {
    echo '<h2 style="color:red">Ошибка: ' . $e->getMessage() . '</h2>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
    
    // Убедимся, что проверка внешних ключей включена обратно
    if (isset($pdo)) {
        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            echo '<p>Проверка внешних ключей восстановлена.</p>';
        } catch (Exception $e2) {
            echo '<p>Не удалось восстановить проверку внешних ключей: ' . $e2->getMessage() . '</p>';
        }
    }
} 