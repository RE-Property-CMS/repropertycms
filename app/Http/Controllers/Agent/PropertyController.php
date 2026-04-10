<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agents;
use App\Models\Backend\Admin;
use App\Models\Amenities;
use App\Models\CreditLog;
use App\Models\Properties;
use App\Models\PropertyImages;
use App\Models\PropertyMatterport;
use App\Models\PropertyVideos;
use App\Models\PropertyAmenities;
use App\Models\PropertyDocuments;
use App\Models\PropertyFloorplanImages;
use App\Models\PropertyFloorplans;
use App\Models\PropertyGalleries;
use App\Models\PropertyGalleryImages;
use App\Models\PropertySlider;
use App\Models\States;
use App\Models\Subscription;
use App\Notifications\PropertyPublished;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Log;
use Storage;

class PropertyController extends Controller
{
    public function listing(Request $request)
    {
        $request->session()->forget('property');

        $agent = auth()->user();

        $properties = $agent->properties()  // Use Eloquent relationship
            ->with([
                'property_images',
                'property_floorplans',
                'state', // Eager load state relationship
            ])
            ->get();

        return view('agent.Property.listing', compact('properties', 'agent'));
    }

    //    public function listing_old(Request $request)
    //    {
    //        $request->session()->forget('property');
    //        $agent = auth('agent')->user();
    //        $properties = Properties::where('agent_id', '=', $agent->id)->with('property_images', 'property_floorplans')->get();
    //        if ($properties->count() > 0) {
    //            foreach ($properties as $property) {
    //                $state_name = States::where('state_id', '=', $property->state_id)->first();
    //            }
    //        } else {
    //            $state_name = '';
    //        }
    //
    //        return view('agent/Property/listing', compact('properties', 'agent', 'state_name'));
    //    }

    public function address($propertyId = null) // Use camelCase for consistency
    {
        $agent = auth()->user();

        $states = States::all();

        if ($propertyId) {
            $property = $agent->properties()->with(['country', 'state'])
                ->findOrFail($propertyId);

            session()->put('property', $property);
        }

        return view('agent.Property.address', [
            'property' => $property ?? null, // Pass as null if not found
            'states' => $states,
        ]);
    }
    // Action For View Property Address Page
    //    public function address_old($property_id = null)
    //    {
    //        $agent = session('agent');
    //        $states = States::all();
    //
    //        if (! is_null($property_id)) {
    //            $property = Properties::where('id', '=', $property_id)->with('country', 'state')->first();
    //
    //            //Add in session for pages in Property editor
    //            session()->put('property', $property);
    //
    //            return view('agent/Property/address', compact('property', 'states'));
    //        } else {
    //            return view('agent/Property/address', compact('states'));
    //        }
    //    }

    // Action For View Property Amenities Page
    public function amenities()
    {
        $property = session('property');

        if (! $property || ! $property->id) {
            return $property
                ? view('agent.Property.address')
                : redirect('agent/property/listing')->with('error', 'You cannot access Amenities directly!');
        }

        $property = Properties::findOrFail($property->id); // Eager load relationships

        session()->put('property', $property);

        return view('agent.Property.amenities', compact('property'));
    }
    //    public function amenities()
    //    {
    //        $propertie = session('property');
    //        if (! is_null($propertie)) {
    //            if ($propertie->id) {
    //                $property = Properties::find($propertie->id);
    //                // check for display all amenities of agent_id = 0 and logged in agent_id
    //                $amenities = Amenities::where('agent_id', '=', 0)->orWhere('agent_id', '=', $property->agent_id)->get();
    //
    //                //Add in session for pages in Property editor
    //                session()->put('property', $property);
    //
    //                $amenities_array = [];
    //                if (isset($property->property_amenities)) {
    //                    foreach ($property->property_amenities as $property_amenities) {
    //                        $amenities_array[] = $property_amenities->amenity_id;
    //                    }
    //                }
    //
    //                return view('agent/Property/amenities', compact('amenities', 'property', 'amenities_array'));
    //            } else {
    //                return view('agent/Property/address');
    //            }
    //        } else {
    //            return redirect('agent/property/listing')->with('error', 'You can not access Amenities directly !');
    //        }
    //    }

