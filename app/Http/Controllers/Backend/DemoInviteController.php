<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\DemoCredentialsMail;
use App\Models\Agents;
use App\Models\Backend\Admin;
use App\Models\DemoSession;
use App\Services\DemoProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DemoInviteController extends Controller
{
    public function __construct(private readonly DemoProvisioningService $provisioner) {}

    // ─── Sessions list ────────────────────────────────────────────────────────

    public function sessions(Request $request)
    {
        $query = DemoSession::query()->latest();

        // Filter by status
        $status = $request->query('status', 'all');
        if ($status === 'active') {
            $query->where('expires_at', '>', now());
        } elseif ($status === 'expired') {
            $query->where('expires_at', '<=', now());
        }

        // Filter by type
        $type = $request->query('type', 'all');
        if (in_array($type, ['self_service', 'invited'])) {
            $query->where('type', $type);
        }

        // Search by email
        $search = $request->query('search', '');
        if ($search) {
            $query->where('lead_email', 'like', '%' . $search . '%');
        }

        $sessions = $query->paginate(20)->withQueryString();

        return view('admin.demo.sessions', compact('sessions', 'status', 'type', 'search'));
    }

    // ─── Invite form ──────────────────────────────────────────────────────────

    public function invite()
    {
        return view('admin.demo.invite');
    }

    // ─── Send invite ──────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:100',
        ]);

        // Prevent duplicate active invited session for the same email
        $existing = DemoSession::where('lead_email', $request->email)
            ->where('type', 'invited')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', "An active invited demo already exists for {$request->email}. It expires " . $existing->expires_at->diffForHumans() . '.');
        }

        try {
            $this->provisioner->provision(
                email:         $request->email,
                name:          $request->name ?? '',
                type:          'invited',
                expiryMinutes: 14400, // 10 days
            );
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create demo session: ' . $e->getMessage());
        }

        return redirect()->route('admin.demo.sessions')
            ->with('success', "Demo invitation sent to {$request->email}. They have 10 days to explore.");
    }

    // ─── Resend credentials email ─────────────────────────────────────────────

    public function resend(int $id)
    {
        $session = DemoSession::findOrFail($id);

        if ($session->isExpired()) {
            return redirect()->route('admin.demo.sessions')
                ->with('error', "Cannot resend — the demo session for {$session->lead_email} has already expired.");
        }

        $token    = $session->token;
        $password = 'Demo@' . strtoupper(substr($token, 0, 6));
        $duration = $session->type === 'invited' ? '10 days' : '60 minutes';

        $admin = Admin::where('demo_session_id', $token)->first();
        $agent = Agents::withoutGlobalScopes()->where('demo_session_id', $token)->first();

        if (! $admin || ! $agent || ! $session->lead_email) {
            return redirect()->route('admin.demo.sessions')
                ->with('error', 'Could not resend — session data is incomplete.');
        }

        Mail::to($session->lead_email)->send(new DemoCredentialsMail(
            leadName:   $session->lead_name ?? '',
            token:      $token,
            adminEmail: $admin->email,
            agentEmail: $agent->email,
            password:   $password,
            duration:   $duration,
        ));

        return redirect()->route('admin.demo.sessions')
            ->with('success', "Credentials resent to {$session->lead_email}.");
    }

    // ─── Revoke ───────────────────────────────────────────────────────────────

    public function revoke(int $id)
    {
        $session = DemoSession::findOrFail($id);

        $email = $session->lead_email;

        $this->provisioner->purge($session);

        return redirect()->route('admin.demo.sessions')
            ->with('success', "Demo session for {$email} has been revoked and all data deleted.");
    }
}
