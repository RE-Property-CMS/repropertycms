<?php

namespace App\Http\Controllers;

use App\Models\Backend\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\EnvService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SetupController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STEP 0 — Key Verification (Landing Page)
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        if (session('setup_verified')) {
            return redirect()->route('setup.requirements');
        }
        return view('setup.index');
    }

    public function verifyKey(Request $request)
    {
        $configKey = config('app.setup_key');

        if (!$configKey) {
            return back()->withErrors([
                'setup_key' => 'No setup key has been configured for this installation. Please contact RePropertyCMS Administration.',
            ]);
        }

        if ($request->setup_key !== $configKey) {
            return back()->withErrors([
                'setup_key' => 'Invalid setup key. Please check the key you received from RePropertyCMS Administration.',
            ])->withInput();
        }

        session(['setup_verified' => true]);
        return redirect()->route('setup.requirements');
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX — Credential Test Endpoints
    |--------------------------------------------------------------------------
    */

    public function testDatabase(Request $request): JsonResponse
    {
        try {
            config([
                'database.connections.setup_test' => [
                    'driver'    => 'mysql',
                    'host'      => $request->db_host ?? '127.0.0.1',
                    'port'      => $request->db_port ?? 3306,
                    'database'  => $request->db_name ?? '',
                    'username'  => $request->db_user ?? '',
                    'password'  => $request->db_pass ?? '',
                    'charset'   => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ],
            ]);
            DB::connection('setup_test')->getPdo();
            DB::purge('setup_test');
            return response()->json(['success' => true, 'message' => 'Database connection successful.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
        }
    }

    public function testMail(Request $request): JsonResponse
    {
        try {
            config([
                'mail.default'                 => 'smtp',
                'mail.mailers.smtp.host'       => $request->mail_host,
                'mail.mailers.smtp.port'       => $request->mail_port,
                'mail.mailers.smtp.username'   => $request->mail_username,
                'mail.mailers.smtp.password'   => $request->mail_password,
                'mail.mailers.smtp.encryption' => $request->mail_encryption ?? null,
                'mail.from.address'            => $request->mail_from_address,
                'mail.from.name'               => $request->mail_from_name,
            ]);

            Mail::raw('This is a test email from your setup wizard.', function ($msg) use ($request) {
                $msg->to($request->mail_from_address)
                    ->subject('Setup Wizard — Test Email');
            });

            return response()->json(['success' => true, 'message' => 'Test email sent to ' . $request->mail_from_address]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Mail test failed: ' . $e->getMessage()]);
        }
    }

    public function testStripe(Request $request): JsonResponse
    {
        try {
            \Stripe\Stripe::setApiKey($request->stripe_secret);
            \Stripe\Balance::retrieve();
            return response()->json(['success' => true, 'message' => 'Stripe connection successful. Keys are valid.']);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid Stripe secret key.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Stripe test failed: ' . $e->getMessage()]);
        }
    }

    public function testStorage(Request $request): JsonResponse
    {
        if ($request->driver === 'local') {
            return response()->json(['success' => true, 'message' => 'Local storage selected. No credentials needed.']);
        }

        try {
            $client = new \Aws\S3\S3Client([
                'region'      => $request->aws_region ?? 'us-east-1',
                'version'     => 'latest',
                'credentials' => [
                    'key'    => $request->aws_key,
                    'secret' => $request->aws_secret,
                ],
            ]);
            $client->headBucket(['Bucket' => $request->aws_bucket]);
            return response()->json(['success' => true, 'message' => 'S3 connection successful. Bucket accessible.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'S3 test failed: ' . $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 1 — System Requirements
    |--------------------------------------------------------------------------
    */

    public function requirements()
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

        return view('setup.requirements', compact('requirements'));
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 2 — Database Configuration
    |--------------------------------------------------------------------------
    */

    public function database()
    {
        return view('setup.database');
    }

    public function saveDatabase(Request $request, EnvService $env)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_pass' => 'nullable|string',
        ]);

        // Test connection before saving
        try {
            config([
                'database.connections.setup_test' => [
                    'driver'    => 'mysql',
                    'host'      => $request->db_host,
                    'port'      => $request->db_port,
                    'database'  => $request->db_name,
                    'username'  => $request->db_user,
                    'password'  => $request->db_pass ?? '',
                    'charset'   => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ],
            ]);
            DB::connection('setup_test')->getPdo();
            DB::purge('setup_test');
        } catch (\Exception $e) {
            return back()->withErrors(['db_error' => 'Database connection failed: ' . $e->getMessage()])->withInput();
        }

        // Write to .env
        $env->set([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST'       => $request->db_host,
            'DB_PORT'       => $request->db_port,
            'DB_DATABASE'   => $request->db_name,
            'DB_USERNAME'   => $request->db_user,
            'DB_PASSWORD'   => $request->db_pass ?? '',
        ]);

        // Run migrations against the newly configured database
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            return back()->withErrors(['db_error' => 'Migrations failed: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('setup.admin');
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 3 — Admin Account
    |--------------------------------------------------------------------------
    */

    public function admin()
    {
        return view('setup.create-admin');
    }

    public function saveAdmin(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Admin::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Store non-sensitive summary in session for the final review page
        $setup = session('setup', []);
        $setup['admin'] = ['name' => $request->name, 'email' => $request->email];
        session(['setup' => $setup]);

        return redirect()->route('setup.mail');
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 4 — Mail Configuration (Skippable)
    |--------------------------------------------------------------------------
    */

    public function mail()
    {
        return view('setup.mail');
    }

    public function saveMail(Request $request, EnvService $env)
    {
        $request->validate([
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|numeric',
            'mail_username'     => 'required|string',
            'mail_password'     => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'required|string',
        ]);

        $env->set([
            'MAIL_MAILER'       => $request->mail_mailer ?? 'smtp',
            'MAIL_HOST'         => $request->mail_host,
            'MAIL_PORT'         => $request->mail_port,
            'MAIL_USERNAME'     => $request->mail_username,
            'MAIL_PASSWORD'     => $request->mail_password,
            'MAIL_ENCRYPTION'   => $request->mail_encryption ?? 'tls',
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME'    => $request->mail_from_name,
        ]);

        $setup = session('setup', []);
        $setup['mail_skipped'] = false;
        session(['setup' => $setup]);

        return redirect()->route('setup.stripe');
    }

    public function skipMail()
    {
        $setup = session('setup', []);
        $setup['mail_skipped'] = true;
        session(['setup' => $setup]);

        return redirect()->route('setup.stripe');
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 5 — Stripe Configuration (Skippable)
    |--------------------------------------------------------------------------
    */

    public function stripe()
    {
        return view('setup.stripe');
    }

    public function saveStripe(Request $request, EnvService $env)
    {
        $request->validate([
            'stripe_key'     => 'required|string',
            'stripe_secret'  => 'required|string',
            'stripe_webhook' => 'required|string',
        ]);

        $env->set([
            'STRIPE_KEY'            => $request->stripe_key,
            'STRIPE_SECRET'         => $request->stripe_secret,
            'STRIPE_WEBHOOK_SECRET' => $request->stripe_webhook,
        ]);

        $setup = session('setup', []);
        $setup['stripe_skipped'] = false;
        session(['setup' => $setup]);

        return redirect()->route('setup.storage');
    }

    public function skipStripe()
    {
        $setup = session('setup', []);
        $setup['stripe_skipped'] = true;
        session(['setup' => $setup]);

        return redirect()->route('setup.storage');
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 6 — Storage Configuration (Skippable)
    |--------------------------------------------------------------------------
    */

    public function storage()
    {
        return view('setup.storage');
    }

    public function saveStorage(Request $request, EnvService $env)
    {
        $request->validate(['driver' => 'required|in:local,s3']);

        if ($request->driver === 's3') {
            $request->validate([
                'aws_key'    => 'required|string',
                'aws_secret' => 'required|string',
                'aws_region' => 'required|string',
                'aws_bucket' => 'required|string',
            ]);

            $env->set([
                'FILESYSTEM_DISK'       => 's3',
                'AWS_ACCESS_KEY_ID'     => $request->aws_key,
                'AWS_SECRET_ACCESS_KEY' => $request->aws_secret,
                'AWS_DEFAULT_REGION'    => $request->aws_region,
                'AWS_BUCKET'            => $request->aws_bucket,
            ]);
        } else {
            $env->set(['FILESYSTEM_DISK' => 'local']);
        }

        $setup = session('setup', []);
        $setup['storage_skipped'] = false;
        session(['setup' => $setup]);

        return redirect()->route('setup.captcha');
    }

    public function skipStorage()
    {
        $setup = session('setup', []);
        $setup['storage_skipped'] = true;
        session(['setup' => $setup]);

        return redirect()->route('setup.captcha');
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 7 — reCAPTCHA Configuration (Skippable)
    |--------------------------------------------------------------------------
    */

    public function captcha()
    {
        return view('setup.captcha');
    }

    public function saveCaptcha(Request $request, EnvService $env)
    {
        $request->validate([
            'site_key'   => 'required|string',
            'secret_key' => 'required|string',
        ]);

        $env->set([
            'RECAPTCHA_SITE_KEY'   => $request->site_key,
            'RECAPTCHA_SECRET_KEY' => $request->secret_key,
        ]);

        $setup = session('setup', []);
        $setup['captcha_skipped'] = false;
        session(['setup' => $setup]);

        return redirect()->route('setup.final');
    }

    public function skipCaptcha()
    {
        $setup = session('setup', []);
        $setup['captcha_skipped'] = true;
        session(['setup' => $setup]);

        return redirect()->route('setup.final');
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 8 — Final Review & Complete Installation
    |--------------------------------------------------------------------------
    */

    public function final()
    {
        $setup = session('setup', []);
        return view('setup.final', compact('setup'));
    }

    public function finish(EnvService $env)
    {
        $setup = session('setup', []);

        // Mark app as installed in .env
        $env->set(['APP_INSTALLED' => 'true']);

        // Record integration configuration status in the database
        $integrations = [
            ['integration' => 'app',     'is_setup' => true],
            ['integration' => 'mail',    'is_setup' => !($setup['mail_skipped']    ?? true)],
            ['integration' => 'stripe',  'is_setup' => !($setup['stripe_skipped']  ?? true)],
            ['integration' => 'storage', 'is_setup' => !($setup['storage_skipped'] ?? true)],
            ['integration' => 'captcha', 'is_setup' => !($setup['captcha_skipped'] ?? true)],
        ];

        foreach ($integrations as $record) {
            DB::table('integration_settings')->updateOrInsert(
                ['integration' => $record['integration']],
                [
                    'is_setup'   => $record['is_setup'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        session()->flush();

        return redirect('/admin/sign-in')
            ->with('success', 'Installation complete! Please sign in with your admin credentials.');
    }

    /*
    |--------------------------------------------------------------------------
    | Database Repair
    |--------------------------------------------------------------------------
    */

    public function databaseRepair()
    {
        return view('setup.database-repair');
    }
}