    //  Action For View Property Description Page
    public function description()
    {
        $property = session('property');

        if (! $property || ! $property->id) {
            return $property
                ? view('agent.Property.address')
                : redirect('agent/property/listing')->with('error', 'You cannot access Description directly!');
        }

        $property = Properties::findOrFail($property->id); // Use findOrFail for error handling

        return view('agent.Property.description', compact('property'));
    }

    // Action For View Property Price Feature Page
    public function priceFeature()
    {
        $property = session('property');

        if (! $property || ! $property->id) {
            return $property
                ? view('agent.Property.address')
                : redirect('agent/property/listing')->with('error', 'You cannot access Property Price Feature directly!');
        }

        $property = Properties::findOrFail($property->id);

        return view('agent.Property.price_feature', compact('property'));
    }

    public function default()
    {
        $property = session('property');

        if (! $property || ! $property->id) {
            return $property
                ? view('agent.Property.address')
                : redirect('agent/property/listing')->with('error', 'You cannot access Property Price Feature directly!');
        }

        $property = Properties::findOrFail($property->id);

        return view('agent.Property.default', compact('property')); // Pass the updated $property
    }

    public function PublishProperty($propertyId)
    {
        $agent = auth()->user();

        // Future Stripe subscription check (uncomment when ready)
        // if ($agent->properties->count() >= $agent->activeSubscriptionCredits()) {
        //    return back()->with('error', 'No credits to publish. Add credits in Billing.');
        // }

        $property = Properties::findOrFail($propertyId);

        $property->published = 1;
        $property->reviewed = 1;
        $property->publish_date = Carbon::now();
        $property->expiry_date = $property->expiry_date ?? Carbon::now()->addDays(90); // Set if null

        if (! $property->save()) {
            return back()->with('error', 'Error publishing property.'); // Concise error message
        }

        // Handle credit log updates
        if (! $this->updateCreditLog($agent, $propertyId)) {
            return back()->with('error', 'Failed to update credit log.');
        }

        // Send notification email (extracted to separate method)
        $this->sendPropertyPublishedNotification($property, $agent);

        return back()->with('success', 'Property is published');
    }

    // Extract credit log update logic to a separate method
    private function updateCreditLog($agent, $propertyId)
    {
        if (CreditLog::where('property_id', $propertyId)
            ->where('credits', -1)
            ->where('type', 'Spent')
            ->exists()) {
            return true; // Credit log already exists
        }

        $agent->credit_balance -= 1;

        if (! $agent->save()) {
            return false;
        }

        $creditLog = new CreditLog;
        $creditLog->agent_id = $agent->id;
        $creditLog->property_id = $propertyId;
        $creditLog->credits = -1;
        $creditLog->type = 'Spent';

        return $creditLog->save();
    }

    // Extract notification email sending logic to a separate method
    private function sendPropertyPublishedNotification(Properties $property, Agents $agent)
    {
        $data = [
            'name' => $property->name,
            'address' => $this->formatPropertyAddress($property),
            'url' => url($property->unique_url),
            'agent' => $agent,
        ];

        $adminEmail = Admin::first()?->email;
        if ($adminEmail) {
            Notification::route('mail', $adminEmail)
                ->notify(new PropertyPublished($data));
        }
    }

    // Extract property address formatting logic to a separate method
    private function formatPropertyAddress(Properties $property)
    {
        $addressParts = array_filter([
            $property->address_line_1,
            $property->city,
            $property->zip,
        ]);

        return implode(', ', $addressParts);
    }

    public function deleteProperty($propertyId)
    {
        $agent = auth()->user();

        try {
            $property = Properties::where('id', $propertyId)
                ->where('agent_id', $agent->id)
                ->firstOrFail();

            DB::transaction(function () use ($property) {
                $this->deleteRelatedPropertyData($property);

                if (! $property->delete()) {
                    throw new \Exception('Error deleting property.');
                }
            });

            return redirect()->route('agent.dashboard')->with('success', 'Property is removed');

        } catch (\Exception $e) {
            Log::info($e->getMessage());

            return back()->with('error', 'Error deleting property.'); // Generic error for security
        }
    }

