<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DemoWizardController extends Controller
{
    public function requirements(): View
    {
        $requirements = [
            'php_version' => [
                'required' => '8.1+',
                'current'  => PHP_VERSION,
                'status'   => version_compare(PHP_VERSION, '8.1.0', '>='),
            ],
            'openssl'          => extension_loaded('openssl'),
            'pdo'              => extension_loaded('pdo'),
            'mbstring'         => extension_loaded('mbstring'),
            'curl'             => extension_loaded('curl'),
            'fileinfo'         => extension_loaded('fileinfo'),
            'storage_writable' => is_writable(storage_path()),
            'cache_writable'   => is_writable(base_path('bootstrap/cache')),
            'env_writable'     => is_writable(base_path('.env')),
        ];

        return view('demo.wizard.requirements', compact('requirements'));
    }

    public function database(): View
    {
        return view('demo.wizard.database');
    }

    public function admin(): View
    {
        return view('demo.wizard.admin');
    }

    public function mail(): View
    {
        return view('demo.wizard.mail');
    }

    public function stripe(): View
    {
        return view('demo.wizard.stripe');
    }

    public function storage(): View
    {
        return view('demo.wizard.storage');
    }

    public function captcha(): View
    {
        return view('demo.wizard.captcha');
    }

    public function branding(): View
    {
        return view('demo.wizard.branding');
    }

    public function complete(): View
    {
        return view('demo.wizard.complete');
    }

    public function finish()
    {
        session(['demo_wizard_shown' => true]);
        return redirect()->route('admin.dashboard');
    }
}
