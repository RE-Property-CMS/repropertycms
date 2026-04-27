<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemoCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $leadName,
        public string $token,
        public string $adminEmail,
        public string $agentEmail,
        public string $password,
        public string $duration = '60 minutes',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ' . config('app.name') . ' Demo Access',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.demo-credentials',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
