<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\DataMigrationService;
use App\Services\DatabaseStructureService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Команда миграции данных
Artisan::command('data:migrate {--type=all} {--dry-run} {--force}', function () {
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
    
    try {
        $migrationService = app(DataMigrationService::class);
        
        if ($dryRun) {
            $this->info("🧪 Would migrate {$type} data (dry run)");
            return 0;
        }

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

        $this->info("🎉 Migration completed successfully!");
        
    } catch (\Exception $e) {
        $this->error("❌ Migration failed: " . $e->getMessage());
        return 1;
    }

    return 0;
})->purpose('Migrate data from old format to new refactored architecture');

// Команда миграции аватаров
Artisan::command('avatars:migrate {--dry-run}', function () {
    $dryRun = $this->option('dry-run');
    
    $this->info('Starting avatar migration...');
    
    if ($dryRun) {
        $this->warn('DRY RUN MODE - No changes will be made');
        // Логика dry-run
        $this->info('Would migrate avatars (dry run)');
        return 0;
    }

    try {
        $avatarService = app(\App\Services\AvatarService::class);
        $result = $avatarService->migrateOldAvatars();
        
        $this->info("Successfully migrated {$result['migrated']} avatars");
        
        if (!empty($result['errors'])) {
            $this->error('Errors occurred:');
            foreach ($result['errors'] as $error) {
                $this->error($error);
            }
        }
    } catch (\Exception $e) {
        $this->error('Migration failed: ' . $e->getMessage());
        return 1;
    }
    
    $this->info('Migration completed!');
    return 0;
})->purpose('Migrate old avatar system to new database-based system');

// Команда адаптации существующей БД
Artisan::command('db:adapt {--dry-run}', function () {
    $dryRun = $this->option('dry-run');
    
    $this->info('🔧 Database Structure Adaptation');
    $this->info('=================================');
    
    if ($dryRun) {
        $this->warn('🧪 DRY RUN MODE - No changes will be made');
        $this->info('Would adapt existing database structure');
        return 0;
    }

    try {
        $structureService = app(DatabaseStructureService::class);
        $result = $structureService->adaptExistingDatabase();
        
        $this->info('📊 Adaptation Results:');
        
        foreach ($result['results'] as $category => $categoryResult) {
            $this->info("  {$category}:");
            if (is_array($categoryResult)) {
                foreach ($categoryResult as $item => $status) {
                    $this->line("    {$item}: {$status}");
                }
            } else {
                $this->line("    {$categoryResult}");
            }
        }
        
        if (isset($result['log'])) {
            $this->info("\n📝 Adaptation Log:");
            foreach ($result['log'] as $logEntry) {
                $this->line($logEntry);
            }
        }
        
        $this->info("\n🎉 Database adaptation completed successfully!");
        
    } catch (\Exception $e) {
        $this->error("❌ Adaptation failed: " . $e->getMessage());
        return 1;
    }
    
    return 0;
})->purpose('Adapt existing database dump to new refactored architecture');
