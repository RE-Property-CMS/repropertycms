<?php

namespace App\Providers;

use App\Models\Agents;
use App\Models\Plan;
use App\Models\Properties;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

/**
 * Demo branch only.
 * Registers global Eloquent scopes that isolate each demo session's data.
 * When session('demo_session_id') is set, every query on the scoped models
 * is automatically filtered to that session token only.
 */
class DemoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Agents::addGlobalScope('demo_isolation', function (Builder $builder) {
            if ($token = session('demo_session_id')) {
                $builder->where('agents.demo_session_id', $token);
            }
        });

        Properties::addGlobalScope('demo_isolation', function (Builder $builder) {
            if ($token = session('demo_session_id')) {
                $builder->where('properties.demo_session_id', $token);
            }
        });

        Plan::addGlobalScope('demo_isolation', function (Builder $builder) {
            if ($token = session('demo_session_id')) {
                $builder->where('plans.demo_session_id', $token);
            }
        });

        Subscription::addGlobalScope('demo_isolation', function (Builder $builder) {
            if ($token = session('demo_session_id')) {
                $builder->where('subscriptions.demo_session_id', $token);
            }
        });
    }
}
