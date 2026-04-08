<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\PropertyImages;
use App\Models\PropertyGalleries;
use App\Models\PropertyGalleryImages;
use Illuminate\Http\Request;

class PropertyGalleriesController extends Controller
{
    public function galleryImages()
    {
        $property = session('property');

        if (! is_null($property)) {
            $property_images = PropertyImages::where('property_id', '=', $property->id)->get();
            // here join the property gallery Table with Property Gallery Images table
            $property_gallery = PropertyGalleries::where('property_id', '=', $property->id)->with('Property_gallery_images')->get();

            // here in the property_gallery_details array we store the property gallery name and description and
            // store property_images_id , galelry_id from praoperty gallery Images table

            $property_gallery_details = [];
            foreach ($property_gallery as $pg) {

                // here we store property galary images data with key "images" in array
                $property_gallery_details[$pg->id]['images'] = [];

                foreach ($pg->property_gallery_images as $pgi) {
                    if (! is_null($pgi->property_images)) {
                        $tmp = [];
                        $tmp['property_image_id'] = $pgi->property_image_id;
                        $tmp['file_name'] = $pgi->property_images['file_name'];
                        $tmp['thumb_name'] = $pgi->property_images['thumb'];
                        if (count($tmp) != 0) {
                            $property_gallery_details[$pgi->gallery_id]['images'][] = $tmp;
                        }
                    }
                }

                $property_gallery_details[$pg->id]['gallery_name'] = $pg->name;
                $property_gallery_details[$pg->id]['property_id'] = $pg->property_id;
                $property_gallery_details[$pg->id]['short_description'] = $pg->short_description;
            }

            return view('agent/galleries/gallery_images', compact('property_images', 'property_gallery_details', 'property_gallery', 'property'));
        } else {
            return redirect('agent/property/listing')->with('error', 'You can not access Property Galleries directly !');
        }
    }

    public function propertyGalleries(Request $request)
    {
        $property = session('property');
        if (! is_null($property)) {
            if ($request['id'] != 0) {
                $property_gallery = PropertyGalleries::where('id', '=', $request['id'])->first();
            } else {
                $property_gallery = new PropertyGalleries;
            }

            $property_gallery->property_id = $property->id;
            $property_gallery->name = $request['gallery_name'];
            $property_gallery->short_description = $request['short_description'];

            if ($property_gallery->save()) {

                $property_gallery_images = PropertyGalleryImages::where('gallery_id', '=', $request['id']);
                if (! is_null($property_gallery_images)) {
                    $property_gallery_images->delete();
                }
                $seq = 1;
                foreach ($request['Drop_img_id'] as $drop) {
                    $property_gallery_image = new PropertyGalleryImages;
                    $property_gallery_image->gallery_id = $property_gallery->id;
                    $property_gallery_image->property_image_id = $drop;
                    $property_gallery_image->featured_image = 0;
                    $property_gallery_image->sequence = $seq++;
                    $property_gallery_image->save();
                }
                $result['gallery_id'] = $property_gallery->id;
                $result['response'] = '1';
            } else {
                $result['gallery_id'] = 0;
                $result['response'] = '0';
            }

            return response()->json($result);
        } else {
            return back()->with('error', 'Error on Updating Property Gallery !');
        }
    }

    public function deletePropertyGalleries(Request $request)
    {
        if ($request['gallery_length'] > 1) {
            $property_gallery_images = PropertyGalleryImages::where('gallery_id', '=', $request['gallery_id'])->get();
            $property_gallery = PropertyGalleries::where('id', '=', $request['gallery_id'])->first();
            if (! is_null($property_gallery_images) && ! is_null($property_gallery)) {
                foreach ($property_gallery_images as $property_gallery_image) {
                    $property_gallery_image->delete();
                }
                if ($property_gallery->delete()) {
                    $result['response'] = '1';
                    $result['gallery_id'] = $request['gallery_id'];
                    $result['messege'] = 'Successfully deleted';
                } else {
                    $result['response'] = '0';
                    $result['gallery_id'] = '0';
                    $result['messege'] = 'Error on Deleting';
                }

            } else {
                $result['response'] = '1';
                $result['gallery_id'] = '0';
                $result['messege'] = 'Successfully deleted';
            }

            return response()->json($result);
        } else {
            $result['response'] = '0';
            $result['gallery_id'] = '0';
            $result['messege'] = "You can't Delete your last gallery";
        }

        return response()->json($result);
    }

    public function DefaultGalleryImages()
    {
        $property = session('property');
        if (! is_null($property)) {
            $property_images = PropertyImages::where('property_id', '=', $property->id)->get();

            // here join the property gallery Table with Property Gallery Images table
            $property_gallerys = PropertyGalleries::where('property_id', '=', $property->id)->with('Property_gallery_images')->get();

            // here in the property_gallery_details array we store the property gallery name and description and
            // store property_images_id , galelry_id from praoperty gallery Images table

            $property_gallery_details = [];
            foreach ($property_gallerys as $pg) {

                // here we store property galary images data with key "images" in array
                $property_gallery_details[$pg->id]['images'] = [];

                foreach ($pg->property_gallery_images as $pgi) {
                    if (! is_null($pgi->property_images)) {
                        $tmp = [];
                        $tmp['property_image_id'] = $pgi->property_image_id;
                        $tmp['property_images_gallery_id'] = $pgi->id;
                        $tmp['featured_images'] = $pgi->featured_image;
                        $tmp['file_name'] = $pgi->property_images['file_name'];
                        $tmp['thumb_name'] = $pgi->property_images['thumb'];
                        if (count($tmp) != 0) {
                            $property_gallery_details[$pgi->gallery_id]['images'][] = $tmp;
                        }
                    }
                }
                $property_gallery_details[$pg->id]['gallery_id'] = $pg->id;
                $property_gallery_details[$pg->id]['gallery_name'] = $pg->name;
                $property_gallery_details[$pg->id]['property_id'] = $pg->property_id;
            }

            return view('agent/galleries/default-gallery-images', compact('property_gallerys', 'property_gallery_details'));

        } else {
            return redirect('agent/property/listing')->with('error', 'You can not access Default Images directly !');
        }

    }

    // Add function for save multiple gallerys image as a feature

    public function SaveFeaturdGalleryImages(Request $request)
    {

        $property_gallery_images = PropertyGalleryImages::where('gallery_id', '=', $request['gallery_id'])->get();

        foreach ($property_gallery_images as $property_gallery_image) {
            $property_gallery_image->featured_image = '0';
            $property_gallery_image->save();
        }

        $property_gallery_image = PropertyGalleryImages::where('id', '=', $request['gallery_image_id'])->update(['featured_image' => '1']);

        if ($property_gallery_image) {
            $data['success'] = 1;
            $data['messege'] = 'Successfully Updated Gallery Featured Image ';
        } else {
            $data['success'] = 0;
            $data['messege'] = 'Error on updation Gallery Featured Image ';
        }

        return response()->json($data);
    }
}
