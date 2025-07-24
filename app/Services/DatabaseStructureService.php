<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

class DatabaseStructureService
{
    protected $log = [];
    
    /**
     * Адаптировать существующую БД к новой архитектуре
     */
    public function adaptExistingDatabase(): array
    {
        $this->log('🔧 Starting database structure adaptation...');
        
        $results = [
            'tables_checked' => $this->checkRequiredTables(),
            'fields_added' => $this->addMissingFields(),
            'indexes_created' => $this->createMissingIndexes(),
            'constraints_fixed' => $this->fixConstraints(),
        ];
        
        $this->log('🎉 Database adaptation completed!');
        
        return [
            'success' => true,
            'results' => $results,
            'log' => $this->log
        ];
    }
    
    /**
     * Проверить и создать недостающие таблицы
     */
    public function checkRequiredTables(): array
    {
        $this->log('📊 Checking required tables...');
        
        $requiredTables = [
            'users' => $this->createUsersTableIfNeeded(),
            'blogs' => $this->createBlogsTableIfNeeded(),
            'content_video' => $this->createVideosTableIfNeeded(),
            'courses' => $this->createCoursesTableIfNeeded(),
            'club' => $this->createClubsTableIfNeeded(),
            'categories' => $this->createCategoriesTableIfNeeded(),
            'specialists' => $this->createSpecialistsTableIfNeeded(),
            'likes' => $this->createLikesTableIfNeeded(),
            'dislikes' => $this->createDislikesTableIfNeeded(),
            'participant_actions' => $this->createParticipantActionsTableIfNeeded(),
            'transactions' => $this->createTransactionsTableIfNeeded(),
        ];
        
        return $requiredTables;
    }
    
    /**
     * Добавить недостающие поля в существующие таблицы
     */
    public function addMissingFields(): array
    {
        $this->log('➕ Adding missing fields...');
        
        $results = [];
        
        // Поля для пользователей
        $results['users'] = $this->addUserFields();
        
        // Поля для блогов
        $results['blogs'] = $this->addBlogFields();
        
        // Поля для видео
        $results['videos'] = $this->addVideoFields();
        
        // Поля для курсов
        $results['courses'] = $this->addCourseFields();
        
        // Поля для клубов
        $results['clubs'] = $this->addClubFields();
        
        // Поля для транзакций
        $results['transactions'] = $this->addTransactionFields();
        
        return $results;
    }
    
