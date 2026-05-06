<?php

namespace App\Jobs;

use App\Mail\DemoCredentialsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDemoCredentialsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

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
        Log::channel('stack')->info('[DemoCredentialsJob] Starting', [
            'to'         => $this->email,
            'lead'       => $this->leadName,
            'token'      => $this->token,
            'attempt'    => $this->attempts(),
            'mail_host'  => config('mail.mailers.smtp.host'),
            'mail_port'  => config('mail.mailers.smtp.port'),
            'mail_from'  => config('mail.from.address'),
            'mail_driver'=> config('mail.default'),
        ]);

        try {
            Mail::to($this->email)->send(new DemoCredentialsMail(
                leadName:   $this->leadName,
                token:      $this->token,
                adminEmail: $this->adminEmail,
                agentEmail: $this->agentEmail,
                password:   $this->password,
                duration:   $this->duration,
            ));

            Log::channel('stack')->info('[DemoCredentialsJob] Email sent successfully', [
                'to'    => $this->email,
                'token' => $this->token,
            ]);
        } catch (Throwable $e) {
            Log::channel('stack')->error('[DemoCredentialsJob] Mail::send threw an exception', [
                'to'        => $this->email,
                'attempt'   => $this->attempts(),
                'exception' => $e->getMessage(),
                'file'      => $e->getFile() . ':' . $e->getLine(),
            ]);

            throw $e; // re-throw so Laravel retries the job
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::channel('stack')->error('[DemoCredentialsJob] Job permanently failed after all retries', [
            'to'        => $this->email,
            'token'     => $this->token,
            'exception' => $exception->getMessage(),
            'file'      => $exception->getFile() . ':' . $exception->getLine(),
            'trace'     => $exception->getTraceAsString(),
        ]);
    }
}
