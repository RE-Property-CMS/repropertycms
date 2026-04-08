<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\DemoSeederController;
use App\Models\Agents;
use App\Services\EnvService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Settings Overview
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $integrations = DB::table('integration_settings')
            ->whereIn('integration', ['mail', 'stripe', 'storage', 'captcha'])
            ->pluck('is_setup', 'integration')
            ->toArray();

        $statuses = [
            'mail'    => $integrations['mail']    ?? false,
            'stripe'  => $integrations['stripe']  ?? false,
            'storage' => $integrations['storage'] ?? false,
            'captcha' => $integrations['captcha'] ?? false,
        ];

        $demoExists = Agents::where('email', DemoSeederController::DEMO_EMAIL)->exists();

        return view('admin.settings.index', compact('statuses', 'demoExists'));
    }

    /*
    |--------------------------------------------------------------------------
    | Mail Settings
    |--------------------------------------------------------------------------
    */

    public function mail()
    {
        $isConfigured = DB::table('integration_settings')
            ->where('integration', 'mail')
            ->value('is_setup') ?? false;

        $current = [
            'host'         => config('mail.mailers.smtp.host', ''),
            'port'         => config('mail.mailers.smtp.port', '587'),
            'username'     => config('mail.mailers.smtp.username', ''),
            'encryption'   => config('mail.mailers.smtp.encryption', 'tls'),
            'from_address' => config('mail.from.address', ''),
            'from_name'    => config('mail.from.name', ''),
        ];

        // Mask password — never expose to view
        $current['password_set'] = !empty(config('mail.mailers.smtp.password'));

        return view('admin.settings.mail', compact('current', 'isConfigured'));
    }

    public function saveMail(Request $request, EnvService $env)
    {
        $request->validate([
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|numeric',
            'mail_username'     => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'required|string',
        ]);

        $data = [
            'MAIL_MAILER'       => 'smtp',
            'MAIL_HOST'         => $request->mail_host,
            'MAIL_PORT'         => $request->mail_port,
            'MAIL_USERNAME'     => $request->mail_username,
            'MAIL_ENCRYPTION'   => $request->mail_encryption ?? 'tls',
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME'    => $request->mail_from_name,
        ];

        // Only update password if a new one was provided
        if ($request->filled('mail_password')) {
            $data['MAIL_PASSWORD'] = $request->mail_password;
        }

        $env->set($data);

        DB::table('integration_settings')->updateOrInsert(
            ['integration' => 'mail'],
            ['is_setup' => true, 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->route('admin.settings.mail')
            ->with('success', 'Mail settings saved successfully.');
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

            Mail::raw('Test email from Admin Settings.', function ($msg) use ($request) {
                $msg->to($request->mail_from_address)
                    ->subject('Admin Settings — Test Email');
            });

            return response()->json(['success' => true, 'message' => 'Test email sent to ' . $request->mail_from_address]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Mail test failed: ' . $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Stripe Settings
    |--------------------------------------------------------------------------
    */

    public function stripe()
    {
        $isConfigured = DB::table('integration_settings')
            ->where('integration', 'stripe')
            ->value('is_setup') ?? false;

        $current = [
            'key'         => config('stripe.api_keys.publish_key', ''),
            'webhook_set' => !empty(config('stripe.api_keys.webhook_secret')),
            'secret_set'  => !empty(config('stripe.api_keys.secret_key')),
        ];

        return view('admin.settings.stripe', compact('current', 'isConfigured'));
    }

    public function saveStripe(Request $request, EnvService $env)
    {
        $request->validate([
            'stripe_key' => 'required|string',
        ]);

        $data = ['STRIPE_PUBLIC_KEY' => $request->stripe_key];

        if ($request->filled('stripe_secret')) {
            $data['STRIPE_SECRET_KEY'] = $request->stripe_secret;
        }

        if ($request->filled('stripe_webhook')) {
            $data['STRIPE_WEBHOOK_SECRET'] = $request->stripe_webhook;
        }

        $env->set($data);

        DB::table('integration_settings')->updateOrInsert(
            ['integration' => 'stripe'],
            ['is_setup' => true, 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->route('admin.settings.stripe')
            ->with('success', 'Stripe settings saved successfully.');
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

    /*
    |--------------------------------------------------------------------------
    | Storage Settings
    |--------------------------------------------------------------------------
    */

    public function storage()
    {
        $isConfigured = DB::table('integration_settings')
            ->where('integration', 'storage')
            ->value('is_setup') ?? false;

        $current = [
            'driver'        => config('filesystems.default', 'local'),
            'aws_key'       => config('filesystems.disks.s3.key', ''),
            'aws_region'    => config('filesystems.disks.s3.region', 'us-east-1'),
            'aws_bucket'    => config('filesystems.disks.s3.bucket', ''),
            'aws_secret_set' => !empty(config('filesystems.disks.s3.secret')),
        ];

        return view('admin.settings.storage', compact('current', 'isConfigured'));
    }

    public function saveStorage(Request $request, EnvService $env)
    {
        $request->validate(['driver' => 'required|in:local,s3']);

        if ($request->driver === 's3') {
            $request->validate([
                'aws_key'    => 'required|string',
                'aws_region' => 'required|string',
                'aws_bucket' => 'required|string',
            ]);

            $data = [
                'FILESYSTEM_DISK'    => 's3',
                'AWS_ACCESS_KEY_ID'  => $request->aws_key,
                'AWS_DEFAULT_REGION' => $request->aws_region,
                'AWS_BUCKET'         => $request->aws_bucket,
            ];

            if ($request->filled('aws_secret')) {
                $data['AWS_SECRET_ACCESS_KEY'] = $request->aws_secret;
            }

            $env->set($data);
        } else {
            $env->set(['FILESYSTEM_DISK' => 'local']);
        }

        DB::table('integration_settings')->updateOrInsert(
            ['integration' => 'storage'],
            ['is_setup' => true, 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->route('admin.settings.storage')
            ->with('success', 'Storage settings saved successfully.');
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
    | reCAPTCHA Settings
    |--------------------------------------------------------------------------
    */

    public function captcha()
    {
        $isConfigured = DB::table('integration_settings')
            ->where('integration', 'captcha')
            ->value('is_setup') ?? false;

        $current = [
            'site_key'   => config('services.recaptcha.site_key', ''),
            'secret_set' => !empty(config('services.recaptcha.secret_key')),
        ];

        return view('admin.settings.captcha', compact('current', 'isConfigured'));
    }

    public function saveCaptcha(Request $request, EnvService $env)
    {
        $request->validate([
            'site_key' => 'required|string',
        ]);

        $data = ['RECAPTCHA_SITE_KEY' => $request->site_key];

        if ($request->filled('secret_key')) {
            $data['RECAPTCHA_SECRET_KEY'] = $request->secret_key;
        }

        $env->set($data);

        DB::table('integration_settings')->updateOrInsert(
            ['integration' => 'captcha'],
            ['is_setup' => true, 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->route('admin.settings.captcha')
            ->with('success', 'reCAPTCHA settings saved successfully.');
    }

    public function testCaptcha(Request $request): JsonResponse
    {
        // reCAPTCHA keys can't be validated without a real CAPTCHA response token,
        // so we just confirm both keys are present and non-empty.
        $siteKey   = $request->site_key;
        $secretKey = $request->secret_key;

        if (empty($siteKey) || empty($secretKey)) {
            return response()->json(['success' => false, 'message' => 'Both site key and secret key are required.']);
        }

        // Basic format check — reCAPTCHA keys start with "6L"
        if (!str_starts_with($siteKey, '6L') || !str_starts_with($secretKey, '6L')) {
            return response()->json(['success' => false, 'message' => 'Keys do not appear to be valid reCAPTCHA keys (should start with "6L").']);
        }

        return response()->json(['success' => true, 'message' => 'Keys look valid. Save to apply them.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Brand & Appearance Settings
    |--------------------------------------------------------------------------
    */

    public function brand()
    {
        $brand = DB::table('brand_settings')->first();

        return view('admin.settings.brand', compact('brand'));
    }

    public function saveBrand(Request $request)
    {
        $request->validate([
            'primary_color'   => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color'    => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_2_color'  => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_color'   => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font_body'       => 'required|string|max:100',
            'font_heading'    => 'required|string|max:100',
            'font_admin'      => 'required|string|max:100',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon'         => 'nullable|file|mimes:ico,png,jpg,jpeg,svg|max:512',
            'website_url'     => 'nullable|url|max:255',
            'instagram_url'   => 'nullable|url|max:255',
            'facebook_url'    => 'nullable|url|max:255',
            'twitter_url'     => 'nullable|url|max:255',
            'linkedin_url'    => 'nullable|url|max:255',
        ]);

        $data = [
            'primary_color'   => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'accent_color'    => $request->accent_color,
            'accent_2_color'  => $request->accent_2_color,
            'sidebar_color'   => $request->sidebar_color,
            'font_body'       => $request->font_body,
            'font_heading'    => $request->font_heading,
            'font_admin'      => $request->font_admin,
            'website_url'     => $request->website_url ?: null,
            'instagram_url'   => $request->instagram_url ?: null,
            'facebook_url'    => $request->facebook_url ?: null,
            'twitter_url'     => $request->twitter_url ?: null,
            'linkedin_url'    => $request->linkedin_url ?: null,
            'updated_at'      => now(),
            'created_at'      => now(),
        ];

        // Handle logo and favicon uploads
        foreach (['logo', 'favicon'] as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $old = DB::table('brand_settings')->value($field . '_path');
                if ($old && file_exists(public_path($old))) {
                    @unlink(public_path($old));
                }
                $dir = public_path('images/brand');
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $ext = $request->file($field)->guessExtension();
                $request->file($field)->move($dir, $field . '.' . $ext);
                $data[$field . '_path'] = 'images/brand/' . $field . '.' . $ext;
            }
        }

        DB::table('brand_settings')->updateOrInsert(['id' => 1], $data);

        cache()->forget('brand_settings');

        return redirect()->route('admin.settings.brand')
            ->with('success', 'Brand settings saved successfully.');
    }
}
