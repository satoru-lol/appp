<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataMigrationService;

class TestMigration extends Command
{
    protected $signature = 'test:migration {--type=users}';
    protected $description = 'Test data migration functionality';

    public function handle()
    {
        $type = $this->option('type');
        
        $this->info("🔄 Testing Data Migration");
        $this->info("Type: {$type}");
        
        try {
            $migrationService = app(DataMigrationService::class);
            
            $this->info("✅ DataMigrationService loaded successfully");
            
            // Тестируем метод
            switch ($type) {
                case 'users':
                    $result = $migrationService->migrateUsers();
                    break;
                case 'all':
                    $result = $migrationService->migrateAllData();
                    break;
                default:
                    $this->error("Unknown type: {$type}");
                    return 1;
            }
            
            $this->info("📊 Migration Results:");
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            $this->info("🎉 Test completed successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("📍 File: " . $e->getFile() . " Line: " . $e->getLine());
            return 1;
        }
        
        return 0;
    }
}