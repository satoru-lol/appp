<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataMigrationService;

class MigrateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate {--type=all : Type of migration} {--dry-run : Run without making changes} {--force : Force migration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from old format to new refactored architecture';

    /**
     * Execute the console command.
     */
    public function handle(DataMigrationService $migrationService)
    {
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔄 Data Migration Tool');
        $this->info('====================');
        
        if ($dryRun) {
            $this->warn('🧪 DRY RUN MODE - No changes will be made');
        }

        if (!$force && !$dryRun) {
            if (!$this->confirm('This will modify your database. Are you sure you want to continue?')) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        $this->info("📊 Starting migration: {$type}");
        
        if ($dryRun) {
            $this->simulateMigration($type);
            return 0;
        }

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
                    $this->error("Unknown migration type: {$type}");
                    return 1;
            }

            $this->displayResults($result);
            
            if (isset($result['log'])) {
                $this->info("\n📝 Migration Log:");
                foreach ($result['log'] as $logEntry) {
                    $this->line($logEntry);
                }
            }

            $this->info("\n🎉 Migration completed successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Migration failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function simulateMigration(string $type): void
    {
        $this->info("🧪 Simulating migration: {$type}");
        
        // Подсчитываем что будет мигрировано
        $counts = $this->getDataCounts();
        
        $this->table(
            ['Data Type', 'Records Found', 'Action'],
            [
                ['Users', $counts['users'], 'Add slugs, bio, preferences'],
                ['Blogs/Meetings', $counts['blogs'], 'Add slugs, SEO, tags, counters'],
                ['Videos', $counts['videos'], 'Add slugs, descriptions, SEO'],
                ['Courses', $counts['courses'], 'Add slugs, prices, durations'],
                ['Clubs', $counts['clubs'], 'Add slugs, SEO, member counts'],
                ['Participants', $counts['participants'], 'Update counters in related tables'],
                ['Transactions', $counts['transactions'], 'Normalize status, add metadata'],
            ]
        );
        
        $this->warn("This is a DRY RUN. No actual changes would be made.");
    }

    protected function getDataCounts(): array
    {
        return [
            'users' => \App\Models\User::count(),
            'blogs' => \App\Models\Blog::count(),
            'videos' => \App\Models\Videos::count(),
            'courses' => \App\Models\Course::count(),
            'clubs' => \App\Models\Club::count(),
            'participants' => \DB::table('participant_actions')->count(),
            'transactions' => \DB::table('transactions')->count(),
        ];
    }

    protected function displayResults(array $result): void
    {
        $this->info("\n📊 Migration Results:");
        
        if (isset($result['results'])) {
            // Полная миграция
            foreach ($result['results'] as $type => $typeResult) {
                $this->displayTypeResult($type, $typeResult);
            }
        } else {
            // Одиночная миграция
            foreach ($result as $type => $typeResult) {
                $this->displayTypeResult($type, $typeResult);
            }
        }
    }

    protected function displayTypeResult(string $type, array $typeResult): void
    {
        $icon = $this->getTypeIcon($type);
        
        if (isset($typeResult['error'])) {
            $this->error("{$icon} {$type}: FAILED - " . $typeResult['error']);
            return;
        }

        $updated = $typeResult['updated'] ?? $typeResult['migrated'] ?? 0;
        $errors = count($typeResult['errors'] ?? []);
        
        if ($errors > 0) {
            $this->warn("{$icon} {$type}: {$updated} updated, {$errors} errors");
            
            // Показываем первые несколько ошибок
            foreach (array_slice($typeResult['errors'], 0, 3) as $error) {
                $this->line("  ⚠️  " . $error);
            }
            
            if (count($typeResult['errors']) > 3) {
                $remaining = count($typeResult['errors']) - 3;
                $this->line("  ... and {$remaining} more errors");
            }
        } else {
            $this->info("{$icon} {$type}: {$updated} records updated successfully");
        }
    }

    protected function getTypeIcon(string $type): string
    {
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
}