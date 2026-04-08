<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\AgentUnpublishProperty;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UnpublishExpiredAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agents:unpublish-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unpublish properties of agents with expired subscriptions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredSubscriptions = Subscription::where('current_period_end', '<', Carbon::now())
            ->get();
        foreach ($expiredSubscriptions as $subscription) {
            $agent = $subscription->agent;
            $agent->properties()->update([
                'published' => 0,
            ]);

//            $agent->notify(new AgentUnpublishProperty);
        }
    }
}
