<?php
/**
 * Скрипт для исправления проблем с кодировкой в файле artisan
 * Проблема может быть в невидимых BOM-символах или других символах, которые нарушают синтаксис
 */

// Путь к файлу artisan
$artisanPath = __DIR__ . '/../artisan';

// Проверяем существование файла
if (!file_exists($artisanPath)) {
    die("Ошибка: Файл artisan не найден по пути $artisanPath!");
}

// Создаем резервную копию оригинального файла
$backupPath = $artisanPath . '.backup.' . time();
if (copy($artisanPath, $backupPath)) {
    echo "Создана резервная копия оригинального файла: $backupPath<br>";
} else {
    echo "Предупреждение: Не удалось создать резервную копию оригинального файла.<br>";
}

// Читаем содержимое файла в бинарном режиме для обнаружения BOM и других проблем
$content = file_get_contents($artisanPath);

// Проверяем и удаляем BOM (Byte Order Mark) если он есть
$bom = chr(239) . chr(187) . chr(191); // UTF-8 BOM
if (substr($content, 0, 3) === $bom) {
    echo "Обнаружен BOM (Byte Order Mark) в начале файла! Удаляем...<br>";
    $content = substr($content, 3);
}

// Заменяем все возможные проблемные символы в конкретной проблемной строке
$lines = explode("\n", $content);
$problemFound = false;

if (count($lines) >= 4) {
    $line4 = $lines[3]; // 4-я строка (индекс 3)
    
    echo "Проблемная строка (строка 4): " . htmlspecialchars($line4) . "<br>";
    echo "Hex представление: " . bin2hex($line4) . "<br>";
    
    // Чистим строку от всех невидимых символов и пересоздаем её
    if (strpos($line4, 'use Symfony') !== false) {
        $cleanLine = 'use Symfony\Component\Console\Input\ArgvInput;';
        $lines[3] = $cleanLine;
        $problemFound = true;
        echo "Строка заменена на чистую версию.<br>";
    }
}

// Если проблема найдена, собираем файл обратно и сохраняем
if ($problemFound) {
    $newContent = implode("\n", $lines);
    
    // Записываем исправленный файл
    if (file_put_contents($artisanPath, $newContent)) {
        echo "Файл artisan успешно исправлен!<br>";
    } else {
        echo "Ошибка: Не удалось записать исправленный файл artisan.<br>";
    }
} else {
    echo "Проблема не найдена в строке 4. Пробуем полную перезапись файла...<br>";
    
    // Полностью перезаписываем файл с правильной кодировкой
    $artisanContent = <<<'EOD'
#!/usr/bin/env php
<?php

use Symfony\Component\Console\Input\ArgvInput;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the command...
$status = (require_once __DIR__.'/bootstrap/app.php')
    ->handleCommand(new ArgvInput);

exit($status);
EOD;

    if (file_put_contents($artisanPath, $artisanContent)) {
        echo "Файл artisan успешно перезаписан с корректной кодировкой!<br>";
    } else {
        echo "Ошибка: Не удалось перезаписать файл artisan.<br>";
    }
}

// Устанавливаем права на выполнение
if (chmod($artisanPath, 0755)) {
    echo "Права на выполнение файла artisan установлены.<br>";
} else {
    echo "Предупреждение: Не удалось установить права на выполнение файла artisan.<br>";
}

// Тестируем исправленный файл
echo "<h2>Тестирование исправленного файла</h2>";
echo "<pre>";

// Находим правильный путь к PHP
$phpPath = '/usr/bin/php';
if (!file_exists($phpPath)) {
    $phpPath = 'php';
}

// Выполняем простую команду
$command = "$phpPath $artisanPath list";
echo "Выполнение команды: $command\n";

$output = @shell_exec($command . " 2>&1");
if ($output) {
    if (strpos($output, 'error') !== false || strpos($output, 'Error') !== false || strpos($output, 'ERROR') !== false) {
        echo "ОШИБКА при выполнении команды:\n$output\n";
    } else {
        echo "РЕЗУЛЬТАТ:\n" . substr($output, 0, 300) . (strlen($output) > 300 ? "..." : "") . "\n";
        echo "Команда успешно выполнена!\n";
    }
} else {
    echo "Команда выполнена, но результат пустой.\n";
}

echo "</pre>";

echo "<p><strong>Важно:</strong> После успешного исправления удалите этот файл с сервера!</p>";
?> 