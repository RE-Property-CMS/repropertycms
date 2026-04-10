<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DemoController;
use App\Models\Agent_addresses;
use App\Models\Agents;
use App\Models\Amenities;
use App\Models\DemoSession;
use App\Models\Properties;
use App\Models\PropertyImages;
use App\Models\PropertyMatterport;
use App\Models\PropertyVideos;
use App\Models\PropertyAmenities;
use App\Models\PropertyFloorplans;
use App\Models\PropertyGalleries;
use App\Models\PropertyGalleryImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeederController extends Controller
{
    const DEMO_EMAIL = 'demo.agent@demo.reproperty.local';

    private function isDemo(): bool
    {
        return !empty(session('demo_session_id'));
    }

    /*
    |--------------------------------------------------------------------------
    | Seed
    |--------------------------------------------------------------------------
    | Demo session: creates 8 luxury listings for the current session's agent.
    | Real admin:   creates a separate demo agent + 8 listings (legacy behaviour).
    */
    public function seed(Request $request)
    {
        if ($this->isDemo()) {
            $token = session('demo_session_id');

            // Guard against double-seeding using actual DB count, not a session flag
            $existing = Properties::withoutGlobalScopes()
                ->where('demo_session_id', $token)
                ->count();

            if ($existing > 0) {
                return back()->with('demo_error', 'Demo listings already exist for this session (' . $existing . ' properties). Use Reset Demo to clear them first.');
            }

            $demoSession = DemoSession::where('token', $token)->first();
            if (! $demoSession) {
                return back()->with('demo_error', 'Demo session record not found.');
            }

            $agent = Agents::withoutGlobalScopes()->find($demoSession->agent_id);
            if (! $agent) {
                return back()->with('demo_error', 'Demo agent not found.');
            }

            $this->createListingsForSession($token, $agent->id);

            return back()->with('demo_success', '8 luxury listings have been created for this demo session.');
        }

        // ── Real admin: legacy behaviour ─────────────────────────────────────
        if (Agents::where('email', self::DEMO_EMAIL)->exists()) {
            return back()->with('demo_error', 'Demo data already exists. Reset it first if you want to re-seed.');
        }

        DB::transaction(function () {
            $this->createSharedDemoData();
        });

        return back()->with('demo_success', 'Demo data seeded. Agent: demo.agent@demo.reproperty.local / Demo@12345!');
    }

    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    | Demo session: wipes all session properties and re-creates fresh listings.
    | Real admin:   deletes the shared demo agent (FK cascade removes listings).
    */
    public function reset(Request $request)
    {
        if ($this->isDemo()) {
            $token = session('demo_session_id');

            // Collect property IDs for this session first
            $propertyIds = Properties::withoutGlobalScopes()
                ->where('demo_session_id', $token)
                ->withTrashed()
                ->pluck('id')
                ->toArray();

            if (! empty($propertyIds)) {
                // Delete uploaded files from S3 / local disk before wiping DB records
                DemoController::deletePropertyFiles($propertyIds);

                // Delete child records explicitly before removing properties
                // (avoids FK constraint failures if the DB has no cascade rules)
                DB::table('property_amenities')->whereIn('property_id', $propertyIds)->delete();
                DB::table('property_images')->whereIn('property_id', $propertyIds)->delete();
                DB::table('property_videos')->whereIn('property_id', $propertyIds)->delete();
                DB::table('property_matterport')->whereIn('property_id', $propertyIds)->delete();
                DB::table('property_floorplans')->whereIn('property_id', $propertyIds)->delete();
                DB::table('property_slider')->whereIn('property_id', $propertyIds)->delete();
                DB::table('property_documents')->whereIn('property_id', $propertyIds)->delete();

                $galleryIds = DB::table('property_galleries')
                    ->whereIn('property_id', $propertyIds)
                    ->pluck('id')
                    ->toArray();
                if (! empty($galleryIds)) {
                    DB::table('property_gallery_images')->whereIn('gallery_id', $galleryIds)->delete();
                    DB::table('property_galleries')->whereIn('id', $galleryIds)->delete();
                }

                // Hard-delete the properties themselves (bypasses SoftDeletes)
                DB::table('properties')->whereIn('id', $propertyIds)->delete();
            }

            return back()->with('demo_success', 'Demo listings cleared. Click Seed Demo to populate fresh listings.');
        }

        // ── Real admin: legacy behaviour ─────────────────────────────────────
        $agent = Agents::where('email', self::DEMO_EMAIL)->first();

        if (! $agent) {
            return back()->with('demo_error', 'No demo data found. Seed it first.');
        }

        // Regular delete; FK cascade removes properties, images, amenities, addresses
        $agent->delete();

        return back()->with('demo_success', 'Demo data has been completely removed.');
    }

    /*
    |--------------------------------------------------------------------------
    | Create listings for a demo session
    |--------------------------------------------------------------------------
    | Builds all 8 luxury listings tagged to the given session token and
    | assigns them to the session's existing agent. No new user is created.
    */
    private function createListingsForSession(string $token, int $agentId): void
    {
        DB::transaction(function () use ($token, $agentId) {
            $usCountry = DB::table('countries')->where('code', 'US')->value('country_id') ?? 1;
            $caState   = DB::table('states')->where('code', 'CA')->value('state_id') ?? 8;
            $nyState   = DB::table('states')->where('code', 'NY')->value('state_id') ?? 33;
            $flState   = DB::table('states')->where('code', 'FL')->value('state_id') ?? 10;
            $coState   = DB::table('states')->where('code', 'CO')->value('state_id') ?? 9;
            $azState   = DB::table('states')->where('code', 'AZ')->value('state_id') ?? 5;

            $amenityMap = Amenities::where('agent_id', 0)->pluck('id', 'name');

            $interiorPool = [
                ['images/demo/interior-kitchen.jpg',  'images/demo/interior-kitchen-1.jpg'],
                ['images/demo/interior-bedroom.jpg',  'images/demo/interior-bedroom-1.jpg'],
                ['images/demo/interior-living.jpg',   'images/demo/interior-living-1.jpg'],
                ['images/demo/interior-bathroom.jpg', 'images/demo/interior-bathroom-1.jpg'],
            ];

            $this->buildListings($token, $agentId, $usCountry, $caState, $nyState, $flState, $coState, $azState, $amenityMap, $interiorPool);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Create shared demo data (real admin — legacy)
    |--------------------------------------------------------------------------
    | Creates the demo.agent@demo.reproperty.local agent and 8 listings
    | with no demo_session_id (shared across all real-admin views).
    */
    private function createSharedDemoData(): void
    {
        $agent = Agents::create([
            'first_name'        => 'Demo',
            'last_name'         => 'Agent',
            'email'             => self::DEMO_EMAIL,
            'password'          => Hash::make('Demo@12345!'),
            'email_verified_at' => now(),
            'credit_balance'    => 50,
            'verification_code' => 'demo',
            'active'            => true,
            'deleted'           => false,
            'profile_image'     => 'images/demo/agent.jpg',
        ]);

        $usCountry = DB::table('countries')->where('code', 'US')->value('country_id') ?? 1;
        $caState   = DB::table('states')->where('code', 'CA')->value('state_id') ?? 8;
        $nyState   = DB::table('states')->where('code', 'NY')->value('state_id') ?? 33;
        $flState   = DB::table('states')->where('code', 'FL')->value('state_id') ?? 10;
        $coState   = DB::table('states')->where('code', 'CO')->value('state_id') ?? 9;
        $azState   = DB::table('states')->where('code', 'AZ')->value('state_id') ?? 5;

        Agent_addresses::create([
            'agent_id'      => $agent->id,
            'business_name' => 'Prestige Luxury Realty',
            'phone'         => '+1 (310) 555-0190',
            'address'       => '9876 Wilshire Blvd, Suite 500',
            'city'          => 'Beverly Hills',
            'state_id'      => $caState,
            'country_id'    => $usCountry,
            'zip'           => '90210',
        ]);

        $amenityMap   = Amenities::where('agent_id', 0)->pluck('id', 'name');
        $interiorPool = [
            ['images/demo/interior-kitchen.jpg',  'images/demo/interior-kitchen-1.jpg'],
            ['images/demo/interior-bedroom.jpg',  'images/demo/interior-bedroom-1.jpg'],
            ['images/demo/interior-living.jpg',   'images/demo/interior-living-1.jpg'],
            ['images/demo/interior-bathroom.jpg', 'images/demo/interior-bathroom-1.jpg'],
        ];

        // null token → no demo_session_id on these properties
        $this->buildListings(null, $agent->id, $usCountry, $caState, $nyState, $flState, $coState, $azState, $amenityMap, $interiorPool);
    }

    /*
    |--------------------------------------------------------------------------
    | buildListings — shared listing factory
    |--------------------------------------------------------------------------
    | Creates all 8 luxury properties with images, videos, matterport,
    | galleries, and floor plans. Pass $token=null for shared (non-demo) data.
    */
    private function buildListings(
        ?string $token,
        int $agentId,
        int $usCountry,
        int $caState,
        int $nyState,
        int $flState,
        int $coState,
        int $azState,
        $amenityMap,
        array $interiorPool
    ): void {
        $matterportUrl = 'https://my.matterport.com/show/?m=SxQL3iGyvde';

        $youtubeVideos = [
            'https://youtu.be/5SEE7h4TkAQ',
            'https://youtu.be/LzLQsA7aTe0',
            'https://youtu.be/2XqSwvqz9qw',
            'https://youtu.be/oClahBxFhwA',
            'https://youtu.be/qBSs0OQjJUU',
            'https://youtu.be/gOQpGr8TGOA',
            'https://youtu.be/F2ycFULarXM',
            'https://youtu.be/Ek3l6Q2VKOE',
        ];

        $listings = [
            [
                'property' => [
                    'name'           => 'Sunset Ridge Estate',
                    'headline'       => 'Panoramic canyon views from every room in this magnificent hilltop compound',
                    'description'    => 'Perched above the canyons of Beverly Hills, Sunset Ridge Estate is a masterpiece of contemporary California architecture. Floor-to-ceiling glass walls dissolve the boundary between indoors and out, framing sweeping views of the Pacific Ocean and the Los Angeles basin. The 11,400 sq ft residence features a chef\'s kitchen with Miele and Sub-Zero appliances, a 12-seat home theatre, a resort-style infinity pool, and a 1,200-bottle climate-controlled wine cellar. The primary suite occupies an entire wing, with a spa bath clad in Calacatta marble and a private terrace overlooking the city lights below.',
                    'bedroom'        => 6,
                    'bathroom'       => 8,
                    'garage'         => 4,
                    'parking_spaces' => 8,
                    'unique_url'     => 'demo-sunset-ridge-estate-' . Str::random(5),
                    'address_line_1' => '1420 Sunset Ridge Drive',
                    'address_line_2' => 'Beverly Hills',
                    'city'           => 'Beverly Hills',
                    'state_id'       => (string) $caState,
                    'country_id'     => $usCountry,
                    'zip'            => '90210',
                    'latitude'       => '34.0736',
                    'longitude'      => '-118.4004',
                    'price'          => '12,500,000',
                    'property_area'  => '11,400',
                    'levels'         => 3,
                ],
                'amenities' => ['Pool', 'Heated Pool', 'Spa', 'City Lights Views', 'Gated Community', 'High Ceilings', 'Walk-In Closets', 'Large Kitchen', 'Oversized Windows'],
            ],
            [
                'property' => [
                    'name'           => 'Crystal Waters Villa',
                    'headline'       => 'Direct oceanfront living with private beach access in the heart of Miami Beach',
                    'description'    => 'Crystal Waters Villa redefines waterfront luxury on the most coveted stretch of Miami Beach. This eight-bedroom trophy estate commands 150 feet of direct Atlantic Ocean frontage, with a private beachside pavilion, two infinity-edge pools, and a full outdoor summer kitchen. The interior is a celebration of natural light — white travertine floors, soaring coffered ceilings, and custom Italian millwork throughout. The media room, gym, and staff quarters are seamlessly integrated. A private dock accommodates vessels up to 65 feet. Being offered fully furnished with museum-quality art collection.',
                    'bedroom'        => 8,
                    'bathroom'       => 10,
                    'garage'         => 3,
                    'parking_spaces' => 10,
                    'unique_url'     => 'demo-crystal-waters-villa-' . Str::random(5),
                    'address_line_1' => '5700 Collins Avenue',
                    'address_line_2' => 'Miami Beach',
                    'city'           => 'Miami Beach',
                    'state_id'       => (string) $flState,
                    'country_id'     => $usCountry,
                    'zip'            => '33140',
                    'latitude'       => '25.8225',
                    'longitude'      => '-80.1220',
                    'price'          => '8,750,000',
                    'property_area'  => '9,800',
                    'levels'         => 2,
                ],
                'amenities' => ['Beach Access', 'Ocean Views', 'Heated Pool', 'Spa', 'Gated Community', 'High Ceilings', 'Open Floor Plan', 'Stainless Steel Appliances'],
            ],
            [
                'property' => [
                    'name'           => 'Alpine Chalet Retreat',
                    'headline'       => 'Ski-in / ski-out luxury chalet steps from the world-famous Aspen Mountain lifts',
                    'description'    => 'Escape to unrivalled mountain living in this extraordinary ski-in / ski-out chalet in Aspen\'s most prestigious enclave. The 8,200 sq ft residence combines the warmth of hand-hewn timber beams with the sophistication of a boutique hotel. A two-sided stone fireplace anchors the great room, where floor-to-ceiling windows frame Pyramid Peak in its entirety. The chef\'s kitchen opens onto an expansive heated stone terrace with a sunken hot tub and panoramic mountain views. Five en-suite bedrooms, a bunk room sleeping six, a home gym, and a private elevator complete this incomparable mountain sanctuary.',
                    'bedroom'        => 6,
                    'bathroom'       => 7,
                    'garage'         => 2,
                    'parking_spaces' => 4,
                    'unique_url'     => 'demo-alpine-chalet-retreat-' . Str::random(5),
                    'address_line_1' => '344 Monarch Street',
                    'address_line_2' => 'Aspen',
                    'city'           => 'Aspen',
                    'state_id'       => (string) $coState,
                    'country_id'     => $usCountry,
                    'zip'            => '81611',
                    'latitude'       => '39.1911',
                    'longitude'      => '-106.8175',
                    'price'          => '6,200,000',
                    'property_area'  => '8,200',
                    'levels'         => 3,
                ],
                'amenities' => ['Mountain Views', 'Heated Pool', 'Spa', 'High Ceilings', 'Heated Floors', 'Large Kitchen', 'Quiet and Private', 'Walk-In Closets'],
            ],
            [
                'property' => [
                    'name'           => 'Manhattan Sky Penthouse',
                    'headline'       => 'Full-floor trophy penthouse commanding 360° views over Central Park and the Manhattan skyline',
                    'description'    => 'Occupying the entire 72nd floor of one of Midtown\'s most iconic towers, this extraordinary full-floor penthouse delivers an unparalleled 360-degree panorama of Central Park, the Hudson River, and the Manhattan skyline from every room. The 6,800 sq ft residence features soaring 12-foot ceilings, an open-plan great room perfect for grand-scale entertaining, a chef\'s kitchen with La Cornue range and Gaggenau appliances, and a 3,000-bottle wine room. The primary suite includes dual spa bathrooms and a private wrap-around terrace. White-glove building services include a private concierge, valet, and access to an amenity floor with pool, spa, and screening room.',
                    'bedroom'        => 4,
                    'bathroom'       => 5,
                    'garage'         => 2,
                    'parking_spaces' => 2,
                    'unique_url'     => 'demo-manhattan-sky-penthouse-' . Str::random(5),
                    'address_line_1' => '432 Park Avenue, Floor 72',
                    'address_line_2' => 'Midtown Manhattan',
                    'city'           => 'New York',
                    'state_id'       => (string) $nyState,
                    'country_id'     => $usCountry,
                    'zip'            => '10022',
                    'latitude'       => '40.7614',
                    'longitude'      => '-73.9740',
                    'price'          => '15,000,000',
                    'property_area'  => '6,800',
                    'levels'         => 1,
                ],
                'amenities' => ['City Lights Views', 'Community Pool', 'Spa', 'High Ceilings', 'Oversized Windows', 'Open Floor Plan', 'Quartz Countertops', 'Stainless Steel Appliances'],
            ],
            [
                'property' => [
                    'name'           => 'Pacific Cliffside Manor',
                    'headline'       => 'Dramatic oceanfront bluff estate with private funicular to a secluded sandy cove',
                    'description'    => 'Set on a dramatic Pacific Ocean bluff in Point Dume, Malibu\'s most coveted enclave, Pacific Cliffside Manor is a singular architectural achievement. Designed by an award-winning Los Angeles firm, the 10,200 sq ft estate is positioned to maximise panoramic Pacific vistas from every principal room. A private funicular descends to a secluded white-sand cove exclusively for residents. The residence features a 60-foot lap pool cantilevered over the bluff edge, a professional recording studio, a climate-controlled car gallery for 8 vehicles, and 5 en-suite guest suites. Full smart-home integration with Crestron throughout.',
                    'bedroom'        => 5,
                    'bathroom'       => 7,
                    'garage'         => 8,
                    'parking_spaces' => 12,
                    'unique_url'     => 'demo-pacific-cliffside-manor-' . Str::random(5),
                    'address_line_1' => '7500 Cliffside Drive',
                    'address_line_2' => 'Point Dume',
                    'city'           => 'Malibu',
                    'state_id'       => (string) $caState,
                    'country_id'     => $usCountry,
                    'zip'            => '90265',
                    'latitude'       => '34.0259',
                    'longitude'      => '-118.8068',
                    'price'          => '9,800,000',
                    'property_area'  => '10,200',
                    'levels'         => 3,
                ],
                'amenities' => ['Ocean Views', 'Beach Access', 'Pool', 'Gated Community', 'Large Lot', 'High Ceilings', 'Oversized Windows', 'Walk-In Closets'],
            ],
            [
                'property' => [
                    'name'           => 'Desert Rose Estate',
                    'headline'       => 'Award-winning desert contemporary on a premier golf course lot with mountain backdrop',
                    'description'    => 'Conceived by one of Arizona\'s most celebrated architects, Desert Rose Estate is a triumph of desert contemporary design on a prime golf course lot in Scottsdale\'s guard-gated Silverleaf community. The 7,600 sq ft home features dramatic negative-edge pool and spa, a championship-grade outdoor putting green, a two-lane bowling alley, and a rooftop terrace with a gas firepit and 360° mountain views. Interior finishes include honed limestone floors, bleached oak millwork, and chef\'s kitchen with dual-island configuration. The four-car garage is air-conditioned with epoxy floors and custom cabinetry — ideal for the serious collector.',
                    'bedroom'        => 5,
                    'bathroom'       => 6,
                    'garage'         => 4,
                    'parking_spaces' => 8,
                    'unique_url'     => 'demo-desert-rose-estate-' . Str::random(5),
                    'address_line_1' => '20800 N 87th Street',
                    'address_line_2' => 'Silverleaf',
                    'city'           => 'Scottsdale',
                    'state_id'       => (string) $azState,
                    'country_id'     => $usCountry,
                    'zip'            => '85255',
                    'latitude'       => '33.7295',
                    'longitude'      => '-111.8639',
                    'price'          => '4,500,000',
                    'property_area'  => '7,600',
                    'levels'         => 2,
                ],
                'amenities' => ['Mountain Views', 'Pool', 'Heated Pool', 'Spa', 'Golf Course Lot', 'Gated Community', 'High Ceilings', 'Large Kitchen', 'Quartz Countertops'],
            ],
            [
                'property' => [
                    'name'           => 'Lake Geneva Grand Residence',
                    'headline'       => 'Lakefront estate with private pier, boathouse, and manicured grounds on 2.4 private acres',
                    'description'    => 'Commanding 180 feet of pristine Lake Geneva shoreline, this timeless shingle-style estate has been comprehensively renovated to the highest standard while preserving its storied character. The 6,900 sq ft main residence features a great hall with original coffered ceilings, a gourmet kitchen with butler\'s pantry, and a sunroom that opens directly to the lakefront terrace. The grounds include a deep-water pier for large vessels, a fully equipped boathouse with guest accommodation above, a regulation clay tennis court, and a heated in-ground pool. A three-bedroom guest cottage provides additional flexibility. Offered fully furnished.',
                    'bedroom'        => 7,
                    'bathroom'       => 8,
                    'garage'         => 3,
                    'parking_spaces' => 10,
                    'unique_url'     => 'demo-lake-geneva-residence-' . Str::random(5),
                    'address_line_1' => '1402 South Lakeshore Drive',
                    'address_line_2' => '',
                    'city'           => 'Lake Geneva',
                    'state_id'       => (string) (DB::table('states')->where('code', 'WI')->value('state_id') ?? 49),
                    'country_id'     => $usCountry,
                    'zip'            => '53147',
                    'latitude'       => '42.5917',
                    'longitude'      => '-88.4334',
                    'price'          => '3,200,000',
                    'property_area'  => '6,900',
                    'levels'         => 3,
                ],
                'amenities' => ['Pool', 'Heated Pool', 'Large Lot', 'Quiet and Private', 'Hardwood Floors', 'High Ceilings', 'Large Kitchen', 'Walk-In Closets'],
            ],
            [
                'property' => [
                    'name'           => 'Coastal Breeze Estate',
                    'headline'       => 'Timeless Spanish Colonial revival steps from East Beach with ocean views and guest house',
                    'description'    => 'Presenting one of Santa Barbara\'s most distinguished addresses — a museum-quality Spanish Colonial Revival estate on a rare double lot steps from East Beach. Restored by award-winning preservation architects, the 8,500 sq ft main residence retains its original 1928 craftsmanship: hand-painted Tunisian tile, original iron fixtures, and arched loggia — while being fully upgraded with modern systems. The grounds feature a separate 1,200 sq ft guest house, a resort-style pool with cabana, an outdoor kitchen, and mature olive and citrus groves. Ocean and mountain views from multiple terraces. A once-in-a-generation opportunity in one of California\'s most desirable communities.',
                    'bedroom'        => 6,
                    'bathroom'       => 7,
                    'garage'         => 3,
                    'parking_spaces' => 8,
                    'unique_url'     => 'demo-coastal-breeze-estate-' . Str::random(5),
                    'address_line_1' => '1850 East Cabrillo Blvd',
                    'address_line_2' => '',
                    'city'           => 'Santa Barbara',
                    'state_id'       => (string) $caState,
                    'country_id'     => $usCountry,
                    'zip'            => '93103',
                    'latitude'       => '34.4031',
                    'longitude'      => '-119.6753',
                    'price'          => '5,750,000',
                    'property_area'  => '8,500',
                    'levels'         => 2,
                ],
                'amenities' => ['Ocean Views', 'Pool', 'Spa', 'Beach Access', 'Large Lot', 'Hardwood Floors', 'Open Floor Plan', 'Quartz Countertops', 'Stainless Steel Appliances'],
            ],
        ];

        foreach ($listings as $i => $listing) {
            $mainSection = $i < 4 ? 'Video' : 'Image';

            $propertyData = array_merge($listing['property'], [
                'agent_id'        => $agentId,
                'matterport_data' => '',
                'views'           => rand(150, 980),
                'published'       => false,
                'featured_image'  => 0,
                'main_section'    => $mainSection,
            ]);

            // Tag with demo_session_id when seeding for a session
            if ($token !== null) {
                $propertyData['demo_session_id'] = $token;
            }

            $property = Properties::withoutGlobalScopes()->create($propertyData);

            $v = $i % 2;
            $propertyImages = [
                'images/demo/prop-' . ($i + 1) . '.jpg',
                $interiorPool[0][$v],
                $interiorPool[1][$v],
                $interiorPool[2][$v],
                $interiorPool[3][$v],
            ];

            $imageIds = [];
            foreach ($propertyImages as $img) {
                $image = PropertyImages::create([
                    'property_id' => $property->id,
                    'file_name'   => $img,
                    'thumb'       => $img,
                ]);
                $imageIds[] = $image->id;
            }

            $property->update(['featured_image' => $imageIds[0]]);

            foreach ($listing['amenities'] as $amenityName) {
                if (isset($amenityMap[$amenityName])) {
                    PropertyAmenities::create([
                        'property_id' => $property->id,
                        'amenity_id'  => $amenityMap[$amenityName],
                    ]);
                }
            }

            PropertyVideos::create([
                'property_id' => $property->id,
                'title'       => $listing['property']['name'] . ' — Property Tour',
                'file_name'   => null,
                'video_type'  => 'YouTube',
                'video_url'   => $youtubeVideos[$i],
                'featured'    => true,
                'main_video'  => true,
                'active'      => true,
            ]);

            PropertyMatterport::create([
                'property_id'    => $property->id,
                'matterport_url' => $matterportUrl,
            ]);

            $gallery1 = PropertyGalleries::create([
                'property_id'       => $property->id,
                'name'              => 'Interior Showcase',
                'short_description' => 'Curated interior photography highlighting the finest finishes and living spaces.',
                'active'            => true,
            ]);
            foreach ([$imageIds[1], $imageIds[2]] as $seq => $imgId) {
                PropertyGalleryImages::create([
                    'gallery_id'        => $gallery1->id,
                    'property_image_id' => $imgId,
                    'featured_image'    => $seq === 0 ? 1 : 0,
                    'sequence'          => $seq,
                ]);
            }

            $gallery2 = PropertyGalleries::create([
                'property_id'       => $property->id,
                'name'              => 'Exterior & Grounds',
                'short_description' => 'Sweeping exterior views, landscaping, and outdoor entertaining areas.',
                'active'            => true,
            ]);
            foreach ([$imageIds[0], $imageIds[3]] as $seq => $imgId) {
                PropertyGalleryImages::create([
                    'gallery_id'        => $gallery2->id,
                    'property_image_id' => $imgId,
                    'featured_image'    => $seq === 0 ? 1 : 0,
                    'sequence'          => $seq,
                ]);
            }

            PropertyFloorplans::create([
                'property_id' => $property->id,
                'name'        => 'Ground Floor',
                'file_name'   => $interiorPool[2][$v],
                'thumb'       => $interiorPool[2][$v],
                'sequence'    => 1,
                'sort_order'  => 0,
            ]);
        }
    }
}
