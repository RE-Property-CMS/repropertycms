<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\PropertyVideos;
use App\Models\PropertySlider;
use Illuminate\Http\Request;

class TopbarsController extends Controller
{
    public function image(Request $request)
    {
        $property = session('property');
        if (! $property || ! $property->id) {
            return $property
                ? view('agent.Property.address')
                : redirect('agent/property/listing')->with('error', 'You cannot access Topbar Image directly!');
        }

        $property = Properties::findOrFail($property->id);

        return view('agent.topbar.image', ['property' => $property]);
    }

    // Feature Video
    public function video()
    {
        $property = session('property');
        if (! $property || ! $property->id) {
            return $property
                ? view('agent.Property.address')
                : redirect('agent/property/listing')->with('error', 'You cannot access Topbar Video directly!');
        }

        $property = Properties::findOrFail($property->id);

        return view('agent.topbar.video', ['property' => $property]);
    }

    public function featureImage(Request $request)
    {
        $property = session('property');
        if (! is_null($property)) {
            $data = [];

            $status = Properties::where('id', '=', $property->id)->update(['featured_image' => $request['image_id']]);
            if ($status) {
                $data['success'] = 1;
                $data['message'] = 'Feature Image Successfully Saved !';
            } else {
                $data['success'] = 0;
                $data['error'] = 'Feature Image Failed ! !';
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Updating Feature Image !');
        }

    }

    public function featureSlider(Request $request)
    {
        $property = session('property');
        if (! is_null($property)) {
            $property_slider = PropertySlider::where('property_id', '=', $property->id)->get();

            if (! is_null($property_slider)) {
                foreach ($property_slider as $property_slide) {
                    $property_slide->delete();
                }
            }

            foreach ($request['Drop_img_id'] as $drop) {
                $property_slider = new PropertySlider;
                $property_slider->property_id = $property->id;
                $property_slider->image_id = $drop;
                $status = $property_slider->save();
            }

            if ($status) {
                $data['success'] = 1;
            } else {
                $data['success'] = 0;
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Updating Featured Slider !');
        }

    }

    public function selectionForTop(Request $request)
    {
        $property = session('property');
        if (! is_null($property)) {
            if ($request['type'] == 'image') {
                $propertys = Properties::find($property->id);
                if ($propertys->featured_image != 0) {
                    $propertie = $propertys->update(['main_section' => 'Image']);
                    if ($propertie) {
                        $data['success'] = 1;
                        $data['messege'] = 'Successfully Updated Image in Topbar ';
                        $data['topbar'] = $propertie->main_section;
                    } else {
                        $data['success'] = 0;
                        $data['messege'] = 'Error on Updating Image in Topbar';
                    }
                } else {
                    $data['success'] = 0;
                    $data['messege'] = 'Please select at least 1 Image as a default';
                }

            } elseif ($request['type'] == 'slider') {

                $propertyslider = PropertySlider::where('property_id', '=', $property->id)->count();
                if ($propertyslider > 0) {
                    $propertie = Properties::find($property->id)->update(['main_section' => 'Slider']);
                    if ($propertie) {
                        $data['success'] = 1;
                        $data['messege'] = 'Successfully Updated Slider in Topbar ';
                        $data['topbar'] = $propertie->main_section;
                    } else {
                        $data['success'] = 0;
                        $data['messege'] = 'Error on Updating Slider in Topbar';
                    }
                } else {
                    $data['success'] = 0;
                    $data['messege'] = 'Please select at least 1 Image in the slider';
                }

            } elseif ($request['type'] == 'video') {
                // Get the all videos of the property
                $propertyvideos = PropertyVideos::where('property_id', '=', $property->id)->get();

                if ($propertyvideos->count() > 0) {

                    // get if any property videos feature column equal to 1
                    $propertyvideo = $propertyvideos->where('featured', '=', 1);

                    // check here property video count greater then 0
                    if ($propertyvideo->count() > 0) {
                        $propertie = Properties::find($property->id)->update(['main_section' => 'Video']);
                        if ($propertie) {
                            $data['success'] = 1;
                            $data['messege'] = 'Successfully Updated Video in Topbar ';
                            $data['topbar'] = $propertie->main_section;
                        } else {
                            $data['success'] = 0;
                            $data['messege'] = 'Error on Updating Video in Topbar';
                        }
                    } else {
                        $data['success'] = 0;
                        $data['messege'] = 'Please select at least 1 video as featured . ';
                    }
                } else {
                    $data['success'] = 0;
                    $data['messege'] = "You don't have video, Please upload at least 1 video. ";
                }

            } else {
                $data['success'] = 0;
                $data['messege'] = 'Error on Updating Topbar';
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Selection for Topbar !');
        }
    }
}
