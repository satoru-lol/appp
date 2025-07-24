<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AvatarService;

class MigrateAvatars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avatars:migrate {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old avatar system to new database-based system';

    /**
     * Execute the console command.
     */
    public function handle(AvatarService $avatarService)
    {
        $this->info('Starting avatar migration...');
        
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        if (!$this->option('dry-run')) {
            $result = $avatarService->migrateOldAvatars();
            
            $this->info("Successfully migrated {$result['migrated']} avatars");
            
            if (!empty($result['errors'])) {
                $this->error('Errors occurred:');
                foreach ($result['errors'] as $error) {
                    $this->error($error);
                }
            }
        } else {
            // Подсчитываем файлы для миграции
            $users = \App\Models\User::whereNull('avatar')->get();
            $count = 0;
            
            foreach ($users as $user) {
                $pattern = public_path('img/avatars/') . md5($user->id . $user->phone) . '.*';
                $files = glob($pattern);
                
                if (!empty($files) && file_exists($files[0])) {
                    $count++;
                    $this->line("Would migrate: User {$user->id} - {$user->firstname} {$user->lastname}");
                }
            }
            
            $this->info("Found {$count} avatars to migrate");
        }
        
        $this->info('Migration completed!');
    }
}