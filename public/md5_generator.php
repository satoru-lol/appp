<?php
/**
 * Генератор MD5 хешей для паролей
 * Использование:
 * 1. Откройте скрипт в браузере
 * 2. Введите пароль
 * 3. Получите MD5 хеш для использования в admin_console.php и console_api.php
 * 
 * После использования УДАЛИТЕ ЭТОТ ФАЙЛ с сервера!
 */

$generatedHash = '';
$password = '';

// Проверяем, был ли отправлен пароль
if (isset($_POST['password']) && !empty($_POST['password'])) {
    $password = $_POST['password'];
    $generatedHash = md5($password);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генератор MD5 хешей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .alert {
            margin-top: 20px;
        }
        .hash-result {
            padding: 10px;
            background: #f1f3f5;
            border-radius: 4px;
            font-family: monospace;
            word-break: break-all;
        }
        .warning-box {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Генератор MD5 хешей</h1>
        
        <div class="card">
            <div class="card-header">
                Генерация MD5 хеша для пароля
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="password" class="form-label">Введите пароль</label>
                        <input type="text" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required>
                        <div class="form-text">Пароль, который вы хотите использовать для доступа к консоли.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Сгенерировать хеш</button>
                </form>
                
                <?php if ($generatedHash): ?>
                <div class="alert alert-success mt-3">
                    <h5>MD5 хеш сгенерирован:</h5>
                    <div class="hash-result"><?php echo $generatedHash; ?></div>
                    <p class="mt-2 mb-0">Используйте этот хеш в файлах admin_console.php и console_api.php</p>
                </div>
                <?php endif; ?>
                
                <div class="warning-box">
                    <strong>Внимание!</strong> После использования этого скрипта для генерации хешей, 
                    <span class="text-danger">обязательно удалите этот файл</span> с сервера для обеспечения безопасности.
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                Как использовать MD5 хеш
            </div>
            <div class="card-body">
                <p>Для файла <code>admin_console.php</code>:</p>
                <pre><code>// MD5 хеш пароля
$passwordHash = '<?php echo $generatedHash ?: 'ваш_md5_хеш_здесь'; ?>';
if (md5($_POST['password']) === $passwordHash) {
    // Код авторизации
}</code></pre>
                
                <p class="mt-3">Для файла <code>console_api.php</code>:</p>
                <pre><code>// Проверка авторизации по API ключу
$apiKey = $_GET['api_key'] ?? $_POST['api_key'] ?? '';
$validApiKeyHash = '<?php echo $generatedHash ?: 'ваш_md5_хеш_здесь'; ?>';

if (md5($apiKey) !== $validApiKeyHash) {
    // Код обработки ошибки авторизации
}</code></pre>
            </div>
        </div>
    </div>
</body>
</html> 