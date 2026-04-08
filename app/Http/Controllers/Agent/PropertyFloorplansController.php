<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\PropertyImages;
use App\Models\PropertyFloorplanImages;
use App\Models\PropertyFloorplans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PropertyFloorplansController extends Controller
{
    public function property_floorplan()
    {
        $property = session('property');
        if (! $property || ! $property->id) {
            return redirect('agent/property/listing')->with('error', 'You cannot access Documents directly!');
        }

        $property = Properties::findOrFail($property->id);

        return view('agent/property-floorplan/property-floorplans', compact('property'));
    }

    public function saveFloorplans(Request $request)
    {
        $property = session('property');
        if (! is_null($property)) {
            $data = [];
            $property_id = $property['id'];
            if (isset($request['name'])) {
                $porperty_floorplan = PropertyFloorplans::where('id', '=', $request['floorplanId'])->first();
                $porperty_floorplan->name = $request['name'];
                if ($porperty_floorplan->save()) {
                    // Response
                    $data['success'] = 1;
                    $data['floorplanId'] = $request['floorplanId'];
                    $data['name'] = $request['name'];
                    $data['message'] = 'File Name Updated Successfully!';
                } else {
                    // Response
                    $data['success'] = 0;
                    $data['message'] = 'File Name not Updated.';
                }
            } else {
                if ($property_id == $request->property_id) {
                    // Validate file upload
                    $request->validate([
                        'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
                    ]);

                    // Deep MIME type verification
                    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $request->file->getRealPath());
                    finfo_close($finfo);

                    if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                        return response()->json(['success' => 0, 'message' => 'Invalid file type detected. Only image files are allowed.']);
                    }

                    // Upload image on S3
                    //                    $path = uploadS3Image('property_floorplans', $request->file);
                    $path = uploadS3ImageThumb('property_floorplans', $request->file, env('FLOORPLAN_WIDTH'));
                    $thumb_path = uploadS3ImageThumb('property_floorplans_thumb', $request->file, env('THUMB_WIDTH'));
                    $porperty_floorplans = PropertyFloorplans::where('property_id', '=', $property['id'])->max('sequence');

                    if ($porperty_floorplans) {
                        $seq = $porperty_floorplans + 1;
                    } else {
                        $seq = 1;
                    }

                    $property_floorplan = new PropertyFloorplans;
                    $property_floorplan->property_id = $property_id;
                    $property_floorplan->name = 'Untitled';
                    $property_floorplan->file_name = $path;
                    $property_floorplan->thumb = $thumb_path;
                    $property_floorplan->sequence = $seq;
                    if ($property_floorplan->save()) {
                        // Response
                        $data['success'] = 1;
                        $data['id'] = $property_floorplan->id;
                        $data['property_id'] = $property_floorplan->property_id;
                        $data['filename'] = asset_s3($thumb_path);
                        $data['hotspotname'] = $property_floorplan->name;
                        $data['message'] = 'Uploaded Successfully!';
                    } else {
                        // Response
                        $data['success'] = 0;
                        $data['message'] = 'File not uploaded.';
                    }
                } else {
                    $data['success'] = 0;
                    $data['error'] = $property->errors()->first('file'); // Error response
                }
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Updating Floorplan !');
        }

    }

    // delete property floor plan
    public function deleteFloorplan($id)
    {
        if (! is_null($id)) {
            $data = [];
            $property_floorplans = PropertyFloorplans::where('id', '=', $id)->with('property_floorplan_images')->first();
            if ($property_floorplans->property_floorplan_images->count() <= 0) {
                if ($property_floorplans->count() > 0) {
                    $property_id = $property_floorplans->property_id;

                }
                deleteS3Image($property_floorplans->file_name);

                $property_floorplans->delete();
                $data['success'] = 1;
                $data['message'] = 'Floor Plan is Deleted !';
            } else {
                $data['success'] = 0;
                $data['error'] = 'You can not delete Floorplan because you have floorplan hostspot !';
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Deleting Floorplan !');
        }
    }

    public function AddHotspot($floorplan_id)
    {
        $property = session('property');
        if (! is_null($property)) {
            $property_floorplans = PropertyFloorplans::where('id', '=', $floorplan_id)->first();

            $property_floorplan_images = PropertyFloorplanImages::where('property_floorplan_id', '=', $floorplan_id)->with('property_images')->get();

            //Get all images for the property - to exclude from the list
            $property_used_images = PropertyFloorplanImages::where('property_id', '=', $property['id'])->get();

            $data = [];
            foreach ($property_used_images as $object) {
                $data[] = $object->property_image_id;
            }

            $property_images = PropertyImages::where('property_id', '=', $property['id'])->whereNotIn('id', $data)->get();

            return view('agent/property-floorplan/Hotspot', compact('property_floorplans', 'property', 'property_floorplan_images', 'property_images'));

        } else {
            return redirect('agent/property/listing')->with('error', 'You can not access Floorplan Hotspot directly !');
        }
    }

    public function addHotspotNew($floorplan_id)
    {
        $property = session('property');
        if (! is_null($property)) {

            $property_floorplans = PropertyFloorplans::where('id', '=', $floorplan_id)->first();

            $property_floorplan_images = PropertyFloorplanImages::where('property_floorplan_id', '=', $floorplan_id)->with('property_images')->get();

            //Get all images for the property - to exclude from the list
            $property_used_images = PropertyFloorplanImages::where('property_id', '=', $property['id'])->get();

            $data = [];
            foreach ($property_used_images as $object) {
                $data[] = $object->property_image_id;
            }

            $property_images = PropertyImages::where('property_id', '=', $property['id'])->whereNotIn('id', $data)->get();

            return view('agent/property-floorplan/hotspot-new', compact('property_floorplans', 'property', 'property_floorplan_images', 'property_images'));
        } else {
            return redirect('agent/property/listing')->with('error', 'You can not access Property Floorplans directly !');
        }
    }

    public function addHotspotArea($floorplan_id, Request $request)
    {
        try {
            $property_floorplan = PropertyFloorplans::find($floorplan_id);
            $hotspot = $property_floorplan->hotspots()->create([
                'top' => $request->top,
                'left' => $request->left,
                'height' => $request->height,
                'width' => $request->width,
            ]);

            return [
                'AREA' => $hotspot,
            ];
        } catch (\Exception $ex) {
            return [
                'ERROR' => $ex->getMessage(),
            ];
        }
    }

    public function removeHotspotArea($floorplan_id, Request $request)
    {

        try {
            $property_floorplan = PropertyFloorplans::find($floorplan_id);
            $hotspot = $property_floorplan->hotspots()->find($request->id);
            if ($hotspot) {
                $hotspot->delete();
            }

            return [
                'SUCCESS' => 1,
            ];
        } catch (\Exception $ex) {
            return [
                'ERROR' => $ex->getMessage(),
            ];
        }
    }

    public function updateHotspotImage($floorplan_id, Request $request)
    {
        if ($request->action == 'add') {
            $hotspot = PropertyFloorplans::find($floorplan_id)->hotspots()->find($request->id);
            $hotspot->propertyImages()->attach($request->assetid);

        } elseif ($request->action == 'remove') {

            $hotspot = PropertyFloorplans::find($floorplan_id)->hotspots()->find($request->id);
            $hotspot->propertyImages()->detach($request->assetid);
        }

        return [
            'SUCCESS' => 1,
        ];

    }

    public function getPropertyImage($id)
    {
        $property_image = PropertyImages::find($id);

        if ($property_image) {
            try {
                if (! $property_image->small) {
                    $small_path = download_s3_image_resize_store('property_images_small', $property_image->thumb, 50);
                    $property_image->update(['small' => $small_path]);
                }

                $fileContents = Storage::disk('s3')->get($property_image->small);

                return response($fileContents, 200)
                    ->header('Content-Type', 'image/jpeg')
                    ->header('Cache-Control', 'public, max-age=31536000')
                    ->header('Expires', now()->addYear()->toRfc2822String());
            } catch (\Exception $e) {
                abort(404, 'Image not found or could not be loaded.'.$e->getMessage()); // Provide more context in error message
            }
        } else {
            abort(404);
        }
    }

    public function saveAddHotspot(Request $request)
    {
        $property = session('property');
        if (! is_null($property)) {
            $data = [];
            if ($request['id']) {
                $property_floorplan_images = new PropertyFloorplanImages;
                $property_floorplan_images->property_id = $property['id'];
                $property_floorplan_images->property_floorplan_id = $request['id'];
                $property_floorplan_images->property_image_id = $request['property_image_id'];
                $property_floorplan_images->coordinates = $request['coordinateX'].','.$request['coordinateY'];
                $property_floorplan_images->floorplan_image_ratio = $request['image_height'].','.$request['image_width'];
                if ($property_floorplan_images->save()) {

                    $property_images = PropertyImages::where('id', '=', $property_floorplan_images['property_image_id'])->first();
                    $data['success'] = 1;
                    $data['id'] = $request['property_image_id'];
                    $data['property_id'] = $property->id;
                    $data['floorplan_id'] = $property_floorplan_images->property_floorplan_id;
                    $data['file_name'] = $property_images['file_name'];
                    $data['message'] = 'Uploaded Successfully!';

                } else {
                    // Response
                    $data['success'] = 0;
                    $data['error'] = 'File not uploaded.';
                }

                return response()->json($data);
            }
        } else {
            return back()->with('error', 'Error on Updating Floorplan Hotspot !');
        }

    }

    // delete property Floorplan hotspot
    public function deletehotspot(Request $request)
    {
        if (! is_null($request['id'])) {
            $data = [];
            $property_floorplan_images = PropertyFloorplanImages::where('property_image_id', '=', $request['id'])->first();
            if ($property_floorplan_images->delete()) {
                $data['success'] = 1;
                $data['message'] = 'Hotspot is Deleted !';
            } else {
                $data['success'] = 0;
                $data['error'] = 'Erorr deleting Hotspot. Please try again.';
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Deleting Floorplan Hotspot !');
        }
    }
}
