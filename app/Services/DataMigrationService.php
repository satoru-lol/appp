<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Blog;
use App\Models\Course;
use App\Models\Videos;
use App\Models\Club;

class DataMigrationService
{
    protected $migrationLog = [];
    
    /**
     * Выполнить полную миграцию данных
     */
    public function migrateAllData(): array
    {
        $this->log('🚀 Starting full data migration...');
        
        $results = [
            'users' => $this->migrateUsers(),
            'blogs' => $this->migrateBlogs(),
            'videos' => $this->migrateVideos(),
            'courses' => $this->migrateCourses(),
            'clubs' => $this->migrateClubs(),
            'participants' => $this->migrateParticipants(),
            'transactions' => $this->migrateTransactions(),
        ];
        
        $this->log('🎉 Full migration completed!');
        
        return [
            'success' => true,
            'results' => $results,
            'log' => $this->migrationLog
        ];
    }
    
    /**
     * Мигрировать пользователей
     */
    public function migrateUsers(): array
    {
        $this->log('👤 Migrating users data...');
        
        try {
            $updated = 0;
            $errors = [];
            
            // Получаем всех пользователей
            $users = User::all();
            
            foreach ($users as $user) {
                try {
                    // Генерируем slug для профиля если нет
                    if (empty($user->slug)) {
                        $user->slug = $this->generateUniqueSlug($user->firstname . ' ' . $user->lastname, 'users');
                    }
                    
                    // Добавляем bio если пустое
                    if (empty($user->bio)) {
                        $user->bio = "Участник платформы с " . $user->created_at->format('Y') . " года";
                    }
                    
                    // Устанавливаем preferences по умолчанию
                    if (empty($user->preferences)) {
                        $user->preferences = [
                            'notifications' => [
                                'email' => true,
                                'sms' => false,
                                'push' => true
                            ],
                            'privacy' => [
                                'show_email' => false,
                                'show_phone' => false,
                                'show_profile' => true
                            ],
                            'display' => [
                                'theme' => 'light',
                                'language' => 'ru'
                            ]
                        ];
                    }
                    
                    $user->save();
                    $updated++;
                    
                } catch (\Exception $e) {
                    $errors[] = "User {$user->id}: " . $e->getMessage();
                }
            }
            
            $this->log("✅ Users migrated: {$updated} updated, " . count($errors) . " errors");
            
            return [
                'updated' => $updated,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            $this->log("❌ Users migration failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Мигрировать блоги/встречи
     */
    public function migrateBlogs(): array
    {
        $this->log('📝 Migrating blogs/meetings data...');
        
        try {
            $updated = 0;
            $errors = [];
            
            $blogs = Blog::all();
            
            foreach ($blogs as $blog) {
                try {
                    // Генерируем slug
                    if (empty($blog->slug)) {
                        $blog->slug = $this->generateUniqueSlug($blog->name, 'blogs');
                    }
                    
                    // Добавляем короткое описание
                    if (empty($blog->short_description)) {
                        $content = $this->getBlogContent($blog->blog_content_id);
                        $blog->short_description = Str::limit(strip_tags($content), 200);
                    }
                    
                    // Устанавливаем теги
                    if (empty($blog->tags)) {
                        $tags = [];
                        if ($blog->is_meeting) {
                            $tags[] = 'встреча';
                        }
                        if ($blog->format_id) {
                            $format = $this->getMeetingFormat($blog->format_id);
                            if ($format) {
                                $tags[] = $format;
                            }
                        }
                        $blog->tags = $tags;
                    }
                    
                    // SEO данные
                    if (empty($blog->seo_data)) {
                        $blog->seo_data = [
                            'title' => $blog->name,
                            'description' => $blog->short_description,
                            'keywords' => implode(', ', $blog->tags ?? []),
                            'og_image' => $blog->image
                        ];
                    }
                    
                    // Подсчитываем статистику
                    $blog->likes_count = $this->countLikes('blog', $blog->id);
                    $blog->dislikes_count = $this->countDislikes('blog', $blog->id);
                    $blog->comments_count = $this->countComments($blog->id);
                    $blog->participants_count = $this->countParticipants('blog', $blog->id);
                    
                    $blog->save();
                    $updated++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Blog {$blog->id}: " . $e->getMessage();
                }
            }
            
            $this->log("✅ Blogs migrated: {$updated} updated, " . count($errors) . " errors");
            
            return [
                'updated' => $updated,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            $this->log("❌ Blogs migration failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Мигрировать видео
     */
    public function migrateVideos(): array
    {
        $this->log('🎥 Migrating videos data...');
        
        try {
            $updated = 0;
            $errors = [];
            
            $videos = Videos::all();
            
            foreach ($videos as $video) {
                try {
                    // Генерируем slug
                    if (empty($video->slug)) {
                        $video->slug = $this->generateUniqueSlug($video->title, 'content_video');
                    }
                    
                    // Добавляем описание если нет
                    if (empty($video->description)) {
                        $video->description = "Видео материал: " . $video->title;
                    }
                    
                    // Извлекаем длительность из URL если возможно
                    if (empty($video->duration)) {
                        $video->duration = $this->extractVideoDuration($video->url);
                    }
                    
                    // SEO данные
                    if (empty($video->seo_data)) {
                        $video->seo_data = [
                            'title' => $video->title,
                            'description' => $video->description,
                            'keywords' => 'видео, обучение, психология',
                            'og_image' => $video->thumbnail
                        ];
                    }
                    
                    // Теги по категории
                    if (empty($video->tags)) {
                        $category = $this->getCategoryName($video->category_id);
                        $video->tags = [$category, 'видео', 'обучение'];
                    }
                    
                    $video->save();
                    $updated++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Video {$video->id}: " . $e->getMessage();
                }
            }
            
            $this->log("✅ Videos migrated: {$updated} updated, " . count($errors) . " errors");
            
            return [
                'updated' => $updated,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            $this->log("❌ Videos migration failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Мигрировать курсы
     */
    public function migrateCourses(): array
    {
        $this->log('📚 Migrating courses data...');
        
        try {
            $updated = 0;
            $errors = [];
            
            $courses = Course::all();
            
            foreach ($courses as $course) {
                try {
                    // Генерируем slug
                    if (empty($course->slug)) {
                        $course->slug = $this->generateUniqueSlug($course->title, 'courses');
                    }
                    
                    // Добавляем описания
                    if (empty($course->description)) {
                        $course->description = "Курс: " . $course->title;
                        if ($course->speakers) {
                            $course->description .= "\nВедущие: " . $course->speakers;
                        }
                    }
                    
                    if (empty($course->short_description)) {
                        $course->short_description = Str::limit($course->description, 200);
                    }
                    
                    // Парсим время в часы
                    if (empty($course->duration_hours) && $course->times) {
                        $course->duration_hours = $this->parseCourseDuration($course->times);
                    }
                    
                    // Устанавливаем цену по уровню продукта
                    if (empty($course->price)) {
                        $course->price = $this->getPriceByProductLevel($course->product_level);
                    }
                    
                    // SEO данные
                    if (empty($course->seo_data)) {
                        $course->seo_data = [
                            'title' => $course->title,
                            'description' => $course->short_description,
                            'keywords' => 'курс, обучение, психология',
                            'og_image' => $course->image
                        ];
                    }
                    
                    // Теги
                    if (empty($course->tags)) {
                        $tags = ['курс', 'обучение'];
                        if ($course->speakers) {
                            $tags[] = 'с экспертами';
                        }
                        $course->tags = $tags;
                    }
                    
                    // Подсчитываем участников
                    $course->current_participants = $this->countParticipants('course', $course->id);
                    
                    $course->save();
                    $updated++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Course {$course->id}: " . $e->getMessage();
                }
            }
            
            $this->log("✅ Courses migrated: {$updated} updated, " . count($errors) . " errors");
            
            return [
                'updated' => $updated,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            $this->log("❌ Courses migration failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Мигрировать клубы
     */
    public function migrateClubs(): array
    {
        $this->log('🏛️ Migrating clubs data...');
        
        try {
            $updated = 0;
            $errors = [];
            
            $clubs = Club::all();
            
            foreach ($clubs as $club) {
                try {
                    // Генерируем slug
                    if (empty($club->slug)) {
                        $club->slug = $this->generateUniqueSlug($club->name, 'club');
                    }
                    
                    // Добавляем описания
                    if (empty($club->short_description)) {
                        $club->short_description = Str::limit($club->description, 200);
                    }
                    
                    // SEO данные
                    if (empty($club->seo_data)) {
                        $club->seo_data = [
                            'title' => $club->name,
                            'description' => $club->short_description,
                            'keywords' => 'клуб, сообщество, встречи',
                            'og_image' => $club->image
                        ];
                    }
                    
                    // Теги
                    if (empty($club->tags)) {
                        $club->tags = ['клуб', 'сообщество', 'встречи'];
                    }
                    
                    // Подсчитываем участников
                    $club->members_count = $this->countParticipants('club', $club->id);
                    
                    $club->save();
                    $updated++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Club {$club->id}: " . $e->getMessage();
                }
            }
            
            $this->log("✅ Clubs migrated: {$updated} updated, " . count($errors) . " errors");
            
            return [
                'updated' => $updated,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            $this->log("❌ Clubs migration failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Мигрировать данные участников
     */
    public function migrateParticipants(): array
    {
        $this->log('👥 Migrating participants data...');
        
        try {
            $migrated = 0;
            $errors = [];
            
            // Получаем все записи из participant_actions
            $participants = DB::table('participant_actions')->get();
            
            foreach ($participants as $participant) {
                try {
                    // Обновляем счетчики в соответствующих таблицах
                    switch ($participant->object_name) {
                        case 'blog':
                            DB::table('blogs')
                                ->where('id', $participant->object_id)
                                ->increment('participants_count');
                            break;
                            
                        case 'course':
                            DB::table('courses')
                                ->where('id', $participant->object_id)
                                ->increment('current_participants');
                            break;
                            
                        case 'club':
                            DB::table('club')
                                ->where('id', $participant->object_id)
                                ->increment('members_count');
                            break;
                    }
                    
                    $migrated++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Participant {$participant->id}: " . $e->getMessage();
                }
            }
            
            $this->log("✅ Participants migrated: {$migrated} records, " . count($errors) . " errors");
            
            return [
                'migrated' => $migrated,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            $this->log("❌ Participants migration failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Мигрировать транзакции
     */
    public function migrateTransactions(): array
    {
        $this->log('💳 Migrating transactions data...');
        
        try {
            $updated = 0;
            $errors = [];
            
            $transactions = DB::table('transactions')->get();
            
            foreach ($transactions as $transaction) {
                try {
                    // Нормализуем статус
                    $status = $this->normalizeTransactionStatus($transaction->state);
                    
                    // Определяем метод платежа
                    $paymentMethod = $this->determinePaymentMethod($transaction->shop);
                    
                    // Создаем метаданные
                    $metadata = [
                        'original_state' => $transaction->state,
                        'shop' => $transaction->shop,
                        'signature' => $transaction->signature_value,
                        'migrated_at' => now()->toISOString()
                    ];
                    
                    DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'status' => $status,
                            'payment_method' => $paymentMethod,
                            'metadata' => json_encode($metadata)
                        ]);
                    
                    $updated++;
                    
                } catch (\Exception $e) {
                    $errors[] = "Transaction {$transaction->id}: " . $e->getMessage();
                }
            }
            
            $this->log("✅ Transactions migrated: {$updated} updated, " . count($errors) . " errors");
            
            return [
                'updated' => $updated,
                'errors' => $errors
            ];
            
        } catch (\Exception $e) {
            $this->log("❌ Transactions migration failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    // === HELPER METHODS ===
    
    protected function generateUniqueSlug(string $title, string $table): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        
        while (DB::table($table)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    protected function getBlogContent($contentId): string
    {
        if (!$contentId) return '';
        
        $content = DB::table('blog_contents')->where('id', $contentId)->first();
        return $content ? $content->text : '';
    }
    
    protected function getMeetingFormat($formatId): ?string
    {
        if (!$formatId) return null;
        
        $format = DB::table('meeting_format')->where('id', $formatId)->first();
        return $format ? $format->label : null;
    }
    
    protected function countLikes(string $objectType, int $objectId): int
    {
        return DB::table('likes')
            ->where('object_type', $objectType)
            ->where('object_id', $objectId)
            ->count();
    }
    
    protected function countDislikes(string $objectType, int $objectId): int
    {
        return DB::table('dislikes')
            ->where('object_type', $objectType)
            ->where('object_id', $objectId)
            ->count();
    }
    
    protected function countComments(int $blogId): int
    {
        return DB::table('blog_comments')
            ->where('blog_id', $blogId)
            ->where('status', 1)
            ->count();
    }
    
    protected function countParticipants(string $objectName, int $objectId): int
    {
        return DB::table('participant_actions')
            ->where('object_name', $objectName)
            ->where('object_id', $objectId)
            ->count();
    }
    
    protected function extractVideoDuration(string $url): ?int
    {
        // Попытка извлечь длительность из YouTube URL или других источников
        // Возвращает длительность в секундах или null
        return null; // Заглушка - можно реализовать через API
    }
    
    protected function getCategoryName(int $categoryId): string
    {
        $category = DB::table('categories')->where('id', $categoryId)->first();
        return $category ? $category->name : 'Общее';
    }
    
    protected function parseCourseDuration($times): ?int
    {
        if (!$times) return null;
        
        // Парсим JSON с временами курса
        $timesData = is_string($times) ? json_decode($times, true) : $times;
        
        if (!is_array($timesData)) return null;
        
        // Подсчитываем общее количество часов
        return count($timesData);
    }
    
    protected function getPriceByProductLevel(?int $level): ?float
    {
        $prices = [
            1 => 0,      // Бесплатно
            2 => 1000,   // Базовый
            3 => 2000,   // Стандарт
            4 => 3000,   // Премиум
        ];
        
        return $prices[$level] ?? null;
    }
    
    protected function normalizeTransactionStatus(?string $state): string
    {
        $statusMap = [
            'success' => 'completed',
            'pending' => 'pending',
            'failed' => 'failed',
            'cancelled' => 'cancelled',
        ];
        
        return $statusMap[$state] ?? 'pending';
    }
    
    protected function determinePaymentMethod(?string $shop): string
    {
        $methodMap = [
            'robokassa' => 'robokassa',
            'yoomoney' => 'yoomoney',
            'sberbank' => 'sberbank',
        ];
        
        return $methodMap[$shop] ?? 'unknown';
    }
    
    protected function log(string $message): void
    {
        $this->migrationLog[] = $message;
        Log::info('[DataMigration] ' . $message);
    }
}