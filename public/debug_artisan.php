<?php
/**
 * Отладочный скрипт для выполнения artisan команд
 * Этот скрипт поможет выявить проблемы с выполнением artisan
 */

// Проверяем различные пути к PHP
$phpPaths = [
    'php',                    // стандартный путь через PATH
    '/usr/bin/php',           // стандартное расположение на Linux
    '/usr/local/bin/php',     // альтернативное расположение на Linux
    '/opt/php/bin/php',       // возможное кастомное расположение
    'C:\\php\\php.exe',       // Windows путь
    PHP_BINARY               // текущий интерпретатор PHP
];

// Путь к artisan
$artisanPath = __DIR__ . '/../artisan';
$relativeArtisanPath = '../artisan';

echo "<h1>Отладка выполнения artisan команд</h1>";

// Информация о PHP
echo "<h2>Информация о PHP</h2>";
echo "<pre>";
echo "PHP_VERSION: " . PHP_VERSION . "\n";
echo "PHP_BINARY: " . PHP_BINARY . "\n";
echo "PHP_OS: " . PHP_OS . "\n";
echo "PHP_SAPI: " . PHP_SAPI . "\n";
echo "PHP_INT_MAX: " . PHP_INT_MAX . "\n";
echo "PHP_EXTENSION_DIR: " . PHP_EXTENSION_DIR . "\n";
echo "</pre>";

// Проверка существования artisan
echo "<h2>Проверка пути к artisan</h2>";
echo "<pre>";
echo "Абсолютный путь: $artisanPath\n";
echo "Относительный путь: $relativeArtisanPath\n";
echo "Файл существует (абсолютный путь): " . (file_exists($artisanPath) ? "Да" : "Нет") . "\n";
echo "Файл существует (относительный путь): " . (file_exists($relativeArtisanPath) ? "Да" : "Нет") . "\n";
echo "Файл доступен для чтения (абсолютный путь): " . (is_readable($artisanPath) ? "Да" : "Нет") . "\n";
echo "Файл доступен для выполнения (абсолютный путь): " . (is_executable($artisanPath) ? "Да" : "Нет") . "\n";
echo "</pre>";

// Вывод содержимого начала файла artisan
echo "<h2>Первые 20 строк файла artisan</h2>";
echo "<pre>";
if (file_exists($artisanPath) && is_readable($artisanPath)) {
    $content = file_get_contents($artisanPath, false, null, 0, 500);
    echo htmlspecialchars($content);
} else {
    echo "Не удалось прочитать файл artisan";
}
echo "</pre>";

// Проверка возможности выполнения команд через shell_exec
echo "<h2>Проверка shell_exec</h2>";
echo "<pre>";
if (function_exists('shell_exec')) {
    echo "shell_exec доступен: Да\n";
    $output = @shell_exec('echo "shell_exec работает"');
    echo "Результат тестовой команды: " . ($output ? $output : "Нет вывода") . "\n";
} else {
    echo "shell_exec доступен: Нет (функция отключена)\n";
}
echo "</pre>";

// Проверка возможности выполнения команд через proc_open
echo "<h2>Проверка proc_open</h2>";
echo "<pre>";
if (function_exists('proc_open')) {
    echo "proc_open доступен: Да\n";
    
    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];
    
    $process = proc_open('echo "proc_open работает"', $descriptorspec, $pipes);
    
    if (is_resource($process)) {
        echo "proc_open успешно запустил процесс\n";
        
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        
        $return_value = proc_close($process);
        
        echo "Вывод: " . ($output ? $output : "Нет вывода") . "\n";
        echo "Ошибки: " . ($error ? $error : "Нет ошибок") . "\n";
        echo "Код возврата: $return_value\n";
    } else {
        echo "Не удалось запустить процесс через proc_open\n";
    }
} else {
    echo "proc_open доступен: Нет (функция отключена)\n";
}
echo "</pre>";

// Пробуем выполнить artisan команду с разными путями PHP
echo "<h2>Попытка выполнения artisan команды с разными путями PHP</h2>";
echo "<pre>";

$testCommand = "list"; // самая простая команда для проверки

foreach ($phpPaths as $phpPath) {
    echo "Тестирование с PHP: $phpPath\n";
    echo "Команда: $phpPath $artisanPath $testCommand\n";
    
    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];
    
    $process = @proc_open("$phpPath $artisanPath $testCommand", $descriptorspec, $pipes);
    
    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        
        proc_close($process);
        
        echo "Результат:\n";
        if ($error) {
            echo "ОШИБКА: " . $error . "\n";
        }
        if ($output) {
            echo "ВЫВОД: " . (strlen($output) > 300 ? substr($output, 0, 300) . "..." : $output) . "\n";
        } else {
            echo "Нет вывода\n";
        }
    } else {
        echo "Не удалось запустить процесс\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Пробуем использовать включение artisan напрямую
echo "<h2>Попытка включить artisan напрямую</h2>";
echo "<pre>";
try {
    // Сохраняем текущую директорию
    $currentDir = getcwd();
    
    // Переходим в корневую директорию проекта
    chdir(__DIR__ . '/..');
    
    // Перехватываем вывод
    ob_start();
    
    // Пытаемся подключить artisan
    include 'artisan';
    
    // Получаем вывод
    $output = ob_get_clean();
    
    // Возвращаемся в исходную директорию
    chdir($currentDir);
    
    echo "Результат включения artisan:\n";
    echo $output ? $output : "Нет вывода";
} catch (Exception $e) {
    echo "Исключение при включении artisan: " . $e->getMessage() . "\n";
    echo "Трассировка:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "Ошибка при включении artisan: " . $e->getMessage() . "\n";
    echo "Трассировка:\n" . $e->getTraceAsString() . "\n";
}
echo "</pre>";

echo "<h2>Решение проблемы</h2>";
echo "<p>На основе результатов выше, попробуйте следующие исправления:</p>";
echo "<ol>";
echo "<li>Убедитесь, что у файла artisan есть права на выполнение (chmod +x artisan)</li>";
echo "<li>Измените первую строку файла artisan на правильный путь к PHP (#!/usr/bin/env php или конкретный путь)</li>";
echo "<li>В admin_console.php измените строку выполнения команды на правильный путь к PHP</li>";
echo "<li>Проверьте, что PHP имеет доступ к выполнению команд через proc_open или shell_exec</li>";
echo "</ol>";

// Создаем простой фикс-скрипт
echo "<h2>Скрипт для исправления artisan</h2>";
echo "<p>Вы можете использовать следующий код для исправления проблемы:</p>";
echo "<pre>";
echo '<?php
// Путь к файлу artisan
$artisanPath = __DIR__ . "/../artisan";

// Проверяем существование файла
if (!file_exists($artisanPath)) {
    die("Файл artisan не найден!");
}

// Читаем содержимое файла
$content = file_get_contents($artisanPath);

// Заменяем первую строку (шебанг) на корректную
$content = preg_replace("#^(#!.*?)$#m", "#!/usr/bin/env php", $content);

// Записываем изменения
file_put_contents($artisanPath, $content);

// Устанавливаем права на выполнение
chmod($artisanPath, 0755);

echo "Файл artisan успешно исправлен!";
?>';
echo "</pre>";

echo "<p><strong>Важно:</strong> После исправления удалите этот файл с сервера!</p>";
?> 