<?php

namespace App\Livewire;

use App\Jobs\SendContactFormEmail;
use App\Models\Properties;
use Illuminate\Support\Facades\Cookie;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class PropertyView extends Component
{
    use LivewireAlert;

    public $property;

    public $state;

    public $agent;

    public $agent_address;

    public $property_amenities;

    public $property_images;

    public $property_topbar_images = [];

    public $property_slider;

    public $property_galleries;

    public $property_gallery_details = [];

    public $property_Random_Gallery_image;

    public $property_Random_Img;

    public $property_documents;

    public $property_floorplans;

    public $currentFloorplanTab;

    public $property_videos;

    public $property_video;

    public $property_video_featured_data = [];

    public $property_matterport;

    public $published;

    public $name;

    public $email;

    public $phone;

    public $comments;

    public function hello()
    {
        dd('herer');
    }

    public function floorplanTab($tabNumber)
    {
        $this->currentFloorplanTab = $tabNumber;
    }

    public function mount($unique_url)
    {
        try {
            $this->property = Properties::where('unique_url', $unique_url)->firstOrFail();
        } catch (\Exception $ex) {
            return back()->with('error', 'This is not a valid property');
        }

        $this->state = $this->property->state;
        $this->agent = $this->property->agentRelation;
        $this->agent_address = $this->agent->agent_address;

        $this->property_amenities = $this->property->property_amenities()->with('Amenities')->get();
        $this->property_images = $this->property->property_images;

        if ($this->property_images->count() > 0) {
            $this->property_topbar_images = $this->property->property_images()->where('id', '=', $this->property->featured_image)->first();
        }

        $this->property_slider = $this->property->property_sliders;
        $this->property_galleries = $this->property->property_galleries;

        // here in the property_gallery_details array we store the property gallery name and description and
        // store property_images_id , galelry_id from property gallery Images table

        if ($this->property_galleries->count() > 0) {
            foreach ($this->property_galleries as $pg) {
                $this->property_Random_Gallery_image = $pg->property_gallery_images()->inRandomOrder()->first();
                // here we store property galary images data with key "images" in array
                $this->property_gallery_details[$pg->id]['images'] = [];

                $featureImage = $this->property_Random_Gallery_image->property_images->file_name;
                foreach ($pg->property_gallery_images as $pgi) {
                    if (! is_null($pgi->property_images)) {
                        $tmp = [];
                        $tmp['property_image_id'] = $pgi->property_image_id;
                        $tmp['file_name'] = $pgi->property_images['file_name'];
                        $tmp['thumb_name'] = $pgi->property_images['thumb'];
                        $tmp['featured_image'] = $pgi->featured_image;
                        if ($pgi->featured_image) {
                            $featureImage = $pgi->property_images['file_name'];
                        }
                        if (count($tmp) != 0) {
                            $this->property_gallery_details[$pgi->gallery_id]['images'][] = $tmp;
                        }
                    }
                }
                //$this->property_gallery_details[$pg->id]['random_file_name'] = $this->property_Random_Gallery_image[0]->property_images->file_name;
                $this->property_gallery_details[$pg->id]['random_file_name'] = $featureImage;
                $this->property_gallery_details[$pg->id]['gallery_name'] = $pg->name;
                $this->property_gallery_details[$pg->id]['property_id'] = $pg->property_id;
                $this->property_gallery_details[$pg->id]['short_description'] = $pg->short_description;
            }
        }

        // get random image from property images table
        $this->property_Random_Img = $this->property->property_images()->inRandomOrder()->first();
        $this->property_documents = $this->property->property_documents;

        $this->property_floorplans = $this->property->property_floorplans()->with(['hotspots.propertyImages'])->orderBy('sort_order')->get();
        $this->currentFloorplanTab = $this->property_floorplans->first()?->id ?? 1;

        $this->property_videos = $this->property->property_videos;
        $this->property_matterport = $this->property->property_matterports;

        //todo:check featured video section.
        if ($this->property_videos->count() > 0) {
            foreach ($this->property_videos as $property_video) {
                if ($property_video->featured == 1) {
                    $this->property_video_featured_data = $property_video->featured;
                }
            }
        }

        if (Cookie::get('property_id') == '') {
            Cookie::queue('property_id', 1, 30);
            $this->property->increment('views');
        }
        $agent_session_data = session('agent');
        $admin_session_data = session('admin');

        if (! is_null($admin_session_data) || ! is_null($agent_session_data) || ($this->property->published == 1 && $this->property->reviewed == 1) || request()->get('share') == true) {
            $this->published = true;
        } else {
            $this->published = false;
        }
    }

    public function submitContactForm()
    {
        // Demo mode: skip email dispatch and show notice
        if (session('demo_session_id')) {
            $this->alert('info', 'Demo mode — no email was sent. In the live system this would notify the agent.', [
                'toast'    => true,
                'position' => 'top-end',
                'timer'    => 6000,
            ]);
            $this->reset(['name', 'email', 'phone', 'comments']);
            return;
        }

        $user = [
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'message' => $this->comments,
        ];
        $agent = $this->property->agentRelation;
        $agent['userEmail'] = $agent->email;

        dispatch(new SendContactFormEmail($user, $agent));

        $this->alert('success', 'Your message has been sent successfully!', [
            'toast' => true,
        ]);

        $this->reset(['name', 'email', 'phone', 'comments']);
    }

    public function render()
    {
        return view('livewire.property-view');
    }

    public function rules()
    {
        [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string', // Adjust validation as needed
            'comments' => 'nullable|string',
        ];
    }
}