    public function confirmDeleteProperty()
    {
        $property = session('property');
        if (! $property || ! $property->id) {
            return $property
                ? view('agent.Property.default')
                : redirect('agent/property/default')->with('error', 'You cannot access Property directly!');
        }
        $property = Properties::findOrFail($property->id);
        return view('agent.Property.delete-property', compact('property')); // Pass the updated $property
    }

    // Extracted method to handle deletion of related property data
    private function deleteRelatedPropertyData(Properties $property)
    {
        // Delete related data (amenities, documents, floor plans, galleries, images, etc.)
        $propertyGalleries = $property->propertyGalleries ? $property->propertyGalleries->pluck('id') : [];
        PropertyAmenities::where('property_id', $property->id)->delete();
        $this->deleteS3ImagesFromModel($property->propertyDocuments ?? [], 'property_documents/');
        PropertyDocuments::where('property_id', $property->id)->delete();
        PropertyFloorplanImages::where('property_id', $property->id)->delete();
        $this->deleteS3ImagesFromModel($property->propertyFloorplans ?? [], 'property_floorplans/', 'property_floorplans_thumb/');
        PropertyFloorplans::where('property_id', $property->id)->delete();
        PropertyGalleryImages::whereIn('gallery_id', $propertyGalleries)->delete();
        PropertyGalleries::where('property_id', $property->id)->delete();
        $this->deleteS3ImagesFromModel($property->property_images ?? [], 'property_images/', 'property_images_thumb/');
        PropertyImages::where('property_id', $property->id)->delete();

        PropertyMatterport::where('property_id', $property->id)->delete();
        PropertySlider::where('property_id', $property->id)->delete();
        PropertyVideos::where('property_id', $property->id)->delete();
    }

