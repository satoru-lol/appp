<?php
/**
 * API для выполнения artisan команд
 * Внимание! Этот файл должен быть защищен от несанкционированного доступа!
 */

// Проверка авторизации по API ключу
$apiKey = $_GET['api_key'] ?? $_POST['api_key'] ?? '';
$validApiKeyHash = '21232f297a57a5a743894a0e4a801fc3'; // MD5 хеш для "admin"

if (md5($apiKey) !== $validApiKeyHash) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}

// Проверка наличия команды
if (!isset($_POST['command'])) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Command is required']);
    exit;
}

// Разрешенные команды для API
$allowedCommands = [
    'subscriptions:list-products',
    'subscriptions:check-users',
    'subscriptions:check-payments',
    'subscriptions:fix-unknown',
    'subscriptions:restore',
    'subscriptions:check-history'
];

$command = $_POST['command'];

// Проверка на разрешенную команду
if (!in_array($command, $allowedCommands)) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Command not allowed']);
    exit;
}

// Получение аргументов и опций
$args = isset($_POST['args']) ? (array)$_POST['args'] : [];
$options = isset($_POST['options']) ? (array)$_POST['options'] : [];
$input = isset($_POST['input']) ? $_POST['input'] : null; // Новый параметр для пользовательского ввода

// Используем явный путь к PHP
$phpPath = '/usr/bin/php';
if (!file_exists($phpPath)) {
    $phpPath = 'php';
}

// Определяем рабочую директорию проекта (на уровень выше public)
$projectDir = dirname(__DIR__);

// Формирование строки команды - меняем подход
$commandString = 'cd ' . escapeshellarg($projectDir) . ' && ' . 
                 $phpPath . ' artisan ' . escapeshellarg($command);

// Добавление аргументов
foreach ($args as $arg) {
    if (!empty($arg)) {
        // Для аргументов с запятыми разбиваем их и добавляем как отдельные аргументы
        if (strpos($arg, ',') !== false) {
            $argArray = explode(',', $arg);
            foreach ($argArray as $singleArg) {
                $commandString .= ' ' . escapeshellarg(trim($singleArg));
            }
        } else {
            $commandString .= ' ' . escapeshellarg($arg);
        }
    }
}

// Добавление опций
foreach ($options as $option) {
    if (!empty($option)) {
        $commandString .= ' ' . escapeshellarg($option);
    }
}

// Выполнение команды и захват вывода
$descriptorspec = [
    0 => ["pipe", "r"],  // stdin
    1 => ["pipe", "w"],  // stdout
    2 => ["pipe", "w"]   // stderr
];

$process = proc_open($commandString, $descriptorspec, $pipes);

if (!is_resource($process)) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to execute command']);
    exit;
}

// Если есть пользовательский ввод, отправляем его в stdin
if ($input !== null) {
    fwrite($pipes[0], $input . "\n");
}

// Читаем вывод построчно, чтобы поймать интерактивный вопрос
$output = '';
$error = '';
$needInput = false;
$inputPrompt = '';

stream_set_blocking($pipes[1], false);
stream_set_blocking($pipes[2], false);

$start = time();
$timeout = 10; // секунд

while (true) {
    $out = fgets($pipes[1]);
    $err = fgets($pipes[2]);
    if ($out !== false) $output .= $out;
    if ($err !== false) $error .= $err;

    // Проверяем на наличие интерактивного вопроса
    if (preg_match('/Do you wish to continue\? \(yes\/no\) \[no\]:/i', $output, $matches)) {
        $needInput = true;
        $inputPrompt = $matches[0];
        break;
    }
    // Можно добавить другие паттерны для интерактивных вопросов

    // Если процесс завершился
    $status = proc_get_status($process);
    if (!$status['running']) {
        break;
    }
    // Таймаут
    if ((time() - $start) > $timeout) {
        break;
    }
    usleep(100000); // 0.1 сек
}

// Закрываем stdin
fclose($pipes[0]);
// Получаем остаток вывода
$output .= stream_get_contents($pipes[1]);
$error .= stream_get_contents($pipes[2]);
// Закрываем pipes
fclose($pipes[1]);
fclose($pipes[2]);
// Закрываем процесс
$exitCode = proc_close($process);

// Если требуется ввод пользователя, возвращаем специальный статус
if ($needInput) {
    header('Content-Type: application/json');
    echo json_encode([
        'need_input' => true,
        'prompt' => $inputPrompt,
        'output' => $output,
        'error' => $error
    ]);
    exit;
}

// Определяем формат ответа
$wantsJson = isset($_GET['format']) && $_GET['format'] === 'json';
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($wantsJson || $isAjax) {
    // Возвращаем JSON ответ
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $exitCode === 0,
        'exit_code' => $exitCode,
        'output' => $output,
        'error' => $error
    ]);
} else {
    // Возвращаем текстовый ответ
    header('Content-Type: text/plain');
    if (!empty($error)) {
        echo "ERROR (code: $exitCode):\n$error\n\n";
    }
    echo $output;
}