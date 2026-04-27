<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupRunMigrations extends Command
{
    protected $signature = 'setup:run-migrations {statusFile}';
    protected $description = 'Run migrations during setup wizard and write result to a status file';

    public function handle(): void
    {
        $statusFile = $this->argument('statusFile');

        try {
            Artisan::call('migrate', ['--force' => true]);

            file_put_contents($statusFile, json_encode([
                'status'       => 'done',
                'completed_at' => now()->toISOString(),
            ]));
        } catch (\Throwable $e) {
            file_put_contents($statusFile, json_encode([
                'status'    => 'failed',
                'error'     => $e->getMessage(),
                'failed_at' => now()->toISOString(),
            ]));
        }
    }
}
