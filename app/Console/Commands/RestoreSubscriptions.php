<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPays;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RestoreSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:restore {--revert : Revert the last restoration from a backup} {--force : Force restore or revert without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores user subscriptions from payment history or reverts the last restoration.';

    /**
     * The backup file path.
     *
     * @var string
     */
    protected $backupPath = 'backups/subscriptions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('revert')) {
            return $this->revertSubscriptions();
        }

        return $this->restoreSubscriptions();
    }

    /**
     * Restore subscriptions based on the payment history.
     */
    protected function restoreSubscriptions()
    {
        // Если передан --force, не спрашиваем подтверждение
        if (!$this->option('force')) {
            if (!$this->confirm('This will restore subscriptions based on payment history. A backup will be created first. Do you wish to continue?')) {
                $this->comment('Operation cancelled.');
                return 1;
            }
        }

        $this->info('Starting subscription restoration...');
        
        // 1. Backup current subscriptions
        $this->backupCurrentState();

        // 2. Get the latest successful purchase for each user
        $this->info('Fetching latest purchases from history...');
        $latestPurchases = SubscriptionPays::where('action', 'buy')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('user_id');

        if ($latestPurchases->isEmpty()) {
            $this->warn('No purchase history found. Nothing to restore.');
            return 1;
        }

        $this->info($latestPurchases->count() . ' unique user purchases found.');
        $progressBar = $this->output->createProgressBar($latestPurchases->count());
        $restoredCount = 0;
        $errorCount = 0;

        DB::beginTransaction();
        try {
            foreach ($latestPurchases as $purchase) {
                $subscription = Subscription::where('user_id', $purchase->user_id)->first();
                $product = Product::find($purchase->product_id);

                if (!$subscription || !$product) {
                    Log::warning("RestoreSubscriptions: Could not find subscription or product for user_id: {$purchase->user_id}");
                    $errorCount++;
                    continue;
                }

                $subscription->level = $product->level;
                $subscription->expired_at = null; // Make it indefinite
                $subscription->is_active = 1;
                $subscription->save();

                $restoredCount++;
                $progressBar->advance();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred during restoration. The transaction has been rolled back.');
            $this->error($e->getMessage());
            Log::error('Subscription Restoration Failed: ' . $e->getMessage());
            return 1;
        }
        
        $progressBar->finish();
        $this->newLine(2);

        $this->info("Restoration complete!");
        $this->line("- Total subscriptions restored: <info>$restoredCount</info>");
        $this->line("- Errors (skipped records): <error>$errorCount</error>");
        
        return 0;
    }

    /**
     * Revert subscriptions from the latest backup file.
     */
    protected function revertSubscriptions()
    {
        $this->info('Looking for the latest backup file...');
        $latestBackup = $this->getLatestBackup();

        if (!$latestBackup) {
            $this->error('No backup file found. Cannot revert.');
            return 1;
        }

        // Если передан --force, не спрашиваем подтверждение
        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to revert subscriptions using backup: '{$latestBackup}'? This will overwrite the current state.")) {
                $this->comment('Operation cancelled.');
                return 1;
            }
        }

        $this->info("Reverting subscriptions from '{$latestBackup}'...");

        $backupData = json_decode(Storage::get($latestBackup), true);

        if (empty($backupData)) {
            $this->error('Backup file is empty or corrupted. Aborting.');
            return 1;
        }
        
        $progressBar = $this->output->createProgressBar(count($backupData));
        
        DB::beginTransaction();
        try {
            // We can truncate to be sure, but update/create is safer
            // Subscription::truncate(); 
            foreach($backupData as $subData) {
                Subscription::updateOrCreate(['id' => $subData['id']], $subData);
                $progressBar->advance();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred during revert. The transaction has been rolled back.');
            $this->error($e->getMessage());
            Log::error('Subscription Revert Failed: ' . $e->getMessage());
            return 1;
        }

        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Revert complete. The 'subscriptions' table has been restored from the backup.");

        return 0;
    }

    protected function backupCurrentState()
    {
        $this->comment('Backing up current subscriptions table...');
        $subscriptions = Subscription::all()->toJson(JSON_PRETTY_PRINT);
        $timestamp = Carbon::now()->format('Y-m-d_His');
        $filename = "{$this->backupPath}/subscriptions_backup_{$timestamp}.json";
        
        Storage::put($filename, $subscriptions);
        
        $this->info("Backup created successfully: <comment>$filename</comment>");
    }

    protected function getLatestBackup()
    {
        $files = Storage::files($this->backupPath);
        if (empty($files)) {
            return null;
        }
        // Sort files by name to get the latest timestamp
        rsort($files);
        return $files[0] ?? null;
    }
}