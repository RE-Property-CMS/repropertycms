<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class RunSetupMigrationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 120;

    public function __construct(private string $statusFile) {}

    public function handle(): void
    {
        try {
            $this->write([
                'status'  => 'running',
                'message' => 'Running migrations...',
            ]);

            $buffer = new \Symfony\Component\Console\Output\BufferedOutput();
            Artisan::call('migrate', ['--force' => true], $buffer);
            $output   = $buffer->fetch();
            $migrated = substr_count($output, 'Migrating:');

            $this->write([
                'status'       => 'done',
                'migrated'     => $migrated,
                'message'      => "Completed — {$migrated} migrations ran.",
                'completed_at' => now()->toISOString(),
            ]);

        } catch (Throwable $e) {
            $this->write([
                'status'  => 'failed',
                'error'   => $e->getMessage(),
                'message' => 'Migration failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function failed(Throwable $e): void
    {
        $this->write([
            'status'  => 'failed',
            'error'   => $e->getMessage(),
            'message' => 'Job failed: ' . $e->getMessage(),
        ]);
    }

    private function write(array $data): void
    {
        file_put_contents($this->statusFile, json_encode($data));
    }
}
