<?php

namespace App\Services;

use App\Models\Amenities;
use App\Models\Agents;
use App\Models\Backend\Admin;
use App\Models\DemoSession;
use App\Models\Plan;
use App\Models\Properties;
use App\Models\PropertyAmenities;
use App\Models\PropertyDocuments;
use App\Models\PropertyFloorplans;
use App\Models\PropertyGalleries;
use App\Models\PropertyGalleryImages;
use App\Models\PropertyImages;
use App\Models\PropertyVideos;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoProvisioningService
{
    // ─── Vimeo banner (shared across both properties) ─────────────────────────

    private const VIMEO_BANNER = 'https://vimeo.com/1171152881';

    // ─── Amenities list ───────────────────────────────────────────────────────

    private const AMENITY_LIST = [
        'Heated Floors',
        'Private Pool & Spa',
        'Home Theater',
        'Wine Cellar',
        'Smart Home System',
        'Gated Entry',
        "Chef's Kitchen",
        'Panoramic Views',
        'Multi-Car Dream Garage',
        'Radiant Heated Driveway',
        'Outdoor Fireplace',
        'Bunk Room',
    ];

    // ─── Floorplans (shared across both properties) ───────────────────────────

    private const FLOORPLANS = [
        ['file' => 'images/demo/Floorplans/garage.jpeg', 'name' => 'Garage'],
        ['file' => 'images/demo/Floorplans/lower.jpeg',  'name' => 'Lower Level'],
        ['file' => 'images/demo/Floorplans/main.jpeg',   'name' => 'Main Level'],
        ['file' => 'images/demo/Floorplans/upper.jpeg',  'name' => 'Upper Level'],
    ];

    // ─── Photo galleries (shared across both properties) ─────────────────────

    private const GALLERIES = [
        'Main Level' => [
            'images/demo/Images/Main Level/AdobeStock_1069484462.jpg',
            'images/demo/Images/Main Level/bath_spa.jpg',
            'images/demo/Images/Main Level/bathroom.jpg',
            'images/demo/Images/Main Level/bathtub.jpg',
            'images/demo/Images/Main Level/hot_tub_corner.jpg',
            'images/demo/Images/Main Level/kitchen.jpg',
            'images/demo/Images/Main Level/kitchen_side_1.jpg',
            'images/demo/Images/Main Level/living2_main.jpg',
            'images/demo/Images/Main Level/living_main.jpg',
            'images/demo/Images/Main Level/rec_room.jpg',
        ],
        'Upper Level' => [
            'images/demo/Images/Upper Level/Upper-spa.jpg',
            'images/demo/Images/Upper Level/Upper_Bath.jpg',
            'images/demo/Images/Upper Level/Upper_Exercise.jpg',
            'images/demo/Images/Upper Level/Upper_Family.jpg',
            'images/demo/Images/Upper Level/Upper_Kitchen.jpg',
            'images/demo/Images/Upper Level/Upper_game.jpg',
            'images/demo/Images/Upper Level/Upper_game_1.jpg',
            'images/demo/Images/Upper Level/Upper_office.jpg',
            'images/demo/Images/Upper Level/theater.jpg',
        ],
        'Lower Level' => [
            'images/demo/Images/Lower Level/Firefly large bedroom snow fireplace 84824.jpg',
            'images/demo/Images/Lower Level/bedroom copy 2.jpg',
            'images/demo/Images/Lower Level/bedroom.jpg',
            'images/demo/Images/Lower Level/bedroom2.jpg',
            'images/demo/Images/Lower Level/bedroom3.jpg',
            'images/demo/Images/Lower Level/bedroom4.jpg',
            'images/demo/Images/Lower Level/bedroom5.jpg',
            'images/demo/Images/Lower Level/bedroom_lower.jpg',
            'images/demo/Images/Lower Level/bunk_beds_lower.jpg',
            'images/demo/Images/Lower Level/kitchen.jpg',
            'images/demo/Images/Lower Level/left_deck_new.jpg',
            'images/demo/Images/Lower Level/patio_outer.jpg',
            'images/demo/Images/Lower Level/patio_outer_2.jpg',
            'images/demo/Images/Lower Level/patio_outer_3.jpg',
            'images/demo/Images/Lower Level/patio_outer_4.jpg',
            'images/demo/Images/Lower Level/rec_room_lower.jpg',
        ],
        'Dream Garage' => [
            'images/demo/Images/Dream Garage/garage1.png',
            'images/demo/Images/Dream Garage/garage16.png',
            'images/demo/Images/Dream Garage/garage18.png',
            'images/demo/Images/Dream Garage/garage19.png',
            'images/demo/Images/Dream Garage/garage1_lift.jpg',
            'images/demo/Images/Dream Garage/garage4 copy.jpg',
            'images/demo/Images/Dream Garage/garage4.jpg',
            'images/demo/Images/Dream Garage/garage4.png',
            'images/demo/Images/Dream Garage/garage5.png',
            'images/demo/Images/Dream Garage/garage6.png',
            'images/demo/Images/Dream Garage/garage7.png',
            'images/demo/Images/Dream Garage/garage_building.jpg',
            'images/demo/Images/Dream Garage/zarage9.png',
            'images/demo/Images/Dream Garage/zgarage1.png',
            'images/demo/Images/Dream Garage/zgarage10.png',
            'images/demo/Images/Dream Garage/zgarage12.png',
            'images/demo/Images/Dream Garage/zgarage13.png',
            'images/demo/Images/Dream Garage/zgarage14.png',
            'images/demo/Images/Dream Garage/zgarage2.png',
            'images/demo/Images/Dream Garage/zgarage21.png',
        ],
    ];

    // ─── Documents (PDFs only) ────────────────────────────────────────────────

    private const DOCUMENTS = [
        ['file' => 'images/demo/Documents/20230518_WB_WMBP-Orientation-Guide_001.pdf',        'name' => 'Orientation Guide'],
        ['file' => 'images/demo/Documents/Leavenworth+Adventure+Park+RLS+5.16.23[71].pdf',    'name' => 'Adventure Park Guide'],
        ['file' => 'images/demo/Documents/Leavenworth_Guide_2021-22_small.pdf',               'name' => 'Leavenworth Guide'],
        ['file' => 'images/demo/Documents/MartisCamp_Family_Barn.pdf',                        'name' => 'Martis Camp Family Barn'],
        ['file' => 'images/demo/Documents/Nordic_map_2020.pdf',                              'name' => 'Nordic Trail Map'],
        ['file' => 'images/demo/Documents/Northstar_winter-trail_map.pdf',                   'name' => 'Winter Trail Map'],
        ['file' => 'images/demo/Documents/www_martiscamp_com_about_martis_camp.pdf',          'name' => 'About Martis Camp'],
        ['file' => 'images/demo/Documents/www_martiscamp_com_camp_lodge.pdf',                 'name' => 'Camp Lodge'],
        ['file' => 'images/demo/Documents/www_martiscamp_com_lake_tahoe_golf.pdf',            'name' => 'Lake Tahoe Golf'],
        ['file' => 'images/demo/Documents/www_martiscamp_com_lake_tahoe_skiing.pdf',          'name' => 'Lake Tahoe Skiing'],
    ];

    // ─── Demo property definitions ────────────────────────────────────────────

    private const PROPERTIES = [
        [
            'name'           => 'Valhalla Estate',
            'headline'       => 'Iconic 10-bedroom mountain estate with panoramic views and world-class amenities',
            'description'    => 'Perched above the valley with sweeping mountain vistas, Valhalla Estate is a rare trophy property offering the ultimate in luxury mountain living. The 14,900 sq ft residence features soaring timber-beam ceilings, four fireplaces, a state-of-the-art home theater, wine cellar, and a dream garage with capacity for 18+ vehicles and two phantom car lifts. Entertain on a grand scale across three beautifully appointed levels, each opening to expansive outdoor terraces with views of the surrounding peaks. A heated private pool, three spas, radiant heated driveway, and full smart home automation complete this extraordinary offering.',
            'bedroom'        => 10,
            'bathroom'       => 6,
            'garage'         => 18,
            'price'          => '7,900,000',
            'property_area'  => '14900',
            'city'           => 'Truckee',
            'address_line_1' => '101505 Valhalla Drive',
            'zip'            => '96161',
            'main_section'   => 'Video',
            'images'         => [
                'images/demo/Images/Exteriors/Front_driveway.jpg',
                'images/demo/Images/Exteriors/Left.jpg',
                'images/demo/Images/Exteriors/Left_2.jpg',
                'images/demo/Images/Exteriors/Left_decks_Use.jpg',
                'images/demo/Images/Exteriors/Left_upper.jpg',
                'images/demo/Images/Exteriors/back_patio.jpg',
                'images/demo/Images/Exteriors/driveway_2.jpg',
                'images/demo/Images/Exteriors/driveway_3000.jpg',
                'images/demo/Images/Exteriors/exterior_left.jpg',
                'images/demo/Images/Exteriors/exterior_right.jpg',
                'images/demo/Images/Exteriors/front_door.jpg',
                'images/demo/Images/Exteriors/left_decks_close_up.jpg',
                'images/demo/Images/Exteriors/left_side_2.jpg',
                'images/demo/Images/Exteriors/new_lower_left_deck.jpg',
            ],
        ],
        [
            'name'           => 'Alpine Summit Lodge',
            'headline'       => 'Spectacular ski-in/ski-out estate with dream garage and resort-style living',
            'description'    => 'Alpine Summit Lodge is a one-of-a-kind mountain retreat designed for those who demand the very best in four-season living. Spanning 14,900 sq ft across three thoughtfully designed levels, the property offers a gourmet chef\'s kitchen, spa-quality bathrooms, a fully equipped recreation room, bunk room sleeping 12, and a private theater. The lower level opens directly to groomed outdoor entertaining terraces and patios. The signature Dream Garage houses an extraordinary collection of exotic vehicles across a climate-controlled space with phantom car lifts. Located within a private gated community with golf, skiing, and world-class amenities steps from your door.',
            'bedroom'        => 10,
            'bathroom'       => 6,
            'garage'         => 18,
            'price'          => '7,900,000',
            'property_area'  => '14900',
            'city'           => 'Truckee',
            'address_line_1' => '101507 Valhalla Drive',
            'zip'            => '96161',
            'main_section'   => 'Video',
            'images'         => [
                'images/demo/Images/Exteriors/Front_driveway.jpg',
                'images/demo/Images/Exteriors/Left.jpg',
                'images/demo/Images/Exteriors/Left_2.jpg',
                'images/demo/Images/Exteriors/Left_decks_Use.jpg',
                'images/demo/Images/Exteriors/Left_upper.jpg',
                'images/demo/Images/Exteriors/back_patio.jpg',
                'images/demo/Images/Exteriors/driveway_2.jpg',
                'images/demo/Images/Exteriors/driveway_3000.jpg',
                'images/demo/Images/Exteriors/exterior_left.jpg',
                'images/demo/Images/Exteriors/exterior_right.jpg',
                'images/demo/Images/Exteriors/front_door.jpg',
                'images/demo/Images/Exteriors/left_decks_close_up.jpg',
                'images/demo/Images/Exteriors/left_side_2.jpg',
                'images/demo/Images/Exteriors/new_lower_left_deck.jpg',
            ],
        ],
    ];

    // ─── Provision ────────────────────────────────────────────────────────────

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

        // Point directly to the shared demo agent photo (no copy needed)
        $agent->update(['profile_image' => url('images/demo/agent.jpg')]);

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
            $images = $data['images'];
            unset($data['images']);

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

            // Main listing images (Exteriors)
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

            // Video banner (Vimeo)
            PropertyVideos::create([
                'property_id' => $property->id,
                'title'       => 'Property Tour',
                'video_type'  => 'Vimeo',
                'video_url'   => self::VIMEO_BANNER,
                'main_video'  => 1,
                'active'      => 1,
                'featured'    => 1,
            ]);

            // Floorplans
            foreach (self::FLOORPLANS as $i => $fp) {
                PropertyFloorplans::create([
                    'property_id' => $property->id,
                    'name'        => $fp['name'],
                    'file_name'   => $fp['file'],
                    'thumb'       => $fp['file'],
                    'sequence'    => $i + 1,
                    'sort_order'  => $i,
                ]);
            }

            // Photo galleries — images go into property_images first, then linked to gallery
            foreach (self::GALLERIES as $galleryName => $galleryImages) {
                $gallery = PropertyGalleries::create([
                    'property_id' => $property->id,
                    'name'        => $galleryName,
                    'active'      => 1,
                ]);

                foreach ($galleryImages as $seq => $imagePath) {
                    $img = PropertyImages::create([
                        'property_id' => $property->id,
                        'file_name'   => $imagePath,
                        'thumb'       => $imagePath,
                    ]);
                    PropertyGalleryImages::create([
                        'gallery_id'        => $gallery->id,
                        'property_image_id' => $img->id,
                        'featured_image'    => $seq === 0 ? $img->id : 0,
                        'sequence'          => $seq,
                    ]);
                }
            }

            // Documents
            foreach (self::DOCUMENTS as $doc) {
                PropertyDocuments::create([
                    'property_id' => $property->id,
                    'name'        => $doc['name'],
                    'file_name'   => $doc['file'],
                ]);
            }

            // Amenities
            foreach (self::AMENITY_LIST as $amenityName) {
                $amenity = Amenities::firstOrCreate(['name' => $amenityName]);
                PropertyAmenities::create([
                    'property_id' => $property->id,
                    'amenity_id'  => $amenity->id,
                ]);
            }
        }

        // 5. Dispatch credentials email as a queued job so SMTP never blocks the response
        $duration = $type === 'invited' ? '10 days' : '60 minutes';

        \Illuminate\Support\Facades\Log::info('[DemoProvisioning] Dispatching credentials email job', [
            'to'    => $email,
            'token' => $token,
            'type'  => $type,
            'queue' => config('queue.default'),
        ]);

        \App\Jobs\SendDemoCredentialsJob::dispatch(
            email:      $email,
            leadName:   $name,
            token:      $token,
            adminEmail: $admin->email,
            agentEmail: $agent->email,
            password:   $password,
            duration:   $duration,
        );

        return $demoSession;
    }

    // ─── Purge ────────────────────────────────────────────────────────────────

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
