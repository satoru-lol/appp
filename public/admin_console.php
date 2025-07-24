<?php
/**
 * Веб-интерфейс для выполнения artisan команд
 * Внимание! Этот файл должен быть защищен от несанкционированного доступа!
 */

// Проверка авторизации (настоятельно рекомендуется использовать более надежный метод авторизации)
session_start();
$isAuthorized = false;

// Простая проверка на админа - в реальном проекте должна быть замена на более надежный метод
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    $isAuthorized = true;
}

// Авторизация через пароль
if (isset($_POST['password'])) {
    // MD5 хеш пароля (по умолчанию хеш для пароля "admin123")
    $passwordHash = '0192023a7bbd73250516f069df18b500'; // MD5 хеш для "admin123"
    if (md5($_POST['password']) === $passwordHash) {
        $_SESSION['is_admin'] = true;
        $isAuthorized = true;
    }
}

// Путь к artisan
$artisanPath = __DIR__ . '/../artisan';

// Доступные команды для подписок
$availableCommands = [
    'subscriptions:list-products' => [
        'title' => 'Список продуктов подписки',
        'options' => [
            '--stats' => 'Показать статистику по количеству подписок'
        ]
    ],
    'subscriptions:check-users' => [
        'title' => 'Проверка подписок пользователей',
        'args' => [
            'emails' => 'Список email-адресов через запятую'
        ],
        'options' => [
            '--all' => 'Проверить всех пользователей с пробными подписками'
        ]
    ],
    'subscriptions:check-payments' => [
        'title' => 'Проверка платежей пользователей',
        'args' => [
            'emails' => 'Список email-адресов через запятую'
        ],
        'options' => [
            '--days' => 'Количество дней для поиска платежей (по умолчанию 365)'
        ]
    ],
    'subscriptions:fix-unknown' => [
        'title' => 'Исправление неизвестных уровней подписок',
        'options' => [
            '--dry-run' => 'Только показать что будет обновлено без внесения изменений'
        ]
    ],
    'subscriptions:restore' => [
        'title' => 'Восстановление подписок',
        'options' => [
            '--revert' => 'Откатить последнее восстановление из резервной копии',
            '--force' => 'Выполнить без запроса подтверждения'
        ]
    ],
    'subscriptions:check-history' => [
        'title' => 'Проверка истории подписок',
        'args' => [
            'level' => 'Уровень подписки для проверки'
        ],
        'options' => [
            '--limit' => 'Ограничение количества записей (по умолчанию 100)',
            '--order' => 'Порядок сортировки (asc/desc, по умолчанию desc)',
            '--email' => 'Фильтр по email пользователя'
        ]
    ]
];

// Результат выполнения команды
$commandOutput = '';
$commandError = '';

// Обработка AJAX запроса на выполнение команды
if ($isAuthorized && isset($_POST['command'])) {
    $command = $_POST['command'];
    $args = isset($_POST['args']) ? $_POST['args'] : [];
    $options = isset($_POST['options']) ? $_POST['options'] : [];
    
    // Используем явный путь к PHP
    $phpPath = '/usr/bin/php8.3';
    if (!file_exists($phpPath)) {
        $phpPath = 'php8.3';
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
    
    if (is_resource($process)) {
        // Закрываем stdin
        fclose($pipes[0]);
        
        // Получаем вывод
        $commandOutput = stream_get_contents($pipes[1]);
        $commandError = stream_get_contents($pipes[2]);
        
        // Закрываем pipes
        fclose($pipes[1]);
        fclose($pipes[2]);
        
        // Закрываем процесс
        proc_close($process);
        
        // Если это AJAX запрос, возвращаем JSON
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => empty($commandError),
                'output' => nl2br(htmlspecialchars($commandOutput)),
                'error' => nl2br(htmlspecialchars($commandError))
            ]);
            exit;
        }
    }
}

