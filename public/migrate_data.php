<?php

// Веб-интерфейс для миграции данных из старого формата в новый
// Доступ через браузер: /migrate_data.php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Services\DataMigrationService;
use Illuminate\Support\Facades\DB;

?>
<!DOCTYPE html>
<html>
<head>
    <title>🔄 Data Migration Tool</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .success { color: #4ade80; font-weight: bold; }
        .error { color: #f87171; font-weight: bold; }
        .warning { color: #fbbf24; font-weight: bold; }
        .info { color: #60a5fa; font-weight: bold; }
        .card { 
            background: rgba(255,255,255,0.1); 
            border-radius: 10px; 
            padding: 20px; 
            margin: 15px 0;
            border: 1px solid rgba(255,255,255,0.2);
        }
        button { 
            background: linear-gradient(45deg, #667eea, #764ba2); 
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
            background: linear-gradient(45deg, #ef4444, #dc2626);
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(45deg, #4ade80, #22c55e);
            transition: width 0.5s ease;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            overflow: hidden;
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
            color: #4ade80;
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
            border-left: 3px solid #60a5fa;
            padding-left: 10px;
        }
        .migration-type {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            margin: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .migration-type:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.05);
        }
        .migration-type.selected {
            background: linear-gradient(45deg, #4ade80, #22c55e);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Data Migration Tool</h1>
        <p>Миграция данных из старого формата в новую рефакторенную архитектуру</p>
        
        <?php
        
        $action = $_GET['action'] ?? 'dashboard';
        $type = $_GET['type'] ?? 'all';
        
        try {
            if ($action === 'dashboard') {
                showDashboard();
            } elseif ($action === 'check') {
                showDataCheck();
            } elseif ($action === 'migrate') {
                performMigration($type);
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            echo "<div class='error'>📍 File: " . htmlspecialchars($e->getFile()) . " Line: " . $e->getLine() . "</div>";
        }
        
        function showDashboard() {
            echo "<div class='card'>";
            echo "<h2>📊 Current Database Status</h2>";
            
            $counts = getDataCounts();
            
            echo "<div class='stats-grid'>";
            foreach ($counts as $type => $count) {
                $icon = getTypeIcon($type);
                echo "<div class='stat-card'>";
                echo "<div>{$icon}</div>";
                echo "<div class='stat-number'>{$count}</div>";
                echo "<div>" . ucfirst($type) . "</div>";
                echo "</div>";
            }
            echo "</div>";
            
            echo "</div>";
            
            echo "<div class='card'>";
            echo "<h2>🎯 Migration Options</h2>";
            echo "<p>Выберите тип данных для миграции:</p>";
            
            $types = [
                'all' => '🔄 Все данные',
                'users' => '👤 Пользователи',
                'blogs' => '📝 Блоги/Встречи',
                'videos' => '🎥 Видео',
                'courses' => '📚 Курсы',
                'clubs' => '🏛️ Клубы',
                'participants' => '👥 Участники',
                'transactions' => '💳 Транзакции'
            ];
            
            foreach ($types as $typeKey => $typeName) {
                echo "<span class='migration-type' onclick='selectType(\"{$typeKey}\")'>{$typeName}</span>";
            }
            
            echo "<div style='margin-top: 20px;'>";
            echo "<a href='?action=check'><button>🔍 Проверить данные</button></a>";
            echo "<button onclick='startMigration()' class='danger'>🚀 Начать миграцию</button>";
            echo "</div>";
            echo "</div>";
        }
        
        function showDataCheck() {
            echo "<div class='card'>";
            echo "<h2>🔍 Data Structure Analysis</h2>";
            
            $analysis = analyzeDataStructure();
            
            echo "<table>";
            echo "<tr><th>Data Type</th><th>Records</th><th>Missing Fields</th><th>Status</th></tr>";
            
            foreach ($analysis as $type => $info) {
                $statusClass = empty($info['missing']) ? 'success' : 'warning';
                $statusText = empty($info['missing']) ? '✅ Ready' : '⚠️ Needs Migration';
                
                echo "<tr>";
                echo "<td>" . getTypeIcon($type) . " " . ucfirst($type) . "</td>";
                echo "<td>{$info['count']}</td>";
                echo "<td>" . implode(', ', $info['missing']) . "</td>";
                echo "<td class='{$statusClass}'>{$statusText}</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            echo "</div>";
            
            echo "<div class='card'>";
            echo "<h3>📋 Migration Plan</h3>";
            echo "<ul>";
            echo "<li><strong>Users:</strong> Add slugs, bio, preferences for better UX</li>";
            echo "<li><strong>Blogs/Meetings:</strong> Add SEO data, tags, counters for optimization</li>";
            echo "<li><strong>Videos:</strong> Add descriptions, thumbnails, SEO for better discovery</li>";
            echo "<li><strong>Courses:</strong> Add pricing, durations, detailed descriptions</li>";
            echo "<li><strong>Clubs:</strong> Add member counts, SEO, better categorization</li>";
            echo "<li><strong>Participants:</strong> Update counters in related tables</li>";
            echo "<li><strong>Transactions:</strong> Normalize status, add metadata</li>";
            echo "</ul>";
            echo "</div>";
            
            echo "<a href='?action=dashboard'><button>🏠 Back to Dashboard</button></a>";
        }
        
        function performMigration($type) {
            echo "<div class='card'>";
            echo "<h2>🚀 Running Migration: " . ucfirst($type) . "</h2>";
            
            $migrationService = app(DataMigrationService::class);
            
            echo "<div class='log-container' id='migrationLog'>";
            echo "<div class='log-entry'>🔄 Starting migration...</div>";
            
            $startTime = microtime(true);
            
            try {
                switch ($type) {
                    case 'all':
                        $result = $migrationService->migrateAllData();
                        break;
                    case 'users':
                        $result = ['users' => $migrationService->migrateUsers()];
                        break;
                    case 'blogs':
                        $result = ['blogs' => $migrationService->migrateBlogs()];
                        break;
                    case 'videos':
                        $result = ['videos' => $migrationService->migrateVideos()];
                        break;
                    case 'courses':
                        $result = ['courses' => $migrationService->migrateCourses()];
                        break;
                    case 'clubs':
                        $result = ['clubs' => $migrationService->migrateClubs()];
                        break;
                    case 'participants':
                        $result = ['participants' => $migrationService->migrateParticipants()];
                        break;
                    case 'transactions':
                        $result = ['transactions' => $migrationService->migrateTransactions()];
                        break;
                    default:
                        throw new Exception("Unknown migration type: {$type}");
                }
                
                $duration = round(microtime(true) - $startTime, 2);
                
                // Отображаем лог
                if (isset($result['log'])) {
                    foreach ($result['log'] as $logEntry) {
                        echo "<div class='log-entry'>" . htmlspecialchars($logEntry) . "</div>";
                    }
                }
                
                echo "<div class='log-entry success'>✅ Migration completed in {$duration}s</div>";
                
                echo "</div>"; // log-container
                
                // Отображаем результаты
                echo "<h3>📊 Results Summary</h3>";
                displayMigrationResults($result);
                
            } catch (Exception $e) {
                echo "<div class='log-entry error'>❌ Migration failed: " . htmlspecialchars($e->getMessage()) . "</div>";
                echo "</div>"; // log-container
            }
            
            echo "</div>"; // card
            
            echo "<a href='?action=dashboard'><button>🏠 Back to Dashboard</button></a>";
            echo "<a href='?action=check'><button>🔍 Check Results</button></a>";
        }
        
        function displayMigrationResults($result) {
            $results = $result['results'] ?? $result;
            
            echo "<table>";
            echo "<tr><th>Type</th><th>Updated</th><th>Errors</th><th>Status</th></tr>";
            
            foreach ($results as $type => $typeResult) {
                $icon = getTypeIcon($type);
                
                if (isset($typeResult['error'])) {
                    echo "<tr>";
                    echo "<td>{$icon} " . ucfirst($type) . "</td>";
                    echo "<td>-</td>";
                    echo "<td>-</td>";
                    echo "<td class='error'>❌ FAILED</td>";
                    echo "</tr>";
                } else {
                    $updated = $typeResult['updated'] ?? $typeResult['migrated'] ?? 0;
                    $errors = count($typeResult['errors'] ?? []);
                    $statusClass = $errors > 0 ? 'warning' : 'success';
                    $statusText = $errors > 0 ? "⚠️ {$errors} errors" : '✅ Success';
                    
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
        
        function getDataCounts() {
            return [
                'users' => DB::table('users')->count(),
                'blogs' => DB::table('blogs')->count(),
                'videos' => DB::table('content_video')->count(),
                'courses' => DB::table('courses')->count(),
                'clubs' => DB::table('club')->count(),
                'participants' => DB::table('participant_actions')->count(),
                'transactions' => DB::table('transactions')->count(),
            ];
        }
        
        function analyzeDataStructure() {
            $analysis = [];
            
            // Анализируем пользователей
            $analysis['users'] = [
                'count' => DB::table('users')->count(),
                'missing' => []
            ];
            
            $usersWithoutSlug = DB::table('users')->whereNull('slug')->count();
            $usersWithoutBio = DB::table('users')->whereNull('bio')->count();
            $usersWithoutPrefs = DB::table('users')->whereNull('preferences')->count();
            
            if ($usersWithoutSlug > 0) $analysis['users']['missing'][] = "slug ({$usersWithoutSlug})";
            if ($usersWithoutBio > 0) $analysis['users']['missing'][] = "bio ({$usersWithoutBio})";
            if ($usersWithoutPrefs > 0) $analysis['users']['missing'][] = "preferences ({$usersWithoutPrefs})";
            
            // Анализируем блоги
            $analysis['blogs'] = [
                'count' => DB::table('blogs')->count(),
                'missing' => []
            ];
            
            $blogsWithoutSlug = DB::table('blogs')->whereNull('slug')->count();
            $blogsWithoutSEO = DB::table('blogs')->whereNull('seo_data')->count();
            
            if ($blogsWithoutSlug > 0) $analysis['blogs']['missing'][] = "slug ({$blogsWithoutSlug})";
            if ($blogsWithoutSEO > 0) $analysis['blogs']['missing'][] = "seo_data ({$blogsWithoutSEO})";
            
            // Аналогично для других типов...
            $analysis['videos'] = [
                'count' => DB::table('content_video')->count(),
                'missing' => []
            ];
            
            $analysis['courses'] = [
                'count' => DB::table('courses')->count(),
                'missing' => []
            ];
            
            $analysis['clubs'] = [
                'count' => DB::table('club')->count(),
                'missing' => []
            ];
            
            $analysis['participants'] = [
                'count' => DB::table('participant_actions')->count(),
                'missing' => []
            ];
            
            $analysis['transactions'] = [
                'count' => DB::table('transactions')->count(),
                'missing' => []
            ];
            
            return $analysis;
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
            <h3>📝 Instructions:</h3>
            <ul>
                <li><strong>Dashboard:</strong> Обзор текущего состояния данных</li>
                <li><strong>Check Data:</strong> Анализ структуры и планирование миграции</li>
                <li><strong>Migration:</strong> Выполнение миграции выбранного типа данных</li>
                <li><strong>Safety:</strong> Все операции безопасны и не удаляют существующие данные</li>
                <li>После успешной миграции удалите этот файл из public/</li>
            </ul>
        </div>
        
    </div>
    
    <script>
        let selectedType = 'all';
        
        function selectType(type) {
            selectedType = type;
            
            // Обновляем визуальное выделение
            document.querySelectorAll('.migration-type').forEach(el => {
                el.classList.remove('selected');
            });
            
            event.target.classList.add('selected');
        }
        
        function startMigration() {
            if (confirm(`Вы уверены, что хотите мигрировать: ${selectedType}?\n\nЭто изменит данные в базе данных.`)) {
                window.location.href = `?action=migrate&type=${selectedType}`;
            }
        }
    </script>
    
</body>
</html>