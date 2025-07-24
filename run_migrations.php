<?php

// Простой скрипт для запуска миграций без artisan
// Используется когда PHP CLI недоступен

require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "🔧 Checking database connection...\n";
    
    // Проверяем подключение к БД
    $pdo = app('db')->connection()->getPdo();
    echo "✅ Database connected successfully!\n";
    
    echo "\n📊 Checking users table structure...\n";
    
    // Проверяем существование таблицы users
    if (!Schema::hasTable('users')) {
        echo "❌ Table 'users' does not exist!\n";
        exit(1);
    }
    
    echo "✅ Table 'users' exists\n";
    
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
            echo "➕ Adding field '$field'...\n";
            
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
        } else {
            echo "✅ Field '$field' already exists\n";
        }
    }
    
    if (!empty($addedFields)) {
        echo "\n🎉 Successfully added fields: " . implode(', ', $addedFields) . "\n";
    } else {
        echo "\n✅ All required fields already exist\n";
    }
    
    // Проверяем таблицу specialists
    echo "\n📊 Checking specialists table...\n";
    
    if (!Schema::hasTable('specialists')) {
        echo "➕ Creating specialists table...\n";
        
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
        
        echo "✅ Specialists table created\n";
    } else {
        echo "✅ Specialists table already exists\n";
    }
    
    echo "\n🎊 Migration completed successfully!\n";
    echo "\n📝 Next steps:\n";
    echo "1. Test avatar upload/display functionality\n";
    echo "2. Run: php run_migrations.php (this script) if needed again\n";
    echo "3. Check the website for proper avatar display\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    exit(1);
}