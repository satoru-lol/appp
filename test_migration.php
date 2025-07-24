<?php

// Простой скрипт для тестирования миграции данных
require_once __DIR__ . '/bootstrap/app.php';

use App\Services\DataMigrationService;
use Illuminate\Support\Facades\DB;

echo "🔄 Testing Data Migration Service\n";
echo "================================\n\n";

try {
    // Проверяем подключение к БД
    echo "🔧 Checking database connection...\n";
    $pdo = app('db')->connection()->getPdo();
    echo "✅ Database connected successfully!\n\n";
    
    // Проверяем количество данных
    echo "📊 Current data counts:\n";
    $counts = [
        'users' => DB::table('users')->count(),
        'blogs' => DB::table('blogs')->count(),
        'videos' => DB::table('content_video')->count(),
        'courses' => DB::table('courses')->count(),
        'clubs' => DB::table('club')->count(),
    ];
    
    foreach ($counts as $type => $count) {
        echo "  {$type}: {$count} records\n";
    }
    echo "\n";
    
    // Создаем сервис миграции
    echo "🔧 Creating DataMigrationService...\n";
    $migrationService = app(DataMigrationService::class);
    echo "✅ Service created successfully!\n\n";
    
    // Тестируем миграцию пользователей
    echo "👤 Testing users migration...\n";
    $result = $migrationService->migrateUsers();
    
    echo "📊 Results:\n";
    echo "  Updated: " . ($result['updated'] ?? 0) . "\n";
    echo "  Errors: " . count($result['errors'] ?? []) . "\n";
    
    if (!empty($result['errors'])) {
        echo "❌ Errors found:\n";
        foreach (array_slice($result['errors'], 0, 3) as $error) {
            echo "  - {$error}\n";
        }
    }
    
    echo "\n🎉 Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n\n";
    
    // Показываем трассировку
    echo "🔍 Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}