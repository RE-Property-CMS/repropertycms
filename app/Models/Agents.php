<?php

namespace App\Models;

use App\Notifications\CustomResetPasswordNotification;
use App\Traits\ManagesStripeCustomers;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmailNotification;

class Agents extends Authenticatable implements MustVerifyEmail
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    use HasApiTokens, Notifiable;

    use ManagesStripeCustomers;

    protected $table = 'agents';

    protected $primarykey = 'id';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function properties()
    {
        return $this->hasMany(Properties::class, 'agent_id', 'id');
    }

    public function publishedProperties()
    {
        return $this->hasMany(Properties::class, 'agent_id')->where('published', 1);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'agent_id');
    }

    public function agent_address()
    {
        return $this->hasOne(Agent_addresses::class, 'agent_id', 'id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'agent_id')
            ->where(function ($query) {
                $query->whereNull('current_period_end')
                    ->orWhere('current_period_end', '>', now());
            })
            ->orderByDesc('created_at') // Order by descending to prioritize newer subscriptions
            ->limit(1); // Limit to the single most recent active subscription
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('stripe_status', 'active') // Or other relevant statuses like 'trialing'
            ->where('current_period_end', '>', now())
            ->exists();
    }

    public function getTotalPublishedPropertiesCountAttribute()
    {
        return $this->properties()->where('published', 1)->count();
    }

    public function sendCustomEmailVerificationNotification($agent = null)
    {
        $this->notify(new CustomVerifyEmailNotification($agent));
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}
