<?php

// Веб-интерфейс для адаптации существующего дампа БД к новой архитектуре
// Доступ через браузер: /adapt_database.php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Services\DatabaseStructureService;
use App\Services\DataMigrationService;
use Illuminate\Support\Facades\DB;

?>
<!DOCTYPE html>
<html>
<head>
    <title>🔧 Database Adaptation Tool</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
            color: #fff; 
            padding: 20px; 
            margin: 0;
            min-height: 100vh;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: rgba(255,255,255,0.1); 
            border-radius: 15px; 
            padding: 30px; 
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        }
        .success { color: #48bb78; font-weight: bold; }
        .error { color: #f56565; font-weight: bold; }
        .warning { color: #ed8936; font-weight: bold; }
        .info { color: #4299e1; font-weight: bold; }
        .card { 
            background: rgba(255,255,255,0.1); 
            border-radius: 10px; 
            padding: 20px; 
            margin: 15px 0;
            border: 1px solid rgba(255,255,255,0.2);
        }
        button { 
            background: linear-gradient(45deg, #4299e1, #3182ce); 
            color: #fff; 
            border: none; 
            padding: 12px 24px; 
            margin: 8px; 
            cursor: pointer; 
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        button.danger {
            background: linear-gradient(45deg, #e53e3e, #c53030);
        }
        button.success {
            background: linear-gradient(45deg, #38a169, #2f855a);
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            overflow: hidden;
            margin: 15px 0;
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th { 
            background: rgba(255,255,255,0.2); 
            font-weight: bold;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #48bb78;
        }
        .log-container {
            background: rgba(0,0,0,0.3);
            border-radius: 8px;
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 15px 0;
        }
        .log-entry {
            margin: 5px 0;
            padding: 5px;
            border-left: 3px solid #4299e1;
            padding-left: 10px;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
            margin: 0 5px;
            position: relative;
        }
        .step.active {
            background: linear-gradient(45deg, #4299e1, #3182ce);
        }
        .step.completed {
            background: linear-gradient(45deg, #38a169, #2f855a);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Adaptation Tool</h1>
        <p>Адаптация существующего дампа БД к новой рефакторенной архитектуре</p>
        
        <div class="step-indicator">
            <div class="step <?= ($_GET['action'] ?? 'check') === 'check' ? 'active' : '' ?>">
                <strong>1. Анализ</strong><br>
                <small>Проверка структуры</small>
            </div>
            <div class="step <?= ($_GET['action'] ?? '') === 'adapt' ? 'active' : '' ?>">
                <strong>2. Адаптация</strong><br>
                <small>Добавление полей</small>
            </div>
            <div class="step <?= ($_GET['action'] ?? '') === 'migrate' ? 'active' : '' ?>">
                <strong>3. Миграция</strong><br>
                <small>Перенос данных</small>
            </div>
        </div>
        
        <?php
        
        $action = $_GET['action'] ?? 'check';
        
        try {
            if ($action === 'check') {
                showDatabaseAnalysis();
            } elseif ($action === 'adapt') {
                performDatabaseAdaptation();
            } elseif ($action === 'migrate') {
                performDataMigration();
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            echo "<div class='error'>📍 File: " . htmlspecialchars($e->getFile()) . " Line: " . $e->getLine() . "</div>";
        }
        
        function showDatabaseAnalysis() {
            echo "<div class='card'>";
            echo "<h2>📊 Анализ текущей структуры БД</h2>";
            
            // Проверяем подключение
            try {
                $pdo = app('db')->connection()->getPdo();
                echo "<div class='success'>✅ Подключение к БД установлено</div>";
            } catch (Exception $e) {
                echo "<div class='error'>❌ Ошибка подключения к БД: " . $e->getMessage() . "</div>";
                return;
            }
            
            // Анализируем таблицы
            echo "<h3>📋 Анализ таблиц:</h3>";
            
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
            
            $existingTables = [];
            foreach ($requiredTables as $table => $description) {
                $exists = checkTableExists($table);
                $count = $exists ? getTableCount($table) : 0;
                $statusClass = $exists ? 'success' : 'warning';
                $statusText = $exists ? '✅ Существует' : '⚠️ Отсутствует';
                
                if ($exists) {
                    $existingTables[$table] = $count;
                }
                
                echo "<tr>";
                echo "<td><code>{$table}</code></td>";
                echo "<td>{$description}</td>";
                echo "<td class='{$statusClass}'>{$statusText}</td>";
                echo "<td>{$count}</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Анализируем поля
            if (!empty($existingTables)) {
                echo "<h3>🔍 Анализ недостающих полей:</h3>";
                
                $missingFields = analyzeMissingFields($existingTables);
                
                if (!empty($missingFields)) {
                    echo "<table>";
                    echo "<tr><th>Таблица</th><th>Недостающие поля</th><th>Действие</th></tr>";
                    
                    foreach ($missingFields as $table => $fields) {
                        if (!empty($fields)) {
                            echo "<tr>";
                            echo "<td><code>{$table}</code></td>";
                            echo "<td>" . implode(', ', $fields) . "</td>";
                            echo "<td class='warning'>⚠️ Требуется адаптация</td>";
                            echo "</tr>";
                        }
                    }
                    
                    echo "</table>";
                    
                    echo "<div class='warning'>";
                    echo "<h4>⚠️ Обнаружены недостающие поля</h4>";
                    echo "<p>Для работы с новой архитектурой необходимо добавить недостающие поля в существующие таблицы.</p>";
                    echo "</div>";
                } else {
                    echo "<div class='success'>";
                    echo "<h4>✅ Структура БД готова</h4>";
                    echo "<p>Все необходимые поля присутствуют. Можно переходить к миграции данных.</p>";
                    echo "</div>";
                }
            }
            
            echo "</div>";
            
            // Кнопки действий
            echo "<div class='card'>";
            echo "<h3>🎯 Следующие шаги:</h3>";
            
            if (!empty($missingFields) && array_filter($missingFields)) {
                echo "<a href='?action=adapt'><button class='danger'>🔧 Адаптировать структуру БД</button></a>";
                echo "<p><small>Добавит недостающие поля в существующие таблицы</small></p>";
            } else {
                echo "<a href='?action=migrate'><button class='success'>🚀 Мигрировать данные</button></a>";
                echo "<p><small>Перенести данные в новый формат</small></p>";
            }
            
            echo "<a href='?action=check'><button>🔄 Обновить анализ</button></a>";
            echo "</div>";
        }
        
        function performDatabaseAdaptation() {
            echo "<div class='card'>";
            echo "<h2>🔧 Адаптация структуры БД</h2>";
            
            $structureService = app(DatabaseStructureService::class);
            
            echo "<div class='log-container' id='adaptationLog'>";
            echo "<div class='log-entry'>🔄 Начинаем адаптацию структуры...</div>";
            
            $startTime = microtime(true);
            
            try {
                $result = $structureService->adaptExistingDatabase();
                
                $duration = round(microtime(true) - $startTime, 2);
                
                // Отображаем лог
                if (isset($result['log'])) {
                    foreach ($result['log'] as $logEntry) {
                        echo "<div class='log-entry'>" . htmlspecialchars($logEntry) . "</div>";
                    }
                }
                
                echo "<div class='log-entry success'>✅ Адаптация завершена за {$duration}s</div>";
                
                echo "</div>"; // log-container
                
                // Отображаем результаты
                echo "<h3>📊 Результаты адаптации:</h3>";
                
                echo "<table>";
                echo "<tr><th>Категория</th><th>Результат</th></tr>";
                
                foreach ($result['results'] as $category => $categoryResult) {
                    echo "<tr>";
                    echo "<td>" . ucfirst($category) . "</td>";
                    echo "<td>";
                    
                    if (is_array($categoryResult)) {
                        foreach ($categoryResult as $item => $status) {
                            echo "<div><code>{$item}</code>: {$status}</div>";
                        }
                    } else {
                        echo $categoryResult;
                    }
                    
                    echo "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
                echo "<div class='success'>";
                echo "<h4>✅ Структура БД успешно адаптирована!</h4>";
                echo "<p>Теперь можно переходить к миграции данных.</p>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='log-entry error'>❌ Ошибка адаптации: " . htmlspecialchars($e->getMessage()) . "</div>";
                echo "</div>"; // log-container
            }
            
            echo "</div>"; // card
            
            echo "<a href='?action=check'><button>🔍 Проверить результат</button></a>";
            echo "<a href='?action=migrate'><button class='success'>🚀 Мигрировать данные</button></a>";
        }
        
        function performDataMigration() {
            echo "<div class='card'>";
            echo "<h2>🚀 Миграция данных</h2>";
            
            $migrationService = app(DataMigrationService::class);
            
            echo "<div class='log-container' id='migrationLog'>";
            echo "<div class='log-entry'>🔄 Начинаем миграцию данных...</div>";
            
            $startTime = microtime(true);
            
            try {
                $result = $migrationService->migrateAllData();
                
                $duration = round(microtime(true) - $startTime, 2);
                
                // Отображаем лог
                if (isset($result['log'])) {
                    foreach ($result['log'] as $logEntry) {
                        echo "<div class='log-entry'>" . htmlspecialchars($logEntry) . "</div>";
                    }
                }
                
                echo "<div class='log-entry success'>✅ Миграция завершена за {$duration}s</div>";
                
                echo "</div>"; // log-container
                
                // Отображаем результаты
                echo "<h3>📊 Результаты миграции:</h3>";
                displayMigrationResults($result);
                
                echo "<div class='success'>";
                echo "<h4>🎉 Миграция данных завершена!</h4>";
                echo "<p>Ваши данные успешно адаптированы к новой архитектуре.</p>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='log-entry error'>❌ Ошибка миграции: " . htmlspecialchars($e->getMessage()) . "</div>";
                echo "</div>"; // log-container
            }
            
            echo "</div>"; // card
            
            echo "<a href='?action=check'><button>🔍 Проверить результат</button></a>";
            echo "<a href='/'><button class='success'>🏠 Перейти на сайт</button></a>";
        }
        
        function displayMigrationResults($result) {
            $results = $result['results'] ?? $result;
            
            echo "<table>";
            echo "<tr><th>Тип данных</th><th>Обновлено</th><th>Ошибки</th><th>Статус</th></tr>";
            
            foreach ($results as $type => $typeResult) {
                $icon = getTypeIcon($type);
                
                if (isset($typeResult['error'])) {
                    echo "<tr>";
                    echo "<td>{$icon} " . ucfirst($type) . "</td>";
                    echo "<td>-</td>";
                    echo "<td>-</td>";
                    echo "<td class='error'>❌ ОШИБКА</td>";
                    echo "</tr>";
                } else {
                    $updated = $typeResult['updated'] ?? $typeResult['migrated'] ?? 0;
                    $errors = count($typeResult['errors'] ?? []);
                    $statusClass = $errors > 0 ? 'warning' : 'success';
                    $statusText = $errors > 0 ? "⚠️ {$errors} ошибок" : '✅ Успешно';
                    
                    echo "<tr>";
                    echo "<td>{$icon} " . ucfirst($type) . "</td>";
                    echo "<td>{$updated}</td>";
                    echo "<td>{$errors}</td>";
                    echo "<td class='{$statusClass}'>{$statusText}</td>";
                    echo "</tr>";
                }
            }
            
            echo "</table>";
        }
        
        function checkTableExists($table) {
            try {
                return \Illuminate\Support\Facades\Schema::hasTable($table);
            } catch (Exception $e) {
                return false;
            }
        }
        
        function getTableCount($table) {
            try {
                return DB::table($table)->count();
            } catch (Exception $e) {
                return 0;
            }
        }
        
        function analyzeMissingFields($existingTables) {
            $missingFields = [];
            
            // Анализируем users
            if (isset($existingTables['users'])) {
                $userFields = ['slug', 'avatar', 'avatar_original_name', 'bio', 'preferences'];
                $missing = [];
                foreach ($userFields as $field) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', $field)) {
                        $missing[] = $field;
                    }
                }
                $missingFields['users'] = $missing;
            }
            
            // Анализируем blogs
            if (isset($existingTables['blogs'])) {
                $blogFields = ['slug', 'short_description', 'tags', 'seo_data', 'likes_count'];
                $missing = [];
                foreach ($blogFields as $field) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('blogs', $field)) {
                        $missing[] = $field;
                    }
                }
                $missingFields['blogs'] = $missing;
            }
            
            // Аналогично для других таблиц...
            
            return $missingFields;
        }
        
        function getTypeIcon($type) {
            $icons = [
                'users' => '👤',
                'blogs' => '📝',
                'videos' => '🎥',
                'courses' => '📚',
                'clubs' => '🏛️',
                'participants' => '👥',
                'transactions' => '💳',
            ];
            
            return $icons[$type] ?? '📊';
        }
        
        ?>
        
        <hr style="border: 1px solid rgba(255,255,255,0.2); margin: 30px 0;">
        
        <div class="info">
            <h3>📝 Инструкции:</h3>
            <ul>
                <li><strong>Шаг 1 - Анализ:</strong> Проверяет текущую структуру БД и выявляет недостающие поля</li>
                <li><strong>Шаг 2 - Адаптация:</strong> Добавляет недостающие поля в существующие таблицы</li>
                <li><strong>Шаг 3 - Миграция:</strong> Переносит данные в новый формат с SEO, тегами и счетчиками</li>
                <li><strong>Безопасность:</strong> Все операции не удаляют существующие данные</li>
                <li>После завершения удалите этот файл из public/</li>
            </ul>
        </div>
        
    </div>
    
</body>
</html>