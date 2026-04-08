<?php

namespace App\Console\Commands;

use App\Models\Properties;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DemoCron extends Command
{
    protected $signature = 'demo:cron';
    protected $description = 'Unpublish expired properties and notify agents';

    public function handle()
    {
        $now = Carbon::now();
        Log::info("DemoCron started at: {$now}");

        Properties::whereDate('expiry_date', '<', $now)
            ->where('published', 1)
            ->with('agentRelation:id,email')
            ->chunk(50, function ($properties) use ($now) {
                Log::info("Processing chunk of properties", ['count' => $properties->count()]);

                $grouped = $properties->groupBy('agent_id');

                foreach ($grouped as $agentId => $props) {
                    $agent = $props->first()->agentRelation;

                    if (!$agent) {
                        Log::warning("Agent not found for agent_id={$agentId}");
                        continue;
                    }

                    Log::info("Processing agent={$agentId}, email={$agent->email}, properties=" . $props->count());

                    $mailData = [];

                    foreach ($props as $property) {
                        Log::info("Unpublishing property", [
                            'property_id' => $property->id,
                            'name'        => $property->name,
                            'expiry_date' => $property->expiry_date,
                            'publish_date'=> $property->publish_date,
                        ]);

                        $property->update(['published' => 0]);

                        $mailData[] = [
                            'name'       => $property->name,
                            'desc'       => $property->description,
                            'exp_date'   => $property->expiry_date,
                            'pub_date'   => $property->publish_date,
                        ];
                    }

                    //  Optional: Send email notification (kept commented intentionally)
                    /*
                    Mail::send('mail.message', ['props' => $mailData], function ($message) use ($agent) {
                        $message->to($agent->email);
                        $message->subject('Expired Properties');
                    });
                    */
                }
            });

        Log::info("DemoCron finished successfully at: " . Carbon::now());

        $this->info('DemoCron executed successfully at ' . now());
        return Command::SUCCESS;
    }
}
