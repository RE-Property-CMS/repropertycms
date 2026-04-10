<?php

namespace App\Http\Controllers;

use App\Models\Agents;
use App\Models\Backend\Admin;
use App\Models\DemoSession;
use App\Models\Plan;
use App\Models\Properties;
use App\Models\PropertyFloorplans;
use App\Models\PropertyImages;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Demo branch only.
 * Handles the full demo lifecycle: landing, session creation, role switching, and ending.
 */
class DemoController extends Controller
{
    // ─── Landing ─────────────────────────────────────────────────────────────

    public function landing(Request $request)
    {
        $expired = $request->query('expired');
        return view('demo.landing', compact('expired'));
    }

    // ─── Start ───────────────────────────────────────────────────────────────

    public function start()
    {
        $token = Str::random(16);

        // 1. Create demo admin (bypasses global scope — session not set yet)
        $admin = Admin::create([
            'email'            => 'admin_' . $token . '@demo.local',
            'password'         => Hash::make('demo1234'),
            'demo_session_id'  => $token,
        ]);

        // 2. Create demo agent
        $agent = Agents::withoutGlobalScopes()->create([
            'first_name'        => 'Sarah',
            'last_name'         => 'Mitchell',
            'email'             => 'agent_' . $token . '@demo.local',
            'password'          => Hash::make('demo1234'),
            'email_verified_at' => now(),
            'demo_session_id'   => $token,
        ]);

        // Copy demo profile photo into agent's local folder
        $agentImageDir = public_path('files/agents/' . $agent->id);
        if (! is_dir($agentImageDir)) {
            mkdir($agentImageDir, 0755, true);
        }
        $demoAgentSrc = public_path('images/demo/agent.jpg');
        if (file_exists($demoAgentSrc)) {
            copy($demoAgentSrc, $agentImageDir . '/agent.jpg');
            $agent->update(['profile_image' => 'agent.jpg']);
        }

        // 3. Record the demo session (before properties so we have the expiry timestamp)
        $demoSession = DemoSession::create([
            'token'      => $token,
            'admin_id'   => $admin->id,
            'agent_id'   => $agent->id,
            'expires_at' => now()->addMinutes(60),
        ]);

        // 4. Create demo properties (unpublished — agent must subscribe to publish)
        $propertyData = [
            [
                'name'           => 'Oceanview Villa',
                'headline'       => 'Stunning 4-bedroom villa with panoramic ocean views',
                'description'    => 'Experience luxury living in this beautifully designed oceanfront property. Features open-plan living, chef\'s kitchen, private pool, and direct beach access. Every room captures sweeping views of the Pacific, with floor-to-ceiling glass doors opening onto a wraparound terrace.',
                'bedroom'        => 4,
                'bathroom'       => 3,
                'garage'         => 2,
                'price'          => '1,250,000',
                'property_area'  => '420',
                'city'           => 'Malibu',
                'address_line_1' => '12 Ocean Drive',
                'zip'            => '90265',
                'unique_url'     => 'oceanview-villa-' . $token,
                'main_section'   => 'Image',
                'images'         => [
                    'images/demo/prop-1.jpg',
                    'images/demo/prop-2.jpg',
                    'images/demo/interior-living.jpg',
                    'images/demo/interior-living-1.jpg',
                    'images/demo/interior-kitchen.jpg',
                    'images/demo/interior-bedroom.jpg',
                    'images/demo/interior-bathroom.jpg',
                ],
                'floorplan' => 'images/demo/floorplan.jpg',
            ],
            [
                'name'           => 'City Centre Apartment',
                'headline'       => 'Modern 2-bedroom apartment in the heart of downtown',
                'description'    => 'Sleek and contemporary apartment offering stunning city skyline views. Open-plan living space, floor-to-ceiling windows, and premium finishes throughout. Steps from world-class dining, retail, and transport hubs.',
                'bedroom'        => 2,
                'bathroom'       => 2,
                'garage'         => 1,
                'price'          => '485,000',
                'property_area'  => '95',
                'city'           => 'New York',
                'address_line_1' => '88 Park Avenue',
                'zip'            => '10016',
                'unique_url'     => 'city-centre-apt-' . $token,
                'main_section'   => 'Image',
                'images'         => [
                    'images/demo/prop-3.jpg',
                    'images/demo/prop-4.jpg',
                    'images/demo/prop-5.jpg',
                    'images/demo/interior-living-1.jpg',
                    'images/demo/interior-kitchen-1.jpg',
                    'images/demo/interior-bedroom-1.jpg',
                    'images/demo/interior-bathroom-1.jpg',
                ],
            ],
        ];

        foreach ($propertyData as $data) {
            $images    = $data['images'];
            $floorplan = $data['floorplan'] ?? null;
            unset($data['images'], $data['floorplan']);

            $property = Properties::withoutGlobalScopes()->create(array_merge($data, [
                'agent_id'        => $agent->id,
                'matterport_data' => '',
                'published'       => 0,
                'reviewed'        => 0,
                'demo_session_id' => $token,
                'publish_date'    => now()->toDateString(),
                'expiry_date'     => $demoSession->expires_at,
            ]));

            // Attach images and set the first as the featured topbar image
            $firstImageId = null;
            foreach ($images as $imagePath) {
                $img = PropertyImages::create([
                    'property_id' => $property->id,
                    'file_name'   => $imagePath,
                    'thumb'       => $imagePath,
                ]);
                if ($firstImageId === null) {
                    $firstImageId = $img->id;
                }
            }
            if ($firstImageId) {
                $property->update(['featured_image' => $firstImageId]);
            }

            // Attach floorplan if provided
            if ($floorplan) {
                PropertyFloorplans::create([
                    'property_id' => $property->id,
                    'name'        => 'Ground Floor',
                    'file_name'   => $floorplan,
                    'thumb'       => $floorplan,
                    'sequence'    => 1,
                    'sort_order'  => 0,
                ]);
            }
        }

        // 7. Store the token in session — activates global scopes from here on
        session(['demo_session_id' => $token]);

        // 8. Go to admin entry point
        return redirect('/demo/' . $token . '/admin');
    }