// Если нужен только выход из системы
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление подписками</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .command-form {
            margin-bottom: 20px;
        }
        #output {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
            white-space: pre-wrap;
            max-height: 600px;
            overflow-y: auto;
        }
        .error {
            color: #dc3545;
        }
        .loading {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 0.2em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
            margin-right: 10px;
        }
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Веб-консоль управления подписками</h1>
        
        <?php if (!$isAuthorized): ?>
        <!-- Форма авторизации -->
        <div class="row mt-4">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        Авторизация
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="password" class="form-label">Пароль администратора</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Войти</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        
        <div class="row mb-3">
            <div class="col-md-10">
                <p class="text-muted">Выберите команду и заполните необходимые параметры</p>
            </div>
            <div class="col-md-2 text-end">
                <a href="?logout=1" class="btn btn-sm btn-outline-secondary">Выйти</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Доступные команды
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($availableCommands as $cmd => $details): ?>
                            <a href="#" class="list-group-item list-group-item-action command-link" data-command="<?php echo htmlspecialchars($cmd); ?>">
                                <?php echo htmlspecialchars($details['title']); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Параметры команды <span id="command-title"></span>
                    </div>
                    <div class="card-body">
                        <form id="command-form" class="command-form">
                            <input type="hidden" id="command" name="command">
                            
                            <div id="args-container">
                                <!-- Здесь будут динамически добавлены поля для аргументов -->
                            </div>
                            
                            <div id="options-container">
                                <!-- Здесь будут динамически добавлены поля для опций -->
                            </div>
                            
                            <button type="submit" class="btn btn-primary mt-3">Выполнить</button>
                        </form>
                        
                        <div id="loading" class="d-none mt-3">
                            <span class="loading"></span> Выполнение команды...
                        </div>
                        
                        <div id="output" class="d-none"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if ($isAuthorized): ?>
    <script>
        $(document).ready(function() {
            // Данные о доступных командах
            const availableCommands = <?php echo json_encode($availableCommands); ?>;
            
            // Обработчик клика по команде
            $('.command-link').on('click', function(e) {
                e.preventDefault();
                
                const command = $(this).data('command');
                const details = availableCommands[command];
                
                // Устанавливаем выбранную команду
                $('#command').val(command);
                $('#command-title').text(command);
                
                // Очищаем контейнеры аргументов и опций
                $('#args-container').empty();
                $('#options-container').empty();
                
                // Добавляем поля для аргументов
                if (details.args) {
                    $('#args-container').append('<h5 class="mt-3">Аргументы</h5>');
                    
                    Object.entries(details.args).forEach(([name, description]) => {
                        $('#args-container').append(`
                            <div class="mb-3">
                                <label class="form-label">${description}</label>
                                <input type="text" class="form-control" name="args[]" placeholder="${name}">
                            </div>
                        `);
                    });
                }
                
                // Добавляем поля для опций
                if (details.options) {
                    $('#options-container').append('<h5 class="mt-3">Опции</h5>');
                    
                    Object.entries(details.options).forEach(([option, description]) => {
                        $('#options-container').append(`
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="${option.replace('--', '')}" value="${option}">
                                <label class="form-check-label" for="${option.replace('--', '')}">
                                    ${option} - ${description}
                                </label>
                            </div>
                        `);
                    });
                }
                
                // Показываем форму
                $('#output').addClass('d-none');
            });
            
            // Обработчик отправки формы
            $('#command-form').on('submit', function(e) {
                e.preventDefault();
                
                // Собираем данные формы
                const command = $('#command').val();
                const args = [];
                
                // Собираем значения аргументов
                $('input[name="args[]"]').each(function() {
                    args.push($(this).val());
                });
                
                // Собираем выбранные опции
                const options = [];
                $('#options-container input[type="checkbox"]:checked').each(function() {
                    options.push($(this).val());
                });
                
                // Показываем индикатор загрузки
                $('#loading').removeClass('d-none');
                $('#output').addClass('d-none');
                
                // Отправляем AJAX запрос
                $.ajax({
                    url: '',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        command: command,
                        args: args,
                        options: options
                    },
                    success: function(response) {
                        // Скрываем индикатор загрузки
                        $('#loading').addClass('d-none');
                        
                        // Показываем результат
                        $('#output').removeClass('d-none');
                        
                        if (response.error) {
                            $('#output').html('<div class="error">' + response.error + '</div>');
                        } else {
                            $('#output').html(response.output);
                        }
                    },
                    error: function() {
                        // Скрываем индикатор загрузки
                        $('#loading').addClass('d-none');
                        
                        // Показываем ошибку
                        $('#output').removeClass('d-none').html('<div class="error">Произошла ошибка при выполнении запроса</div>');
                    }
                });
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>