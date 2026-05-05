<?php

namespace App\Jobs;

use App\Mail\DemoCredentialsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDemoCredentialsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly string $email,
        public readonly string $leadName,
        public readonly string $token,
        public readonly string $adminEmail,
        public readonly string $agentEmail,
        public readonly string $password,
        public readonly string $duration,
    ) {}

    public function handle(): void
    {
        Mail::to($this->email)->send(new DemoCredentialsMail(
            leadName:   $this->leadName,
            token:      $this->token,
            adminEmail: $this->adminEmail,
            agentEmail: $this->agentEmail,
            password:   $this->password,
            duration:   $this->duration,
        ));
    }
}