    // Extracted method to delete S3 images from a model with given path(s)
    private function deleteS3ImagesFromModel($model, ...$paths)
    {
        foreach ($model as $data) {
            foreach ($paths as $path) {
                try {
                    $propertyImage = explode($path, $data->{$path == $paths[0] ? 'file_name' : 'thumb'});
                    deleteS3Image($path.'/'.$propertyImage[1]);
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }

    // Action For Store Property Amenities in Property Amenities Table
    public function storeAmenities(Request $request)
    {
        $property = session('property');

        if (! $property) {
            return back()->with('error', 'Error updating property amenities: Property not found.');
        }

        $property = Properties::with('property_amenities')->findOrFail($property->id);

        DB::transaction(function () use ($property, $request) {
            $property->agent_id = $request->agent_id;
            $property->property_amenities()->delete(); // Clear existing amenities

            $amenityIds = explode(',', $request->amenities_array);

            foreach ($amenityIds as $amenityId) {
                if (! is_numeric($amenityId)) {
                    // Create a new amenity if it doesn't exist
                    $newAmenity = Amenities::create([
                        'agent_id' => $property->agent_id,
                        'name' => $amenityId,
                    ]);

                    $amenityId = $newAmenity->id; // Use the newly created ID
                }

                $property->property_amenities()->create([
                    'amenity_id' => $amenityId,
                ]);
            }
        });

        return redirect('/agent/property/price-feature')->with('success', 'Property amenities have been saved.');
    }

    // Action For Store Property Description in Properties Table
    public function storeDescription(Request $request)
    {
        $property = session('property');

        if (! $property) {
            return back()->with('error', 'Error updating property description: Property not found.');
        }

        $property = Properties::findOrFail($property->id);

        $property->update([
            'description' => $request->description,
            'headline' => $request->headline,
            'video_credit' => $request->video_credit,
            'photographer_credit' => $request->photographer_credit,
        ]);

        return redirect('/agent/property/amenities')->with('success', 'Property description has been saved.');
    }

    // Action For Store Property Price And Features in Properties Table
    public function storePriceFeature(Request $request)
    {
        $property = session('property');

        if (! $property) {
            return back()->with('error', 'Error updating property: Property not found.');
        }

        $property = Properties::findOrFail($property->id);

        $request->validate([
            'price' => 'required',
            'property_area' => 'required',
            'bedroom' => 'nullable|numeric',
            'bathroom' => 'nullable|numeric',
            'garage' => 'nullable|numeric',
            'parking_spaces' => 'nullable|numeric',
        ]);

        // Handle image uploads (extracted to separate method)
        $updatedImagePaths = $this->handleImageUploads($request, $property);

        // Update property attributes
        $property->update([
            'bedroom' => $request->bedroom,
            'bathroom' => $request->bathroom,
            'garage' => $request->garage,
            'parking_spaces' => $request->parking_spaces,
            'price' => $request->price,
            'property_area' => $request->property_area,
            'levels' => $request->levels,
            'bedroom_image' => $updatedImagePaths['bedroom_image'] ?? $property->bedroom_image,
            'bathroom_image' => $updatedImagePaths['bathroom_image'] ?? $property->bathroom_image,
            'levels_image' => $updatedImagePaths['levels_img_image'] ?? $property->levels_image,
        ]);

        return redirect('/agent/property-images/images')->with('success', 'Property price and features have been saved.');
    }

    // Extract image handling logic to a separate method
    private function handleImageUploads(Request $request, Properties $property)
    {
        $updatedImagePaths = [];

        $folderPath = public_path('files/property_features/'.$property->id.'/');
        Storage::makeDirectory('files/property_features/'.$property->id, 0755, true); // Ensure directory exists

        foreach (['levels_img', 'bedroom', 'bathroom'] as $imageType) {
            $cropImageKey = 'crop_'.$imageType.'_image';
            $imageNameKey = $imageType.'_img';

            if ($request->has($cropImageKey)) {
                $imageData = $request->input($cropImageKey);

                if (is_string($imageData)) {
                    // Delete old image from S3 if exists
                    if (! empty($property->{$imageType.'_image'})) {
                        $oldImagePath = $property->{$imageType.'_image'};
                        deleteS3Image($oldImagePath);
                    }

                    // Upload new image to S3
                    $imageParts = explode(';base64,', $imageData);
                    $imageBase64 = base64_decode($imageParts[1]);
                    $imageName = time().'_'.$request->input($imageNameKey);
                    $updatedImagePaths[$imageType.'_image'] = uploadS3Base64Image('property_features', $imageName, $imageBase64);
                }
            }
        }

        return $updatedImagePaths;
    }

    // Action For Store Property Address in Property Amenities Table
    public function storeAddress(Request $request, $id = null)
    {
        
        session()->put('agent', auth()->user());
        $agent = session('agent');

        $validator = Validator::make($request->all(), [
            'address_line_1' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('error', 'Street field can not be blank ');
        } else {
            if (is_null($id)) {
                $properties = new Properties;
                $unique_url = Str::slug($request['address_line_1'], '-');

                // Find unique Slug/URL for the property
                $propertie = Properties::where('unique_url', '=', $unique_url)->first();
                if ($propertie) {
                    $unique_url = $propertie->unique_url.'-'.rand(1, 100);
                }
                $properties->unique_url = $unique_url;

                // Tag with the demo session so it is cleaned up when the session ends
                if (! empty(session('demo_session_id'))) {
                    $properties->demo_session_id = session('demo_session_id');
                }

            } else {
                $properties = Properties::where('id', '=', $id)->first();
            }
            // find porperty state name from state table
            if (! is_null($request['state_name'])) {
                $property_state = States::where('name', '=', $request['state_name'])->first();
                $state_id = $property_state->state_id;
            } else {
                $state_id = null;
            }

            $properties->agent_id = $agent->id;
            $properties->name = $request['address_line_1'];
            $properties->address_line_1 = $request['address_line_1'];
            $properties->address_line_2 = $request['address_line_2'];
            $properties->city = $request['city'];
            $properties->state_id = $state_id;
            $properties->zip = $request['zip'];
            $properties->country_id = '230';
            $properties->matterport_data = $request['address_line_1'];
            if ($properties->save()) {
                $request->session()->put('property', $properties);

                return redirect('/agent/property/description')->with('success', 'Property Address Has been Saved');
            } else {
                return back()->with('error', 'Property is not saved');
            }

        }
    }
}
