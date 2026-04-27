<?php

namespace App\Http\Controllers;

use App\Models\Backend\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\EnvService;
use Illuminate\Support\Facades\DB;
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
        $host     = config('database.connections.mysql.host', '');
        $port     = config('database.connections.mysql.port', '3306');
        $name     = config('database.connections.mysql.database', '');
        $username = config('database.connections.mysql.username', '');
        $hasPass  = !empty(config('database.connections.mysql.password'));

        $configured = !empty($host) && !empty($name) && !empty($username);

        return view('setup.database', compact('configured', 'host', 'port', 'name', 'username', 'hasPass'));
    }

    public function saveDatabase(): JsonResponse
    {
        try {
            DB::connection()->getPdo();

            $statusFile = storage_path('setup-migrate-status.json');
            file_put_contents($statusFile, json_encode([
                'status'     => 'running',
                'started_at' => now()->toISOString(),
            ]));

            // Fire detached subprocess — returns immediately, process continues in background
            // Windows: start /B "" <title> prevents cmd from treating the first quoted arg as a window title
            $php     = PHP_BINARY;
            $artisan = base_path('artisan');
            $log     = storage_path('setup-migrate.log');
            $cmd     = 'start /B "" "' . $php . '" "' . $artisan . '" setup:run-migrations "' . $statusFile . '" > "' . $log . '" 2>&1';
            pclose(popen($cmd, 'r'));

            return response()->json(['success' => true, 'redirect' => route('setup.migrating')]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('saveDatabase: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function migrating()
    {
        return view('setup.migrating');
    }

    public function migrationStatus(): JsonResponse
    {
        $statusFile = storage_path('setup-migrate-status.json');

        if (!file_exists($statusFile)) {
            return response()->json(['status' => 'unknown']);
        }

        return response()->json(json_decode(file_get_contents($statusFile), true));
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

        return redirect()->route('setup.branding');
    }

    public function skipCaptcha()
    {
        $setup = session('setup', []);
        $setup['captcha_skipped'] = true;
        session(['setup' => $setup]);

        return redirect()->route('setup.branding');
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 8 — Brand Identity (Logo & Favicon)
    |--------------------------------------------------------------------------
    */

    public function branding()
    {
        $brand = DB::table('brand_settings')->first();
        return view('setup.branding', compact('brand'));
    }

    public function saveBranding(Request $request)
    {
        $request->validate([
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,svg|max:512',
        ]);

        $data = ['updated_at' => now(), 'created_at' => now()];

        foreach (['logo', 'favicon'] as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $dir = public_path('images/brand');
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $ext = $request->file($field)->getClientOriginalExtension();
                $request->file($field)->move($dir, $field . '.' . $ext);
                $data[$field . '_path'] = 'images/brand/' . $field . '.' . $ext;
            }
        }

        if (count($data) > 2) {
            DB::table('brand_settings')->updateOrInsert(['id' => 1], $data);
            cache()->forget('brand_settings');
        }

        return redirect()->route('setup.final');
    }

    public function skipBranding()
    {
        return redirect()->route('setup.final');
    }

    /*
    |--------------------------------------------------------------------------
    | STEP 9 — Final Review & Complete Installation
    |--------------------------------------------------------------------------
    */

    public function final()
    {
        $setup = session('setup', []);
        return view('setup.final', compact('setup'));
    }

    public function finish(Request $request, EnvService $env): JsonResponse
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

        // No PHP timeout — seeder can take time
        set_time_limit(0);

        // Seed required reference data as a subprocess
        $seeder = new \Symfony\Component\Process\Process(
            [PHP_BINARY, 'artisan', 'db:seed', '--class=SetupSeeder', '--force'],
            base_path()
        );
        $seeder->setTimeout(60);
        $seeder->run();

        session()->flush();

        return response()->json([
            'success'  => true,
            'redirect' => '/admin/sign-in',
            'message'  => 'Installation complete! Please sign in with your admin credentials.',
        ]);
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
