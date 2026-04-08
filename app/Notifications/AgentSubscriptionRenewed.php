<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentSubscriptionRenewed extends Notification
{
    use Queueable;

    protected $subscription;
    protected $agent;

    /**
     * Create a new notification instance.
     */
    public function __construct($subscription, $agent)
    {
        $this->subscription = $subscription;
        $this->agent = $agent;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your '.config('app.name').' Subscription Renewed!')
            ->view('mail.admin_subscription_renewal', ['agent' => $this->agent,
                'renewalDate' => Carbon::parse($this->subscription->current_period_end)->format('d M Y')]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Subscription Renewed!',
            'message' => 'Your subscription to '.config('app.name').' has been renewed.',
            'icon' => 'heroicon-o-gift', // Or any icon you prefer
            'icon_color' => 'text-green-500', // Choose a suitable color
            'url' => route('agent.billing'),  // Link to billing or dashboard
        ];
    }
}
