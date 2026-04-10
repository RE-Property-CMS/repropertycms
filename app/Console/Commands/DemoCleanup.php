<?php

namespace App\Console\Commands;

use App\Http\Controllers\DemoController;
use App\Models\DemoSession;
use Illuminate\Console\Command;

/**
 * Demo branch only.
 * Deletes all data belonging to expired demo sessions that were never manually ended.
 * Runs every 15 minutes via the scheduler.
 */
class DemoCleanup extends Command
{
    protected $signature   = 'demo:cleanup';
    protected $description = 'Delete data for all expired demo sessions';

    public function handle(): int
    {
        $expired = DemoSession::where('expires_at', '<', now())->get();

        if ($expired->isEmpty()) {
            $this->info('No expired demo sessions to clean up.');
            return self::SUCCESS;
        }

        foreach ($expired as $session) {
            DemoController::purgeSession($session->token);
            $this->line("  Cleaned: {$session->token}");
        }

        $this->info("Cleaned {$expired->count()} expired demo session(s).");
        return self::SUCCESS;
    }
}
