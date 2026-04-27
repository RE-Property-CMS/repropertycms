<?php

namespace App\Services;

use App\Mail\DemoCredentialsMail;
use App\Models\Agents;
use App\Models\Backend\Admin;
use App\Models\DemoSession;
use App\Models\Plan;
use App\Models\Properties;
use App\Models\PropertyFloorplans;
use App\Models\PropertyImages;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoProvisioningService
{
    // ─── Demo property seed data ──────────────────────────────────────────────

    private const PROPERTIES = [
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

    // ─── Provision ────────────────────────────────────────────────────────────

    /**
     * Create a full demo sandbox (admin + agent + properties + session) and send
     * the credentials email.
     *
     * @param  string       $email          Lead/invitee email address
     * @param  string       $name           Lead/invitee name (empty string if not provided)
     * @param  string       $type           'self_service' | 'invited'
     * @param  int          $expiryMinutes  60 for self-service, 14400 (10 days) for invited
     * @param  string|null  $ip             Visitor IP (null for admin-initiated invites)
     */
    public function provision(
        string  $email,
        string  $name,
        string  $type,
        int     $expiryMinutes,
        ?string $ip = null,
    ): DemoSession {
        $token    = Str::random(16);
        $password = 'Demo@' . strtoupper(substr($token, 0, 6));

        // 1. Create demo admin
        $admin = Admin::create([
            'email'           => 'admin_' . $token . '@demo.local',
            'password'        => Hash::make($password),
            'demo_session_id' => $token,
        ]);

        // 2. Create demo agent
        $agent = Agents::withoutGlobalScopes()->create([
            'first_name'        => 'Sarah',
            'last_name'         => 'Mitchell',
            'email'             => 'agent_' . $token . '@demo.local',
            'password'          => Hash::make($password),
            'email_verified_at' => now(),
            'demo_session_id'   => $token,
        ]);

        // Copy demo profile photo
        $agentImageDir = public_path('files/agents/' . $agent->id);
        if (! is_dir($agentImageDir)) {
            mkdir($agentImageDir, 0755, true);
        }
        $demoAgentSrc = public_path('images/demo/agent.jpg');
        if (file_exists($demoAgentSrc)) {
            copy($demoAgentSrc, $agentImageDir . '/agent.jpg');
            $agent->update(['profile_image' => 'agent.jpg']);
        }

        // 3. Create demo session record
        $demoSession = DemoSession::create([
            'token'      => $token,
            'type'       => $type,
            'admin_id'   => $admin->id,
            'agent_id'   => $agent->id,
            'expires_at' => now()->addMinutes($expiryMinutes),
            'lead_name'  => $name ?: null,
            'lead_email' => $email,
            'lead_ip'    => $ip,
        ]);

        // 4. Seed demo properties
        foreach (self::PROPERTIES as $data) {
            $images    = $data['images'];
            $floorplan = $data['floorplan'] ?? null;
            unset($data['images'], $data['floorplan']);

            $slug     = Str::slug($data['name']);
            $property = Properties::withoutGlobalScopes()->create(array_merge($data, [
                'agent_id'        => $agent->id,
                'unique_url'      => $slug . '-' . $token,
                'matterport_data' => '',
                'published'       => 0,
                'reviewed'        => 0,
                'demo_session_id' => $token,
                'publish_date'    => now()->toDateString(),
                'expiry_date'     => $demoSession->expires_at,
            ]));

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

        // 5. Send credentials email
        $duration = $type === 'invited' ? '10 days' : '60 minutes';

        Mail::to($email)->send(new DemoCredentialsMail(
            leadName:   $name,
            token:      $token,
            adminEmail: $admin->email,
            agentEmail: $agent->email,
            password:   $password,
            duration:   $duration,
        ));

        return $demoSession;
    }

    // ─── Purge ────────────────────────────────────────────────────────────────

    /**
     * Completely wipe all data for a demo session — DB records + uploaded files.
     * Safe to call even if the session was already partially cleaned.
     */
    public function purge(DemoSession $session): void
    {
        $token = $session->token;

        // Delete agent image directory
        $agentImageDir = public_path('files/agents/' . $session->agent_id);
        if (is_dir($agentImageDir)) {
            array_map('unlink', glob($agentImageDir . '/*'));
            rmdir($agentImageDir);
        }

        // Delete demo brand uploads (logo/favicon uploaded during this session)
        $demoBrandDir = public_path('images/brand/demo/' . $token);
        if (is_dir($demoBrandDir)) {
            array_map('unlink', glob($demoBrandDir . '/*'));
            rmdir($demoBrandDir);
        }

        // Collect property IDs before wiping DB records
        $propertyIds = Properties::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->withTrashed()
            ->pluck('id')
            ->toArray();

        if (! empty($propertyIds)) {
            $this->deletePropertyFiles($propertyIds);
        }

        Properties::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->withTrashed()
            ->forceDelete();

        Subscription::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->delete();

        Plan::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->delete();

        Agents::withoutGlobalScopes()
            ->where('demo_session_id', $token)
            ->delete();

        Admin::where('demo_session_id', $token)->delete();

        $session->delete();
    }

    // ─── File cleanup helper ──────────────────────────────────────────────────

    private function deletePropertyFiles(array $propertyIds): void
    {
        if (empty($propertyIds)) {
            return;
        }

        if (config('filesystems.default') === 's3') {
            $s3Paths = [];

            foreach (\Illuminate\Support\Facades\DB::table('property_images')
                ->whereIn('property_id', $propertyIds)
                ->get(['file_name', 'thumb']) as $row) {
                foreach ([$row->file_name, $row->thumb] as $path) {
                    if ($path && ! str_starts_with($path, 'images/demo/')) {
                        $s3Paths[] = $path;
                    }
                }
            }

            foreach (\Illuminate\Support\Facades\DB::table('property_floorplans')
                ->whereIn('property_id', $propertyIds)
                ->get(['file_name', 'thumb']) as $row) {
                foreach ([$row->file_name, $row->thumb] as $path) {
                    if ($path && ! str_starts_with($path, 'images/demo/')) {
                        $s3Paths[] = $path;
                    }
                }
            }

            foreach (\Illuminate\Support\Facades\DB::table('property_documents')
                ->whereIn('property_id', $propertyIds)
                ->whereNotNull('file_name')
                ->pluck('file_name') as $path) {
                if (! str_starts_with($path, 'images/demo/')) {
                    $s3Paths[] = $path;
                }
            }

            foreach (\Illuminate\Support\Facades\DB::table('property_videos')
                ->whereIn('property_id', $propertyIds)
                ->whereNotNull('file_name')
                ->pluck('file_name') as $path) {
                if (! str_starts_with($path, 'images/demo/')) {
                    $s3Paths[] = $path;
                }
            }

            if (! empty($s3Paths)) {
                Storage::disk('s3')->delete(array_unique($s3Paths));
            }
        }

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
}
