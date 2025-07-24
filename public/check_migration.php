<?php

// Веб-интерфейс для проверки и выполнения миграций
// Доступ через браузер: /check_migration.php

// Подключаем Laravel
require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

?>
<!DOCTYPE html>
<html>
<head>
    <title>🔧 Migration Checker</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        .warning { color: #ffaa00; }
        .info { color: #00aaff; }
        pre { background: #000; padding: 10px; border-radius: 5px; }
        button { background: #333; color: #fff; border: 1px solid #555; padding: 10px; margin: 5px; cursor: pointer; }
        button:hover { background: #555; }
    </style>
</head>
<body>
    <h1>🔧 Laravel Migration Checker & Runner</h1>
    
    <?php
    
    $action = $_GET['action'] ?? 'check';
    
    try {
        echo "<div class='info'>🔧 Checking database connection...</div>";
        
        // Проверяем подключение к БД
        $pdo = app('db')->connection()->getPdo();
        echo "<div class='success'>✅ Database connected successfully!</div>";
        
        if ($action === 'run') {
            echo "<h2>🚀 Running Migrations...</h2>";
            
            // Проверяем и добавляем недостающие поля в users
            echo "<div class='info'>📊 Checking users table structure...</div>";
            
            if (!Schema::hasTable('users')) {
                echo "<div class='error'>❌ Table 'users' does not exist!</div>";
                exit;
            }
            
            echo "<div class='success'>✅ Table 'users' exists</div>";
            
            // Проверяем и добавляем недостающие поля
            $fieldsToAdd = [
                'avatar' => 'string',
                'avatar_original_name' => 'string', 
                'bio' => 'text',
                'quick_access_token' => 'string',
                'last_login_at' => 'timestamp',
                'last_login_ip' => 'string',
                'preferences' => 'json'
            ];
            
            $addedFields = [];
            
            foreach ($fieldsToAdd as $field => $type) {
                if (!Schema::hasColumn('users', $field)) {
                    echo "<div class='warning'>➕ Adding field '$field'...</div>";
                    
                    Schema::table('users', function (Blueprint $table) use ($field, $type) {
                        switch ($type) {
                            case 'string':
                                $table->string($field)->nullable();
                                break;
                            case 'text':
                                $table->text($field)->nullable();
                                break;
                            case 'timestamp':
                                $table->timestamp($field)->nullable();
                                break;
                            case 'json':
                                $table->json($field)->nullable();
                                break;
                        }
                    });
                    
                    $addedFields[] = $field;
                    echo "<div class='success'>✅ Field '$field' added successfully</div>";
                } else {
                    echo "<div class='success'>✅ Field '$field' already exists</div>";
                }
            }
            
            // Проверяем таблицу specialists
            echo "<div class='info'>📊 Checking specialists table...</div>";
            
            if (!Schema::hasTable('specialists')) {
                echo "<div class='warning'>➕ Creating specialists table...</div>";
                
                Schema::create('specialists', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
                    $table->string('title');
                    $table->text('description')->nullable();
                    $table->string('specialization')->nullable();
                    $table->integer('experience_years')->default(0);
                    $table->decimal('rating', 3, 2)->default(0);
                    $table->integer('reviews_count')->default(0);
                    $table->decimal('price_per_hour', 8, 2)->nullable();
                    $table->json('services')->nullable();
                    $table->json('working_hours')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->boolean('is_verified')->default(false);
                    $table->boolean('is_popular')->default(false);
                    $table->integer('views_count')->default(0);
                    $table->timestamp('last_activity_at')->nullable();
                    $table->timestamps();
                    
                    $table->index(['is_active', 'is_verified']);
                    $table->index(['category_id', 'is_active']);
                    $table->index(['rating', 'reviews_count']);
                    $table->index('is_popular');
                });
                
                echo "<div class='success'>✅ Specialists table created</div>";
            } else {
                echo "<div class='success'>✅ Specialists table already exists</div>";
            }
            
            if (!empty($addedFields)) {
                echo "<div class='success'>🎉 Successfully added fields: " . implode(', ', $addedFields) . "</div>";
            } else {
                echo "<div class='success'>✅ All required fields already exist</div>";
            }
            
            echo "<div class='success'>🎊 Migration completed successfully!</div>";
            
        } else {
            // Только проверка
            echo "<h2>📊 Database Structure Check</h2>";
            
            // Проверяем таблицы
            $tables = ['users', 'specialists', 'categories', 'clubs', 'blogs', 'courses'];
            
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    echo "<div class='success'>✅ Table '$table' exists</div>";
                } else {
                    echo "<div class='error'>❌ Table '$table' missing</div>";
                }
            }
            
            // Проверяем поля в users
            echo "<h3>👤 Users table fields:</h3>";
            $userFields = ['avatar', 'avatar_original_name', 'bio', 'quick_access_token', 'last_login_at', 'last_login_ip', 'preferences'];
            
            foreach ($userFields as $field) {
                if (Schema::hasColumn('users', $field)) {
                    echo "<div class='success'>✅ users.$field exists</div>";
                } else {
                    echo "<div class='warning'>⚠️ users.$field missing</div>";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<div class='error'>📍 File: " . htmlspecialchars($e->getFile()) . " Line: " . $e->getLine() . "</div>";
    }
    
    ?>
    
    <hr>
    <div>
        <a href="?action=check"><button>🔍 Check Only</button></a>
        <a href="?action=run"><button>🚀 Run Migrations</button></a>
        <a href="/"><button>🏠 Back to Site</button></a>
    </div>
    
    <hr>
    <div class='info'>
        <h3>📝 Instructions:</h3>
        <ul>
            <li><strong>Check Only:</strong> Проверяет структуру БД без изменений</li>
            <li><strong>Run Migrations:</strong> Выполняет недостающие миграции</li>
            <li>После успешной миграции удалите этот файл из public/</li>
        </ul>
    </div>
    
</body>
</html>