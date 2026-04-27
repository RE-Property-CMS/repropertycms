<?php

namespace App\Http\Controllers;

use App\Models\DemoSession;
use App\Models\Page;
use App\Models\Properties;
use App\Models\Agents;
use App\Models\Backend\Admin;
use App\Services\DemoProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Demo branch only.
 * Handles the public demo lifecycle: landing, session activation, role switching, and ending.
 * All provisioning logic lives in DemoProvisioningService.
 */
class DemoController extends Controller
{
    public function __construct(private readonly DemoProvisioningService $provisioner) {}

    // ─── Landing ─────────────────────────────────────────────────────────────

    public function landing(Request $request)
    {
        $expired = $request->query('expired');

        $page = Page::where('key', 'demo-landing')->where('action', true)->first();

        if ($page && $page->html) {
            $formHtml = view('demo.partials.landing-form', [
                'expired' => $expired,
                'errors'  => session()->get('errors', new \Illuminate\Support\ViewErrorBag()),
            ])->render();

            $body = preg_replace(
                '/<div[^>]*data-demo=["\']form["\'][^>]*>.*?<\/div>/si',
                $formHtml,
                $page->html
            );

            return response($this->buildDemoHtml($page, $body ?? $page->html))
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return view('demo.landing', compact('expired'));
    }

    private function buildDemoHtml(Page $page, string $body): string
    {
        $title = e($page->meta_title ?: $page->title);
        $css   = $page->css ?? '';

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$title}</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
            <style>{$css}</style>
        </head>
        <body>
            {$body}
        </body>
        </html>
        HTML;
    }

    // ─── Disposable email check ───────────────────────────────────────────────

    private const DISPOSABLE_DOMAINS = [
        'mailinator.com', 'yopmail.com', 'guerrillamail.com', 'guerrillamail.net',
        'guerrillamail.org', 'guerrillamail.biz', 'guerrillamail.de', 'guerrillamail.info',
        'throwam.com', 'throwam.net', 'tempmail.com', 'temp-mail.org', 'temp-mail.io',
        'trashmail.com', 'trashmail.me', 'trashmail.net', 'trashmail.io', 'trashmail.at',
        'sharklasers.com', 'guerrillamailblock.com', 'grr.la', 'spam4.me',
        'dispostable.com', 'mailnull.com', 'spamgourmet.com', 'spamgourmet.net',
        'maildrop.cc', 'mailnesia.com', 'discard.email',
        'fakeinbox.com', 'fakeinbox.net', 'spamfree24.org', 'spamhereplease.com',
        'spammotel.com', 'spamoff.de', 'spamspot.com', 'spamthis.co.uk',
        'tempinbox.com', 'tempinbox.co.uk', 'tempr.email',
        'getairmail.com', 'filzmail.com', 'getnada.com',
        'mailexpire.com', 'mintemail.com', 'mytrashmail.com', 'no-spam.ws', 'no-spam.eu',
        'nobulk.com', 'noclickemail.com', 'nogmailspam.info', 'nomail.xl.cx',
        'nomail2me.com', 'nospam.ze.tc', 'nospam4.us', 'nospamfor.us',
        'trashmail.xyz', 'trashmailer.com',
        'kurzepost.de', 'objectmail.com', 'obobbo.com', 'oneoffemail.com',
        'onewaymail.com', 'online.ms', 'oopi.org', 'opayq.com',
        'zetmail.com', 'zoemail.com', 'mailseal.de', 'wegwerfmail.de',
        'wegwerfmail.net', 'wegwerfmail.org', 'yep.it', 'mailtemp.info',
    ];

    private function isDisposableEmail(string $email): bool
    {
        $domain = strtolower(substr($email, strpos($email, '@') + 1));
        return in_array($domain, self::DISPOSABLE_DOMAINS, true);
    }

    // ─── Start (self-service) ─────────────────────────────────────────────────

    public function start(Request $request)
    {
        $request->validate([
            'name'  => 'nullable|string|max:100',
            'email' => 'required|email|max:255',
        ]);

        if ($this->isDisposableEmail($request->email)) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Please use a real email address. Temporary/disposable email services are not allowed.']);
        }

        $this->provisioner->provision(
            email:         $request->email,
            name:          $request->name ?? '',
            type:          'self_service',
            expiryMinutes: 60,
            ip:            $request->ip(),
        );

        session()->flash('demo_lead_email', $request->email);

        return redirect()->route('demo.check-email');
    }

    // ─── Enter (activates session then sends to login) ────────────────────────

    public function enter(Request $request, string $token, string $role)
    {
        $demoSession = DemoSession::where('token', $token)->first();

        if (! $demoSession || $demoSession->isExpired()) {
            return redirect()->route('demo.landing', ['expired' => 1]);
        }

        session(['demo_session_id' => $token]);

        if ($role === 'admin') {
            // First-time entry → show wizard tour before admin panel
            if (! session('demo_wizard_shown')) {
                session()->put('url.intended', route('demo.wizard.requirements'));
            }
            return redirect()->route('admin.login');
        }

        return redirect()->route('login');
    }

    // ─── Check email page ─────────────────────────────────────────────────────

    public function checkEmail()
    {
        return view('demo.check-email');
    }

    // ─── Switch Role ─────────────────────────────────────────────────────────

    public function switchRole(Request $request, string $token, string $role)
    {
        if (session('demo_session_id') !== $token) {
            return redirect('/demo');
        }

        $demoSession = DemoSession::where('token', $token)->first();
        if (! $demoSession || $demoSession->isExpired()) {
            return redirect('/demo?expired=1');
        }

        Auth::guard('agent')->logout();
        Auth::guard('admin')->logout();
        $request->session()->forget(['admin', 'agent', 'property',
            'login_admin_59ba36addc2b2f9401580f014c7f58ea4e30989d']);

        if ($role === 'admin') {
            $admin = Admin::find($demoSession->admin_id);
            if ($admin) {
                Auth::guard('admin')->login($admin);
                session([
                    'admin' => $admin,
                    'login_admin_59ba36addc2b2f9401580f014c7f58ea4e30989d' => $admin->id,
                ]);
            }
            return redirect('/admin/dashboard');
        }

        if ($role === 'agent') {
            $agent = Agents::withoutGlobalScopes()->find($demoSession->agent_id);
            if ($agent) {
                Auth::guard('agent')->login($agent);
                session(['agent' => $agent]);
            }
            return redirect('/agent/dashboard');
        }

        if ($role === 'buyer') {
            $property = Properties::withoutGlobalScopes()
                ->where('demo_session_id', $token)
                ->first();
            if ($property) {
                return redirect('/' . $property->unique_url);
            }
            return redirect('/demo');
        }

        return redirect('/demo');
    }

    // ─── End ─────────────────────────────────────────────────────────────────

    public function end(Request $request, string $token)
    {
        $session = DemoSession::where('token', $token)->first();
        if ($session) {
            $this->provisioner->purge($session);
        }

        Auth::guard('agent')->logout();
        Auth::guard('admin')->logout();
        $request->session()->forget([
            'demo_session_id',
            'demo_brand_settings',
            'admin',
            'agent',
            'property',
            'login_admin_59ba36addc2b2f9401580f014c7f58ea4e30989d',
        ]);

        return redirect('/demo/complete');
    }

    // ─── Complete page ────────────────────────────────────────────────────────

    public function complete()
    {
        return view('demo.complete');
    }
}
