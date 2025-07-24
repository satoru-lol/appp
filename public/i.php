<?php
// Сохраните этот скрипт как run_composer.php в директории /home/k/klimovmedp/public/
// и запустите через браузер

// Установим максимальное время выполнения скрипта
set_time_limit(600); // 10 минут

// Определяем пути
$publicDir = __DIR__;
$rootDir = dirname($publicDir); // Корневая директория проекта (на уровень выше public)

// Начальная информация
echo "<h1>Установка зависимостей через Composer</h1>";
echo "<p>Директория public: " . $publicDir . "</p>";
echo "<p>Корневая директория проекта: " . $rootDir . "</p>";

// Проверяем наличие composer.phar в корне проекта
if (!file_exists($rootDir . '/composer.phar')) {
    echo "<p style='color:red'>Файл composer.phar не найден в корневой директории проекта!</p>";
    
    // Проверяем содержимое корневой директории
    echo "<p>Содержимое корневой директории:</p>";
    $rootContents = scandir($rootDir);
    echo "<pre>" . print_r($rootContents, true) . "</pre>";
    
    exit;
}

// Проверяем наличие composer.json в корне проекта
if (!file_exists($rootDir . '/composer.json')) {
    echo "<p style='color:red'>Файл composer.json не найден в корневой директории проекта!</p>";
    exit;
}

// Устанавливаем переменные окружения для Composer
putenv('COMPOSER_HOME=' . $rootDir . '/.composer');
putenv('COMPOSER_CACHE_DIR=' . $rootDir . '/.composer/cache');

// Создаем директории для Composer
if (!is_dir($rootDir . '/.composer')) {
    mkdir($rootDir . '/.composer', 0755, true);
}
if (!is_dir($rootDir . '/.composer/cache')) {
    mkdir($rootDir . '/.composer/cache', 0755, true);
}

// Функция для выполнения команд Composer
function runComposerExec($rootDir, $command) {
    echo "<h2>Выполнение команды через exec: $command</h2>";
    echo "<pre>";
    
    $cmd = PHP_BINARY . ' ' . escapeshellarg($rootDir . '/composer.phar') . ' ' . $command . ' --no-interaction --no-ansi --no-dev --working-dir=' . escapeshellarg($rootDir) . ' 2>&1';
    
    echo "Выполняемая команда: $cmd\n\n";
    
    exec($cmd, $output, $return_var);
    
    foreach ($output as $line) {
        echo htmlspecialchars($line) . "\n";
        flush();
    }
    
    echo "\nКоманда завершена с кодом: $return_var\n";
    echo "</pre>";
    
    return $return_var;
}

// Информация о PHP
echo "<h2>Информация о PHP</h2>";
echo "<pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP Binary: " . PHP_BINARY . "\n";
echo "PHP Extensions: " . implode(", ", get_loaded_extensions()) . "\n";
echo "</pre>";

// Шаг 1: Проверяем версию Composer
echo "<h2>Проверка версии Composer</h2>";
$composerVersionResult = runComposerExec($rootDir, '--version');

// Шаг 2: Выполняем установку зависимостей
echo "<h2>Установка зависимостей</h2>";
echo "<p>Этот процесс может занять несколько минут. Пожалуйста, не закрывайте страницу.</p>";
$installResult = runComposerExec($rootDir, 'install --no-dev --prefer-dist --optimize-autoloader');

// Шаг 3: Проверяем результат установки
echo "<h2>Проверка результатов установки</h2>";

// Проверяем наличие файла vendor/autoload.php
if (file_exists($rootDir . '/vendor/autoload.php')) {
    echo "<p style='color:green'>Файл vendor/autoload.php успешно создан! Laravel должен работать корректно.</p>";
    
    // Проверяем содержимое директории vendor
    $vendorContents = scandir($rootDir . '/vendor');
    echo "<p>Обнаружено " . (count($vendorContents) - 2) . " элементов в директории vendor.</p>";
} else {
    echo "<p style='color:red'>Файл vendor/autoload.php НЕ найден! Установка зависимостей не была завершена успешно.</p>";
    
    // Проверяем содержимое директории vendor
    if (is_dir($rootDir . '/vendor')) {
        $vendorContents = scandir($rootDir . '/vendor');
        echo "<p>Содержимое директории vendor:</p>";
        echo "<pre>" . print_r($vendorContents, true) . "</pre>";
    } else {
        echo "<p style='color:red'>Директория vendor не найдена!</p>";
    }
}

// Установка правильных прав доступа
echo "<h2>Настройка прав доступа</h2>";

// Для директории storage
if (is_dir($rootDir . '/storage')) {
    echo "<p>Установка прав для директории storage...</p>";
    
    function recursiveChmod($dir, $dirPermissions, $filePermissions) {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;
            
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                chmod($path, $dirPermissions);
                recursiveChmod($path, $dirPermissions, $filePermissions);
            } else {
                chmod($path, $filePermissions);
            }
        }
    }
    
    chmod($rootDir . '/storage', 0775);
    recursiveChmod($rootDir . '/storage', 0775, 0664);
    echo "<p style='color:green'>Права для директории storage установлены.</p>";
}

// Для директории bootstrap/cache
if (is_dir($rootDir . '/bootstrap/cache')) {
    echo "<p>Установка прав для директории bootstrap/cache...</p>";
    chmod($rootDir . '/bootstrap/cache', 0775);
    recursiveChmod($rootDir . '/bootstrap/cache', 0775, 0664);
    echo "<p style='color:green'>Права для директории bootstrap/cache установлены.</p>";
}

echo "<h2>Итоговая проверка</h2>";
if (file_exists($rootDir . '/vendor/autoload.php')) {
    echo "<p style='color:green'>Установка зависимостей успешно завершена!</p>";
    echo "<p>Теперь вы можете перейти на свой сайт и проверить его работу.</p>";
} else {
    echo "<p style='color:red'>Возникли проблемы при установке зависимостей.</p>";
    echo "<p>Пожалуйста, обратитесь в поддержку хостинга или попробуйте другой метод установки.</p>";
}