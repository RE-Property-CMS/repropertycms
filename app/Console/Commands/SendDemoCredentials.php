<?php

namespace App\Console\Commands;

use App\Mail\DemoCredentialsMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDemoCredentials extends Command
{
    protected $signature = 'demo:send-credentials {payload}';
    protected $description = 'Send demo credentials email (called as background process)';

    public function handle(): void
    {
        $data = json_decode(base64_decode($this->argument('payload')), true);

        if (!$data) {
            $this->error('Invalid payload.');
            return;
        }

        try {
            Mail::to($data['email'])->send(new DemoCredentialsMail(
                leadName:   $data['leadName'],
                token:      $data['token'],
                adminEmail: $data['adminEmail'],
                agentEmail: $data['agentEmail'],
                password:   $data['password'],
                duration:   $data['duration'],
            ));
            $this->info('Demo credentials sent to ' . $data['email']);
        } catch (\Throwable $e) {
            $this->error('Failed: ' . $e->getMessage());
        }
    }
}
