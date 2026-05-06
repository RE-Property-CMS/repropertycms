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
            $migrator = app('migrator');

            if (!$migrator->repositoryExists()) {
                $migrator->getRepository()->createRepository();
            }

            $files   = $migrator->getMigrationFiles([database_path('migrations')]);
            $ran     = $migrator->getRepository()->getRan();
            $pending = array_values(array_diff(array_keys($files), $ran));
            $total   = count($pending);

            $this->writeStatus($statusFile, [
                'status'  => 'running',
                'total'   => $total,
                'done'    => 0,
                'message' => $total === 0 ? 'No pending migrations.' : "Found {$total} migrations to run.",
            ]);

            if ($total === 0) {
                $this->writeStatus($statusFile, [
                    'status'       => 'done',
                    'total'        => 0,
                    'done'         => 0,
                    'message'      => 'Already up to date.',
                    'completed_at' => now()->toISOString(),
                ]);
                return;
            }

            // Run with buffered output to capture results
            $buffer = new \Symfony\Component\Console\Output\BufferedOutput();
            Artisan::call('migrate', ['--force' => true], $buffer);
            $output = $buffer->fetch();

            // Count migrated from output lines
            $migrated = substr_count($output, 'Migrating:') ?: $total;

            $this->writeStatus($statusFile, [
                'status'       => 'done',
                'total'        => $total,
                'done'         => $migrated,
                'message'      => "{$migrated} of {$total} migrations completed.",
                'completed_at' => now()->toISOString(),
            ]);

        } catch (\Throwable $e) {
            $this->writeStatus($statusFile, [
                'status'    => 'failed',
                'error'     => $e->getMessage(),
                'message'   => 'Migration failed: ' . $e->getMessage(),
                'failed_at' => now()->toISOString(),
            ]);
        }
    }

    private function writeStatus(string $file, array $data): void
    {
        file_put_contents($file, json_encode($data));
    }
}
