<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentSubscriptionExpired extends Notification
{
    use Queueable;

    protected $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
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
            ->subject('Your '.config('app.name').' Subscription has been Cancelled')
            ->line('Your subscription to '.config('app.name').' has been cancelled.')
            ->action('Renew Your Subscription', route('agent.billing'))
            ->line('Thank you for using '.config('app.name').'!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Subscription Expired',
            'message' => 'Your subscription to '.$this->subscription->metadata->plan.' has expired.',
            'icon' => 'clock',  // Choose an appropriate icon
            'icon_color' => 'text-red-500', // Choose an appropriate color
            'url' => route('agent.billing'), // Replace with the correct URL to your billing or renewal page
        ];
    }
}