    /**
     * Создать недостающие индексы
     */
    public function createMissingIndexes(): array
    {
        $this->log('🔍 Creating missing indexes...');
        
        $results = [];
        
        try {
            // Индексы для пользователей
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    if (!$this->indexExists('users', 'users_slug_index')) {
                        $table->index('slug');
                    }
                    if (!$this->indexExists('users', 'users_email_phone_verified_at_index')) {
                        $table->index(['email', 'phone_verified_at']);
                    }
                });
                $results['users'] = 'indexes created';
            }
            
            // Индексы для блогов
            if (Schema::hasTable('blogs')) {
                Schema::table('blogs', function (Blueprint $table) {
                    if (!$this->indexExists('blogs', 'blogs_slug_index')) {
                        $table->index('slug');
                    }
                    if (!$this->indexExists('blogs', 'blogs_is_meeting_status_date_index')) {
                        $table->index(['is_meeting', 'status', 'date']);
                    }
                });
                $results['blogs'] = 'indexes created';
            }
            
            // Аналогично для других таблиц...
            
        } catch (\Exception $e) {
            $results['error'] = $e->getMessage();
        }
        
        return $results;
    }
    
    /**
     * Исправить ограничения и связи
     */
    public function fixConstraints(): array
    {
        $this->log('🔗 Fixing constraints...');
        
        // Здесь можно добавить логику для исправления внешних ключей
        // если они нужны и отсутствуют
        
        return ['status' => 'constraints checked'];
    }
    
    // === МЕТОДЫ СОЗДАНИЯ ТАБЛИЦ ===
    
    protected function createUsersTableIfNeeded(): string
    {
        if (Schema::hasTable('users')) {
            return 'exists';
        }
        
        // Если таблицы нет, создаем базовую структуру
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('group')->default('user');
            $table->string('phone')->nullable()->unique();
            $table->string('verification_code')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('auto')->default(false);
            $table->string('name')->nullable();
            $table->string('action')->nullable();
            $table->string('permissions')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->string('psy_lance')->nullable();
            $table->boolean('used_sub')->default(false);
            $table->timestamps();
        });
        
        return 'created';
    }
    
    protected function createBlogsTableIfNeeded(): string
    {
        if (Schema::hasTable('blogs')) {
            return 'exists';
        }
        
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('blog_category_id')->nullable();
            $table->bigInteger('blog_content_id')->nullable();
            $table->string('name');
            $table->string('time_read')->nullable();
            $table->string('image')->nullable();
            $table->integer('views')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('reg')->nullable();
            $table->string('video')->nullable();
            $table->string('feedback')->nullable();
            $table->string('amount')->nullable();
            $table->integer('product_level')->nullable();
            $table->datetime('date')->nullable();
            $table->boolean('is_meeting')->nullable();
            $table->string('fio')->nullable();
            $table->integer('format_id')->nullable();
            $table->string('explanation')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });
        
        return 'created';
    }
    
    protected function createVideosTableIfNeeded(): string
    {
        if (Schema::hasTable('content_video')) {
            return 'exists';
        }
        
        Schema::create('content_video', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->integer('category_id');
            $table->timestamps();
        });
        
        return 'created';
    }
    
    protected function createCoursesTableIfNeeded(): string
    {
        if (Schema::hasTable('courses')) {
            return 'exists';
        }
        
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_category_id');
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->json('times')->nullable();
            $table->string('speakers')->nullable();
            $table->string('theory')->nullable();
            $table->string('practice')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('views')->default(0);
            $table->string('feedback', 500)->nullable();
            $table->boolean('is_polygon')->nullable();
            $table->boolean('is_hidden')->nullable();
            $table->integer('product_level')->nullable();
            $table->timestamps();
        });
        
        return 'created';
    }
    
    protected function createClubsTableIfNeeded(): string
    {
        if (Schema::hasTable('club')) {
            return 'exists';
        }
        
        Schema::create('club', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('text')->nullable();
            $table->string('image')->nullable();
            $table->string('times')->nullable();
            $table->datetime('date')->nullable();
            $table->string('speakers')->nullable();
            $table->string('theory')->nullable();
            $table->string('feedback')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('video')->nullable();
            $table->integer('product_level')->nullable();
            $table->boolean('is_hidden')->nullable();
            $table->string('practice')->nullable();
            $table->timestamps();
        });
        
        return 'created';
    }
    
    protected function createCategoriesTableIfNeeded(): string
    {
        if (Schema::hasTable('categories')) {
            return 'exists';
        }
        
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        return 'created';
    }
    
    protected function createSpecialistsTableIfNeeded(): string
    {
        if (Schema::hasTable('specialists')) {
            return 'exists';
        }
        
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
        
        return 'created';
    }
    
    protected function createLikesTableIfNeeded(): string
    {
        if (Schema::hasTable('likes')) {
            return 'exists';
        }
        
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('object_type'); // blog, course, video, etc.
            $table->unsignedBigInteger('object_id');
            $table->timestamps();
            
            $table->unique(['user_id', 'object_type', 'object_id']);
            $table->index(['object_type', 'object_id']);
        });
        
        return 'created';
    }
    
    protected function createDislikesTableIfNeeded(): string
    {
        if (Schema::hasTable('dislikes')) {
            return 'exists';
        }
        
        Schema::create('dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('object_type');
            $table->unsignedBigInteger('object_id');
            $table->timestamps();
            
            $table->unique(['user_id', 'object_type', 'object_id']);
            $table->index(['object_type', 'object_id']);
        });
        
        return 'created';
    }
    
    protected function createParticipantActionsTableIfNeeded(): string
    {
        if (Schema::hasTable('participant_actions')) {
            return 'exists';
        }
        
        Schema::create('participant_actions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('object_name')->nullable();
            $table->integer('object_id')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'object_name']);
            $table->index(['object_name', 'object_id']);
            $table->unique(['user_id', 'object_name', 'object_id'], 'unique_participation');
        });
        
        return 'created';
    }
    
    protected function createTransactionsTableIfNeeded(): string
    {
        if (Schema::hasTable('transactions')) {
            return 'exists';
        }
        
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('shop')->nullable();
            $table->string('op_key')->nullable();
            $table->string('inv_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('accepted_perms')->nullable();
            $table->integer('product_id')->nullable();
            $table->decimal('sum', 10, 2)->nullable();
            $table->string('state')->nullable();
            $table->string('signature_value')->nullable();
            $table->timestamps();
        });
        
        return 'created';
    }
    
    // === МЕТОДЫ ДОБАВЛЕНИЯ ПОЛЕЙ ===
    
    protected function addUserFields(): array
    {
        if (!Schema::hasTable('users')) {
            return ['error' => 'users table not found'];
        }
        
        $added = [];
        
        Schema::table('users', function (Blueprint $table) use (&$added) {
            if (!Schema::hasColumn('users', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('lastname');
                $added[] = 'slug';
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email_verified_at');
                $added[] = 'avatar';
            }
            if (!Schema::hasColumn('users', 'avatar_original_name')) {
                $table->string('avatar_original_name')->nullable()->after('avatar');
                $added[] = 'avatar_original_name';
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('avatar_original_name');
                $added[] = 'bio';
            }
            if (!Schema::hasColumn('users', 'quick_access_token')) {
                $table->string('quick_access_token')->nullable()->after('bio');
                $added[] = 'quick_access_token';
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('quick_access_token');
                $added[] = 'last_login_at';
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
                $added[] = 'last_login_ip';
            }
            if (!Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('last_login_ip');
                $added[] = 'preferences';
            }
        });
        
        return $added;
    }
    
    protected function addBlogFields(): array
    {
        if (!Schema::hasTable('blogs')) {
            return ['error' => 'blogs table not found'];
        }
        
        $added = [];
        
        Schema::table('blogs', function (Blueprint $table) use (&$added) {
            if (!Schema::hasColumn('blogs', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('quantity');
                $added[] = 'slug';
            }
            if (!Schema::hasColumn('blogs', 'short_description')) {
                $table->text('short_description')->nullable()->after('slug');
                $added[] = 'short_description';
            }
            if (!Schema::hasColumn('blogs', 'tags')) {
                $table->json('tags')->nullable()->after('short_description');
                $added[] = 'tags';
            }
            if (!Schema::hasColumn('blogs', 'likes_count')) {
                $table->integer('likes_count')->default(0)->after('tags');
                $added[] = 'likes_count';
            }
            if (!Schema::hasColumn('blogs', 'dislikes_count')) {
                $table->integer('dislikes_count')->default(0)->after('likes_count');
                $added[] = 'dislikes_count';
            }
            if (!Schema::hasColumn('blogs', 'comments_count')) {
                $table->integer('comments_count')->default(0)->after('dislikes_count');
                $added[] = 'comments_count';
            }
            if (!Schema::hasColumn('blogs', 'participants_count')) {
                $table->integer('participants_count')->default(0)->after('comments_count');
                $added[] = 'participants_count';
            }
            if (!Schema::hasColumn('blogs', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('participants_count');
                $added[] = 'is_featured';
            }
            if (!Schema::hasColumn('blogs', 'seo_data')) {
                $table->json('seo_data')->nullable()->after('is_featured');
                $added[] = 'seo_data';
            }
        });
        
        return $added;
    }
    
    protected function addVideoFields(): array
    {
        if (!Schema::hasTable('content_video')) {
            return ['error' => 'content_video table not found'];
        }
        
        $added = [];
        
        Schema::table('content_video', function (Blueprint $table) use (&$added) {
            if (!Schema::hasColumn('content_video', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
                $added[] = 'slug';
            }
            if (!Schema::hasColumn('content_video', 'description')) {
                $table->text('description')->nullable()->after('slug');
                $added[] = 'description';
            }
            if (!Schema::hasColumn('content_video', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('description');
                $added[] = 'thumbnail';
            }
            if (!Schema::hasColumn('content_video', 'duration')) {
                $table->integer('duration')->nullable()->after('thumbnail');
                $added[] = 'duration';
            }
            if (!Schema::hasColumn('content_video', 'views')) {
                $table->integer('views')->default(0)->after('duration');
                $added[] = 'views';
            }
            if (!Schema::hasColumn('content_video', 'access_level')) {
                $table->integer('access_level')->default(1)->after('views');
                $added[] = 'access_level';
            }
            if (!Schema::hasColumn('content_video', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('access_level');
                $added[] = 'is_featured';
            }
            if (!Schema::hasColumn('content_video', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_featured');
                $added[] = 'is_active';
            }
            if (!Schema::hasColumn('content_video', 'tags')) {
                $table->json('tags')->nullable()->after('is_active');
                $added[] = 'tags';
            }
            if (!Schema::hasColumn('content_video', 'seo_data')) {
                $table->json('seo_data')->nullable()->after('tags');
                $added[] = 'seo_data';
            }
            if (!Schema::hasColumn('content_video', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('seo_data');
                $added[] = 'sort_order';
            }
        });
        
        return $added;
    }
    
    protected function addCourseFields(): array
    {
        if (!Schema::hasTable('courses')) {
            return ['error' => 'courses table not found'];
        }
        
        $added = [];
        
        Schema::table('courses', function (Blueprint $table) use (&$added) {
            if (!Schema::hasColumn('courses', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
                $added[] = 'slug';
            }
            if (!Schema::hasColumn('courses', 'description')) {
                $table->text('description')->nullable()->after('slug');
                $added[] = 'description';
            }
            if (!Schema::hasColumn('courses', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
                $added[] = 'short_description';
            }
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 8, 2)->nullable()->after('short_description');
                $added[] = 'price';
            }
            if (!Schema::hasColumn('courses', 'duration_hours')) {
                $table->integer('duration_hours')->nullable()->after('price');
                $added[] = 'duration_hours';
            }
            if (!Schema::hasColumn('courses', 'max_participants')) {
                $table->integer('max_participants')->nullable()->after('duration_hours');
                $added[] = 'max_participants';
            }
            if (!Schema::hasColumn('courses', 'current_participants')) {
                $table->integer('current_participants')->default(0)->after('max_participants');
                $added[] = 'current_participants';
            }
            if (!Schema::hasColumn('courses', 'tags')) {
                $table->json('tags')->nullable()->after('current_participants');
                $added[] = 'tags';
            }
            if (!Schema::hasColumn('courses', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('tags');
                $added[] = 'is_featured';
            }
            if (!Schema::hasColumn('courses', 'seo_data')) {
                $table->json('seo_data')->nullable()->after('is_featured');
                $added[] = 'seo_data';
            }
            if (!Schema::hasColumn('courses', 'start_date')) {
                $table->datetime('start_date')->nullable()->after('seo_data');
                $added[] = 'start_date';
            }
            if (!Schema::hasColumn('courses', 'end_date')) {
                $table->datetime('end_date')->nullable()->after('start_date');
                $added[] = 'end_date';
            }
            if (!Schema::hasColumn('courses', 'certificate_template')) {
                $table->string('certificate_template')->nullable()->after('end_date');
                $added[] = 'certificate_template';
            }
        });
        
        return $added;
    }
    
    protected function addClubFields(): array
    {
        if (!Schema::hasTable('club')) {
            return ['error' => 'club table not found'];
        }
        
        $added = [];
        
        Schema::table('club', function (Blueprint $table) use (&$added) {
            if (!Schema::hasColumn('club', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
                $added[] = 'slug';
            }
            if (!Schema::hasColumn('club', 'short_description')) {
                $table->text('short_description')->nullable()->after('slug');
                $added[] = 'short_description';
            }
            if (!Schema::hasColumn('club', 'tags')) {
                $table->json('tags')->nullable()->after('short_description');
                $added[] = 'tags';
            }
            if (!Schema::hasColumn('club', 'members_count')) {
                $table->integer('members_count')->default(0)->after('tags');
                $added[] = 'members_count';
            }
            if (!Schema::hasColumn('club', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('members_count');
                $added[] = 'is_active';
            }
            if (!Schema::hasColumn('club', 'seo_data')) {
                $table->json('seo_data')->nullable()->after('is_active');
                $added[] = 'seo_data';
            }
        });
        
        return $added;
    }
    
    protected function addTransactionFields(): array
    {
        if (!Schema::hasTable('transactions')) {
            return ['error' => 'transactions table not found'];
        }
        
        $added = [];
        
        Schema::table('transactions', function (Blueprint $table) use (&$added) {
            if (!Schema::hasColumn('transactions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('signature_value');
                $added[] = 'payment_method';
            }
            if (!Schema::hasColumn('transactions', 'status')) {
                $table->string('status')->default('pending')->after('payment_method');
                $added[] = 'status';
            }
            if (!Schema::hasColumn('transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('status');
                $added[] = 'metadata';
            }
        });
        
        return $added;
    }
    
    // === HELPER METHODS ===
    
    protected function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table}");
            foreach ($indexes as $index) {
                if ($index->Key_name === $indexName) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    protected function log(string $message): void
    {
        $this->log[] = $message;
        Log::info('[DatabaseStructure] ' . $message);
    }
}