    // ─── Switch Role ─────────────────────────────────────────────────────────

    public function switchRole(Request $request, string $token, string $role)
    {
        // Validate the token belongs to the current session
        if (session('demo_session_id') !== $token) {
            return redirect('/demo');
        }

        $demoSession = DemoSession::where('token', $token)->first();
        if (! $demoSession || $demoSession->isExpired()) {
            return redirect('/demo?expired=1');
        }

        // Log out both guards cleanly before switching
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
            // Open the first demo property as a public buyer
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
        // Immediately delete all data for this demo session
        $this->purgeSession($token);

        Auth::guard('agent')->logout();
        Auth::guard('admin')->logout();
        $request->session()->forget([
            'demo_session_id',
            'admin',
            'agent',
            'property',
            'login_admin_59ba36addc2b2f9401580f014c7f58ea4e30989d',
        ]);

        return redirect('/demo/complete');
    }

    // ─── Purge all data for one demo session ─────────────────────────────────

    public static function purgeSession(string $token): void
    {
        $session = DemoSession::where('token', $token)->first();
        if (! $session) {
            return;
        }

        // Delete agent image directory
        $agentImageDir = public_path('files/agents/' . $session->agent_id);
        if (is_dir($agentImageDir)) {
            array_map('unlink', glob($agentImageDir . '/*'));
            rmdir($agentImageDir);
        }

        // Collect property IDs before wiping DB records so we can delete their files
        $propertyIds = Properties::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->withTrashed()
            ->pluck('id')
            ->toArray();

        if (! empty($propertyIds)) {
            self::deletePropertyFiles($propertyIds);
        }

        // Properties (SoftDeletes — force-delete cascades to images, floorplans, etc.)
        Properties::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->withTrashed()
            ->forceDelete();

        // Subscriptions
        Subscription::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->delete();

        // Plans
        Plan::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->delete();

        // Agent
        Agents::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->delete();

        // Admin
        Admin::where('demo_session_id', $token)->delete();

        // Demo session record
        $session->delete();
    }

    // ─── Delete uploaded files for a set of property IDs ─────────────────────
    //
    // Handles both S3 (images, floorplans, documents uploaded via uploadS3Image)
    // and local disk (videos moved to public/files/property_videos/{id}/).
    // Demo placeholder paths (images/demo/*) are never deleted.

    public static function deletePropertyFiles(array $propertyIds): void
    {
        if (empty($propertyIds)) {
            return;
        }

        // ── S3 file cleanup ───────────────────────────────────────────────────
        if (config('filesystems.default') === 's3') {
            $s3Paths = [];

            // Images (file_name + thumb)
            foreach (\Illuminate\Support\Facades\DB::table('property_images')
                ->whereIn('property_id', $propertyIds)
                ->get(['file_name', 'thumb']) as $row) {
                foreach ([$row->file_name, $row->thumb] as $path) {
                    if ($path && ! str_starts_with($path, 'images/demo/')) {
                        $s3Paths[] = $path;
                    }
                }
            }

            // Floorplans (file_name + thumb)
            foreach (\Illuminate\Support\Facades\DB::table('property_floorplans')
                ->whereIn('property_id', $propertyIds)
                ->get(['file_name', 'thumb']) as $row) {
                foreach ([$row->file_name, $row->thumb] as $path) {
                    if ($path && ! str_starts_with($path, 'images/demo/')) {
                        $s3Paths[] = $path;
                    }
                }
            }

            // Documents
            foreach (\Illuminate\Support\Facades\DB::table('property_documents')
                ->whereIn('property_id', $propertyIds)
                ->whereNotNull('file_name')
                ->pluck('file_name') as $path) {
                if (! str_starts_with($path, 'images/demo/')) {
                    $s3Paths[] = $path;
                }
            }

            // Videos stored on S3 (file_name is non-null for direct uploads)
            foreach (\Illuminate\Support\Facades\DB::table('property_videos')
                ->whereIn('property_id', $propertyIds)
                ->whereNotNull('file_name')
                ->pluck('file_name') as $path) {
                if (! str_starts_with($path, 'images/demo/')) {
                    $s3Paths[] = $path;
                }
            }

            if (! empty($s3Paths)) {
                \Illuminate\Support\Facades\Storage::disk('s3')->delete(array_unique($s3Paths));
            }
        }

        // ── Local video directories ───────────────────────────────────────────
        // Videos are moved to public/files/property_videos/{property_id}/ on local disk.
        foreach ($propertyIds as $id) {
            $dir = public_path('files/property_videos/' . $id);
            if (is_dir($dir)) {
                foreach (glob($dir . '/*') as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                rmdir($dir);
            }
        }
    }

    // ─── Complete page ────────────────────────────────────────────────────────

    public function complete()
    {
        return view('demo.complete');
    }
}
