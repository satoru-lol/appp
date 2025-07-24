<?php

// Простой скрипт для очистки кэша маршрутов
$cacheFiles = [
    'bootstrap/cache/routes-v7.php',
    'bootstrap/cache/config.php',
    'bootstrap/cache/services.php',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Удален файл кэша: $file\n";
    }
}

// Очистка кэша в storage
$storageCacheDirs = [
    'storage/framework/cache',
    'storage/framework/views',
    'storage/logs',
];

foreach ($storageCacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "Очищена папка: $dir\n";
    }
}

echo "Кэш очищен!\n";