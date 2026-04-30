<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LicenseBuyer;
use App\Models\LicenseDomain;
use App\Models\LicenseKey;
use App\Models\LicenseVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_buyers'      => LicenseBuyer::count(),
            'active_keys'       => LicenseKey::where('status', 'active')->count(),
            'revoked_keys'      => LicenseKey::where('status', 'revoked')->count(),
            'domains_in_use'    => LicenseDomain::count(),
            'verifications_today' => LicenseVerification::whereDate('verified_at', today())->count(),
        ];

        $atLimit = LicenseKey::where('status', 'active')
            ->with(['buyer', 'licenseDomains'])
            ->get()
            ->filter(fn($k) => $k->isAtLimit());

        $recent = LicenseVerification::with('licenseKey.buyer')
            ->orderByDesc('verified_at')
            ->limit(15)
            ->get();

        return view('admin.licenses.dashboard', compact('stats', 'atLimit', 'recent'));
    }

    public function buyers()
    {
        $buyers = LicenseBuyer::withCount('licenseKeys')
            ->with('licenseKeys.licenseDomains')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.licenses.buyers', compact('buyers'));
    }

    public function createBuyer()
    {
        return view('admin.licenses.create-buyer');
    }

    public function storeBuyer(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:license_buyers,email',
            'notes' => 'nullable|string',
        ]);

        LicenseBuyer::create($request->only('name', 'email', 'notes'));

        return redirect()->route('admin.licenses.buyers')
            ->with('success', "Buyer {$request->name} added successfully.");
    }

    public function keys(Request $request)
    {
        $query = LicenseKey::with(['buyer', 'licenseDomains'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('buyer_id')) {
            $query->where('license_buyer_id', $request->buyer_id);
        }

        $keys   = $query->paginate(20)->withQueryString();
        $buyers = LicenseBuyer::orderBy('name')->get();

        return view('admin.licenses.keys', compact('keys', 'buyers'));
    }

    public function createKey()
    {
        $buyers = LicenseBuyer::orderBy('name')->get();
        return view('admin.licenses.create-key', compact('buyers'));
    }

    public function storeKey(Request $request)
    {
        $request->validate([
            'license_buyer_id' => 'required|exists:license_buyers,id',
            'max_domains'      => 'required|integer|min:1|max:100',
            'notes'            => 'nullable|string',
            'expires_at'       => 'nullable|date|after:today',
        ]);

        $key = $this->generateUniqueKey();

        LicenseKey::create([
            'license_buyer_id' => $request->license_buyer_id,
            'key'              => $key,
            'status'           => 'active',
            'max_domains'      => $request->max_domains,
            'notes'            => $request->notes,
            'expires_at'       => $request->expires_at ?: null,
        ]);

        return redirect()->route('admin.licenses.keys')
            ->with('success', "License key generated: {$key}");
    }

    public function revokeKey(int $id)
    {
        $key = LicenseKey::findOrFail($id);
        $key->update(['status' => 'revoked']);

        return redirect()->back()->with('success', "Key {$key->key} has been revoked.");
    }

    public function verifications(Request $request)
    {
        $query = LicenseVerification::with('licenseKey.buyer')
            ->orderByDesc('verified_at');

        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        if ($request->filled('from')) {
            $query->whereDate('verified_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('verified_at', '<=', $request->to);
        }

        $verifications = $query->paginate(50)->withQueryString();

        return view('admin.licenses.verifications', compact('verifications'));
    }

    private function generateUniqueKey(): string
    {
        do {
            $key = strtoupper(implode('-', [
                Str::random(4), Str::random(4),
                Str::random(4), Str::random(4), Str::random(4),
            ]));
        } while (LicenseKey::where('key', $key)->exists());

        return $key;
    }
}
