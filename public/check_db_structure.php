<?php

// Простая проверка структуры БД без Laravel
// Доступ через браузер: /check_db_structure.php

// Подключение к БД напрямую через PDO
$host = 'localhost';
$dbname = 'apspy_beta'; // Замените на ваше имя БД
$username = 'root';
$password = ''; // Замените на ваш пароль

?>
<!DOCTYPE html>
<html>
<head>
    <title>🔍 Database Structure Checker</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #2d3748; 
            color: #fff; 
            padding: 20px; 
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: rgba(255,255,255,0.1); 
            border-radius: 10px; 
            padding: 20px; 
        }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #555; }
        th { background: rgba(255,255,255,0.2); }
        .success { color: #48bb78; }
        .error { color: #f56565; }
        .warning { color: #ed8936; }
        .info { color: #4299e1; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Database Structure Checker</h1>
        
        <?php
        
        try {
            // Подключение к БД
            $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "<div class='success'>✅ Подключение к БД успешно!</div>";
            echo "<div class='info'>📊 База данных: {$dbname}</div>";
            
            // Проверяем основные таблицы
            echo "<h2>📋 Основные таблицы:</h2>";
            
            $requiredTables = [
                'users' => 'Пользователи',
                'blogs' => 'Блоги/Встречи', 
                'content_video' => 'Видео',
                'courses' => 'Курсы',
                'club' => 'Клубы',
                'categories' => 'Категории',
                'specialists' => 'Специалисты',
                'participant_actions' => 'Участники',
                'transactions' => 'Транзакции'
            ];
            
            echo "<table>";
            echo "<tr><th>Таблица</th><th>Описание</th><th>Статус</th><th>Записи</th></tr>";
            
            foreach ($requiredTables as $table => $description) {
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
                    $count = $stmt->fetchColumn();
                    echo "<tr>";
                    echo "<td><code>{$table}</code></td>";
                    echo "<td>{$description}</td>";
                    echo "<td class='success'>✅ Существует</td>";
                    echo "<td>{$count}</td>";
                    echo "</tr>";
                } catch (PDOException $e) {
                    echo "<tr>";
                    echo "<td><code>{$table}</code></td>";
                    echo "<td>{$description}</td>";
                    echo "<td class='warning'>⚠️ Отсутствует</td>";
                    echo "<td>-</td>";
                    echo "</tr>";
                }
            }
            
            echo "</table>";
            
            // Проверяем структуру таблицы club
            echo "<h2>🏛️ Структура таблицы 'club':</h2>";
            
            try {
                $stmt = $pdo->query("DESCRIBE `club`");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<table>";
                echo "<tr><th>Поле</th><th>Тип</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                
                foreach ($columns as $column) {
                    echo "<tr>";
                    echo "<td><code>{$column['Field']}</code></td>";
                    echo "<td>{$column['Type']}</td>";
                    echo "<td>{$column['Null']}</td>";
                    echo "<td>{$column['Key']}</td>";
                    echo "<td>{$column['Default']}</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
                // Проверяем наличие нужных полей
                $existingFields = array_column($columns, 'Field');
                $requiredFields = ['slug', 'short_description', 'tags', 'members_count', 'is_active', 'seo_data'];
                
                echo "<h3>🔍 Недостающие поля в 'club':</h3>";
                $missingFields = array_diff($requiredFields, $existingFields);
                
                if (empty($missingFields)) {
                    echo "<div class='success'>✅ Все необходимые поля присутствуют</div>";
                } else {
                    echo "<div class='warning'>⚠️ Недостающие поля:</div>";
                    echo "<ul>";
                    foreach ($missingFields as $field) {
                        echo "<li><code>{$field}</code></li>";
                    }
                    echo "</ul>";
                }
                
            } catch (PDOException $e) {
                echo "<div class='error'>❌ Ошибка проверки таблицы club: " . $e->getMessage() . "</div>";
            }
            
            // Проверяем структуру таблицы users
            echo "<h2>👤 Структура таблицы 'users':</h2>";
            
            try {
                $stmt = $pdo->query("DESCRIBE `users`");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Проверяем наличие нужных полей
                $existingFields = array_column($columns, 'Field');
                $requiredFields = ['slug', 'avatar', 'avatar_original_name', 'bio', 'preferences'];
                
                echo "<h3>🔍 Недостающие поля в 'users':</h3>";
                $missingFields = array_diff($requiredFields, $existingFields);
                
                if (empty($missingFields)) {
                    echo "<div class='success'>✅ Все необходимые поля присутствуют</div>";
                } else {
                    echo "<div class='warning'>⚠️ Недостающие поля:</div>";
                    echo "<ul>";
                    foreach ($missingFields as $field) {
                        echo "<li><code>{$field}</code></li>";
                    }
                    echo "</ul>";
                }
                
                echo "<div class='info'>📊 Всего полей в users: " . count($existingFields) . "</div>";
                
            } catch (PDOException $e) {
                echo "<div class='error'>❌ Ошибка проверки таблицы users: " . $e->getMessage() . "</div>";
            }
            
            // Проверяем версию MySQL
            $stmt = $pdo->query("SELECT VERSION()");
            $version = $stmt->fetchColumn();
            echo "<div class='info'>🗄️ Версия MySQL: {$version}</div>";
            
        } catch (PDOException $e) {
            echo "<div class='error'>❌ Ошибка подключения к БД: " . $e->getMessage() . "</div>";
            echo "<div class='info'>💡 Проверьте настройки подключения в начале файла</div>";
        }
        
        ?>
        
        <hr style="border: 1px solid #555; margin: 30px 0;">
        
        <div class="info">
            <h3>📝 Инструкции:</h3>
            <ol>
                <li>Измените настройки подключения к БД в начале файла</li>
                <li>Проверьте структуру существующих таблиц</li>
                <li>Если есть недостающие поля - выполните адаптацию</li>
                <li>После проверки удалите этот файл</li>
            </ol>
        </div>
        
    </div>
</body>
</html>