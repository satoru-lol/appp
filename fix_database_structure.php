<?php

// Простой скрипт для исправления структуры БД
// Запуск: php fix_database_structure.php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "🔧 Исправление структуры БД...\n";

try {
    // Проверяем подключение
    DB::connection()->getPdo();
    echo "✅ Подключение к БД установлено\n";
    
    $fixed = [];
    
    // Исправляем таблицу users
    if (Schema::hasTable('users')) {
        echo "📊 Обрабатываем таблицу users...\n";
        
        Schema::table('users', function (Blueprint $table) use (&$fixed) {
            if (!Schema::hasColumn('users', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('lastname');
                $fixed[] = 'users.slug';
                echo "  ➕ Добавлено поле slug\n";
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email_verified_at');
                $fixed[] = 'users.avatar';
                echo "  ➕ Добавлено поле avatar\n";
            }
            if (!Schema::hasColumn('users', 'avatar_original_name')) {
                $table->string('avatar_original_name')->nullable()->after('avatar');
                $fixed[] = 'users.avatar_original_name';
                echo "  ➕ Добавлено поле avatar_original_name\n";
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('avatar_original_name');
                $fixed[] = 'users.bio';
                echo "  ➕ Добавлено поле bio\n";
            }
            if (!Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('bio');
                $fixed[] = 'users.preferences';
                echo "  ➕ Добавлено поле preferences\n";
            }
        });
    }
    
    // Исправляем таблицу blogs
    if (Schema::hasTable('blogs')) {
        echo "📝 Обрабатываем таблицу blogs...\n";
        
        Schema::table('blogs', function (Blueprint $table) use (&$fixed) {
            if (!Schema::hasColumn('blogs', 'slug')) {
                $table->string('slug')->nullable()->unique();
                $fixed[] = 'blogs.slug';
                echo "  ➕ Добавлено поле slug\n";
            }
            if (!Schema::hasColumn('blogs', 'short_description')) {
                $table->text('short_description')->nullable();
                $fixed[] = 'blogs.short_description';
                echo "  ➕ Добавлено поле short_description\n";
            }
            if (!Schema::hasColumn('blogs', 'tags')) {
                $table->json('tags')->nullable();
                $fixed[] = 'blogs.tags';
                echo "  ➕ Добавлено поле tags\n";
            }
            if (!Schema::hasColumn('blogs', 'likes_count')) {
                $table->integer('likes_count')->default(0);
                $fixed[] = 'blogs.likes_count';
                echo "  ➕ Добавлено поле likes_count\n";
            }
            if (!Schema::hasColumn('blogs', 'dislikes_count')) {
                $table->integer('dislikes_count')->default(0);
                $fixed[] = 'blogs.dislikes_count';
                echo "  ➕ Добавлено поле dislikes_count\n";
            }
            if (!Schema::hasColumn('blogs', 'comments_count')) {
                $table->integer('comments_count')->default(0);
                $fixed[] = 'blogs.comments_count';
                echo "  ➕ Добавлено поле comments_count\n";
            }
            if (!Schema::hasColumn('blogs', 'participants_count')) {
                $table->integer('participants_count')->default(0);
                $fixed[] = 'blogs.participants_count';
                echo "  ➕ Добавлено поле participants_count\n";
            }
            if (!Schema::hasColumn('blogs', 'seo_data')) {
                $table->json('seo_data')->nullable();
                $fixed[] = 'blogs.seo_data';
                echo "  ➕ Добавлено поле seo_data\n";
            }
        });
    }
    
    // Исправляем таблицу club
    if (Schema::hasTable('club')) {
        echo "🏛️ Обрабатываем таблицу club...\n";
        
        Schema::table('club', function (Blueprint $table) use (&$fixed) {
            if (!Schema::hasColumn('club', 'short_description')) {
                $table->text('short_description')->nullable();
                $fixed[] = 'club.short_description';
                echo "  ➕ Добавлено поле short_description\n";
            }
            if (!Schema::hasColumn('club', 'tags')) {
                $table->json('tags')->nullable();
                $fixed[] = 'club.tags';
                echo "  ➕ Добавлено поле tags\n";
            }
            if (!Schema::hasColumn('club', 'members_count')) {
                $table->integer('members_count')->default(0);
                $fixed[] = 'club.members_count';
                echo "  ➕ Добавлено поле members_count\n";
            }
            if (!Schema::hasColumn('club', 'is_active')) {
                $table->boolean('is_active')->default(true);
                $fixed[] = 'club.is_active';
                echo "  ➕ Добавлено поле is_active\n";
            }
            if (!Schema::hasColumn('club', 'seo_data')) {
                $table->json('seo_data')->nullable();
                $fixed[] = 'club.seo_data';
                echo "  ➕ Добавлено поле seo_data\n";
            }
        });
    }
    
    // Исправляем таблицу content_video
    if (Schema::hasTable('content_video')) {
        echo "🎥 Обрабатываем таблицу content_video...\n";
        
        Schema::table('content_video', function (Blueprint $table) use (&$fixed) {
            if (!Schema::hasColumn('content_video', 'slug')) {
                $table->string('slug')->nullable()->unique();
                $fixed[] = 'content_video.slug';
                echo "  ➕ Добавлено поле slug\n";
            }
            if (!Schema::hasColumn('content_video', 'description')) {
                $table->text('description')->nullable();
                $fixed[] = 'content_video.description';
                echo "  ➕ Добавлено поле description\n";
            }
            if (!Schema::hasColumn('content_video', 'duration')) {
                $table->integer('duration')->nullable();
                $fixed[] = 'content_video.duration';
                echo "  ➕ Добавлено поле duration\n";
            }
            if (!Schema::hasColumn('content_video', 'views')) {
                $table->integer('views')->default(0);
                $fixed[] = 'content_video.views';
                echo "  ➕ Добавлено поле views\n";
            }
            if (!Schema::hasColumn('content_video', 'tags')) {
                $table->json('tags')->nullable();
                $fixed[] = 'content_video.tags';
                echo "  ➕ Добавлено поле tags\n";
            }
        });
    }
    
    // Исправляем таблицу courses
    if (Schema::hasTable('courses')) {
        echo "📚 Обрабатываем таблицу courses...\n";
        
        Schema::table('courses', function (Blueprint $table) use (&$fixed) {
            if (!Schema::hasColumn('courses', 'slug')) {
                $table->string('slug')->nullable()->unique();
                $fixed[] = 'courses.slug';
                echo "  ➕ Добавлено поле slug\n";
            }
            if (!Schema::hasColumn('courses', 'description')) {
                $table->text('description')->nullable();
                $fixed[] = 'courses.description';
                echo "  ➕ Добавлено поле description\n";
            }
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 8, 2)->nullable();
                $fixed[] = 'courses.price';
                echo "  ➕ Добавлено поле price\n";
            }
            if (!Schema::hasColumn('courses', 'tags')) {
                $table->json('tags')->nullable();
                $fixed[] = 'courses.tags';
                echo "  ➕ Добавлено поле tags\n";
            }
        });
    }
    
    // Создаем таблицу specialists если её нет
    if (!Schema::hasTable('specialists')) {
        echo "👨‍⚕️ Создаем таблицу specialists...\n";
        
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
        
        $fixed[] = 'specialists table created';
        echo "  ✅ Таблица specialists создана\n";
    }
    
    // Создаем таблицу likes если её нет
    if (!Schema::hasTable('likes')) {
        echo "👍 Создаем таблицу likes...\n";
        
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('object_type');
            $table->unsignedBigInteger('object_id');
            $table->timestamps();
            
            $table->unique(['user_id', 'object_type', 'object_id']);
            $table->index(['object_type', 'object_id']);
        });
        
        $fixed[] = 'likes table created';
        echo "  ✅ Таблица likes создана\n";
    }
    
    // Создаем таблицу dislikes если её нет
    if (!Schema::hasTable('dislikes')) {
        echo "👎 Создаем таблицу dislikes...\n";
        
        Schema::create('dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('object_type');
            $table->unsignedBigInteger('object_id');
            $table->timestamps();
            
            $table->unique(['user_id', 'object_type', 'object_id']);
            $table->index(['object_type', 'object_id']);
        });
        
        $fixed[] = 'dislikes table created';
        echo "  ✅ Таблица dislikes создана\n";
    }
    
    if (empty($fixed)) {
        echo "✅ Структура БД уже актуальна!\n";
    } else {
        echo "\n🎉 Исправления завершены!\n";
        echo "📊 Всего изменений: " . count($fixed) . "\n";
        echo "📝 Список изменений:\n";
        foreach ($fixed as $change) {
            echo "  - {$change}\n";
        }
    }
    
    echo "\n✅ Теперь можете запустить: php artisan db:adapt\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
    echo "📍 Файл: " . $e->getFile() . " Строка: " . $e->getLine() . "\n";
    exit(1);
}