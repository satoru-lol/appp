<?php
/**
 * Скрипт для исправления файла artisan
 * Этот скрипт исправляет синтаксические ошибки в файле artisan
 */

// Путь к файлу artisan
$artisanPath = __DIR__ . '/../artisan';

// Проверяем существование файла
if (!file_exists($artisanPath)) {
    die("Ошибка: Файл artisan не найден по пути $artisanPath!");
}

// Проверяем, можно ли прочитать файл
if (!is_readable($artisanPath)) {
    die("Ошибка: Файл artisan не доступен для чтения!");
}

// Проверяем, можно ли записать в файл
if (!is_writable($artisanPath)) {
    die("Ошибка: Файл artisan не доступен для записи!");
}

// Читаем содержимое файла
$content = file_get_contents($artisanPath);
$originalContent = $content; // Сохраняем оригинальное содержимое для сравнения

// Обнаружение и исправление синтаксической ошибки в строке 4
// Заменяем прямое использование имени класса на строку с 'use' в начале
if (preg_match('/^use Symfony\\\\Component\\\\Console\\\\Input\\\\ArgvInput;$/m', $content)) {
    echo "Директива 'use' уже правильно определена.<br>";
} else {
    // Исправляем ошибку с директивой use
    $content = preg_replace('/^(.*?)use (.*?)$/m', 'use $2', $content);
    // Если не нашли и не заменили, добавляем правильную директиву после <?php
    if ($content === $originalContent) {
        $content = preg_replace('/^<\?php\s+/s', "<?php\n\nuse Symfony\\Component\\Console\\Input\\ArgvInput;\n\n", $content);
    }
}

// Исправляем первую строку (шебанг)
$content = preg_replace('/^#!.*$/m', '#!/usr/bin/env php', $content);

// Проверяем, были ли изменения
if ($content === $originalContent) {
    echo "Предупреждение: Не было сделано никаких изменений в файле artisan.<br>";
} else {
    // Записываем изменения
    if (file_put_contents($artisanPath, $content)) {
        echo "Файл artisan успешно исправлен!<br>";
    } else {
        echo "Ошибка: Не удалось записать изменения в файл artisan.<br>";
    }
}

// Устанавливаем права на выполнение
if (chmod($artisanPath, 0755)) {
    echo "Права на выполнение файла artisan установлены.<br>";
} else {
    echo "Предупреждение: Не удалось установить права на выполнение файла artisan. Может потребоваться сделать это вручную (chmod +x artisan).<br>";
}

// Создаем резервную копию оригинального файла
$backupPath = $artisanPath . '.backup.' . time();
if (file_put_contents($backupPath, $originalContent)) {
    echo "Создана резервная копия оригинального файла: $backupPath<br>";
} else {
    echo "Предупреждение: Не удалось создать резервную копию оригинального файла.<br>";
}

// Тестируем исправленный файл
echo "<h2>Тестирование исправленного файла</h2>";
echo "<pre>";

// Проверяем доступность proc_open
if (function_exists('proc_open')) {
    // Пробуем выполнить простую команду
    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];
    
    $phpBinary = PHP_BINARY;
    $command = "$phpBinary $artisanPath list";
    
    echo "Выполнение команды: $command\n";
    
    $process = @proc_open($command, $descriptorspec, $pipes);
    
    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        
        proc_close($process);
        
        if ($error) {
            echo "ОШИБКА:\n$error\n";
        }
        
        if ($output) {
            echo "РЕЗУЛЬТАТ:\n" . substr($output, 0, 300) . (strlen($output) > 300 ? "..." : "") . "\n";
            echo "Команда успешно выполнена!\n";
        } else {
            echo "Команда выполнена, но результат пустой.\n";
        }
    } else {
        echo "Не удалось запустить процесс через proc_open.\n";
    }
} else {
    echo "Функция proc_open недоступна, невозможно протестировать выполнение команды.\n";
}

echo "</pre>";

// Обновляем admin_console.php для использования правильного пути к PHP
$adminConsolePath = __DIR__ . '/admin_console.php';
if (file_exists($adminConsolePath) && is_writable($adminConsolePath)) {
    $adminConsoleContent = file_get_contents($adminConsolePath);
    
    // Заменяем строку с командой PHP на более надежную
    $adminConsoleContent = preg_replace(
        '/\$commandString = \'php \' \. escapeshellarg\(\$artisanPath\) \. \' \' \. escapeshellarg\(\$command\);/',
        '$commandString = \'' . PHP_BINARY . ' \' . escapeshellarg($artisanPath) . \' \' . escapeshellarg($command);',
        $adminConsoleContent
    );
    
    if (file_put_contents($adminConsolePath, $adminConsoleContent)) {
        echo "Файл admin_console.php успешно обновлен с правильным путем к PHP.<br>";
    } else {
        echo "Ошибка: Не удалось обновить файл admin_console.php.<br>";
    }
} else {
    echo "Предупреждение: Файл admin_console.php не существует или недоступен для записи.<br>";
}

echo "<p><strong>Важно:</strong> После успешного исправления удалите этот файл и debug_artisan.php с сервера!</p>";
?> 