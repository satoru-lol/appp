<?php
/**
 * Скрипт для полной замены файла artisan
 * Этот скрипт создает новый файл artisan с нуля для решения проблемы синтаксиса
 */

// Путь к файлу artisan
$artisanPath = __DIR__ . '/../artisan';

// Проверяем существование директории
$artisanDir = dirname($artisanPath);
if (!is_dir($artisanDir) || !is_writable($artisanDir)) {
    die("Ошибка: Директория $artisanDir не существует или недоступна для записи!");
}

// Создаем резервную копию оригинального файла, если он существует
if (file_exists($artisanPath) && is_readable($artisanPath)) {
    $backupPath = $artisanPath . '.backup.' . time();
    if (copy($artisanPath, $backupPath)) {
        echo "Создана резервная копия оригинального файла: $backupPath<br>";
    } else {
        echo "Предупреждение: Не удалось создать резервную копию оригинального файла.<br>";
    }
}

// Содержимое нового файла artisan
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

// Записываем новый файл
if (file_put_contents($artisanPath, $artisanContent)) {
    echo "Файл artisan успешно создан заново!<br>";
} else {
    echo "Ошибка: Не удалось создать новый файл artisan.<br>";
    exit;
}

// Устанавливаем права на выполнение
if (chmod($artisanPath, 0755)) {
    echo "Права на выполнение файла artisan установлены.<br>";
} else {
    echo "Предупреждение: Не удалось установить права на выполнение файла artisan. Может потребоваться сделать это вручную (chmod +x artisan).<br>";
}

// Тестируем исправленный файл
echo "<h2>Тестирование нового файла artisan</h2>";
echo "<pre>";

// Определяем доступные пути к PHP
$phpPaths = [
    '/usr/bin/php',          // стандартное расположение на Linux
    'php',                   // стандартный путь через PATH
    PHP_BINARY,              // текущий интерпретатор PHP
];

// Выбираем первый доступный путь к PHP
$phpPath = null;
foreach ($phpPaths as $path) {
    if (empty($path)) continue;
    
    $testCmd = "$path -v";
    $output = @shell_exec($testCmd);
    if ($output && strpos($output, 'PHP') !== false) {
        $phpPath = $path;
        echo "Найден рабочий PHP: $phpPath\n";
        echo "Версия: " . trim($output) . "\n\n";
        break;
    }
}

if (!$phpPath) {
    echo "Не удалось найти рабочий путь к PHP. Используем стандартное 'php'.\n";
    $phpPath = 'php';
}

// Пробуем выполнить простую команду
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

// Проверяем через proc_open для получения подробной информации об ошибках
echo "\nПроверка через proc_open:\n";
if (function_exists('proc_open')) {
    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];
    
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
        } else {
            echo "Ошибок не обнаружено.\n";
        }
        
        if ($output) {
            echo "ВЫВОД:\n" . substr($output, 0, 300) . (strlen($output) > 300 ? "..." : "") . "\n";
        } else {
            echo "Нет вывода.\n";
        }
    } else {
        echo "Не удалось запустить процесс через proc_open.\n";
    }
} else {
    echo "Функция proc_open недоступна.\n";
}
echo "</pre>";

// Обновляем путь к PHP в наших скриптах
echo "<h2>Обновление скриптов для использования правильного пути к PHP</h2>";

$scriptsToUpdate = [
    __DIR__ . '/admin_console.php',
    __DIR__ . '/console_api.php'
];

foreach ($scriptsToUpdate as $scriptPath) {
    if (file_exists($scriptPath) && is_writable($scriptPath)) {
        $scriptContent = file_get_contents($scriptPath);
        
        // Заменяем строку с phpPath на новое значение
        $scriptContent = preg_replace(
            '/\$phpPath\s*=\s*[^;]+;/', 
            '$phpPath = ' . var_export($phpPath, true) . ';', 
            $scriptContent
        );
        
        if (file_put_contents($scriptPath, $scriptContent)) {
            echo "Файл " . basename($scriptPath) . " успешно обновлен с PHP-путем: $phpPath<br>";
        } else {
            echo "Ошибка: Не удалось обновить файл " . basename($scriptPath) . ".<br>";
        }
    } else {
        echo "Предупреждение: Файл " . basename($scriptPath) . " не существует или недоступен для записи.<br>";
    }
}

echo "<p><strong>Важно:</strong> После успешного исправления удалите все отладочные и фикс-скрипты с сервера!</p>";
?> 