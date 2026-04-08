<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\PropertyMatterport;
use App\Models\PropertyVideos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function video()
    {
        $property = session('property');
        if (! $property || ! $property->id) {
            return $property
                ? view('agent.Property.address')
                : redirect('agent/property/listing')->with('error', 'You cannot access Video directly!');
        }

        $property = Properties::findOrFail($property->id);

        return view('agent/video/video', ['property' => $property]);
    }

    // ThreeD_video = 3d_videos
    public function ThreeD_video()
    {
        $property = session('property');
        if (! is_null($property)) {
            $property_matterports = PropertyMatterport::where('property_id', '=', $property['id'])->get();
            $data = compact('property_matterports');

            return view('agent/video/3d-video')->with($data);
        } else {
            return redirect('agent/property/listing')->with('error', 'You can not access 3D Matterport Videos directly !');
        }
    }

    // Save Video

    public function saveVideo(Request $request)
    {
        $data = [];
        $property = session('property');
        if (! is_null($property)) {

            $property_videos = PropertyVideos::where('property_id', '=', $property['id'])->count();

            if ($property_videos >= 2) {
                $data['success'] = 0;
                $data['message'] = 'You have Exceed your Video Uplaod Limit';
            } else {

                $validator = Validator::make($request->all(), [
                    'file' => 'required|file|mimes:mp4,mov,avi,wmv,flv,webm|max:102400',
                ]);
                if ($validator->fails()) {
                    $data['success'] = 0;
                    $data['error'] = $validator->errors()->first('file'); // Error response
                } else {
                    $video = $request->file('file');

                    // Deep MIME type verification
                    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $video->getRealPath());
                    finfo_close($finfo);

                    if (!in_array($mimeType, ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/x-flv', 'video/webm'])) {
                        return response()->json(['success' => 0, 'error' => 'Invalid file type detected. Only video files are allowed.']);
                    }

                    // Sanitize filename
                    $safeName   = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME));
                    $video_name = time() . '_' . $safeName . '.' . $video->getClientOriginalExtension();

                    // File upload location
                    $path = public_path().'/files/property_videos/'.$property['id'];
                    $size = $_FILES['file']['size'];
                    if ($size >= 1024 && $size < (1024 * 1024)) {
                        $size = round($size / 1024, 2).'kb';
                    } elseif ($size >= (1024 * 1024)) {
                        $size = round($size / (1024 * 1024), 2).'mb';
                    } else {
                        $size = round($size, 2).'byte';
                    }

                    // Upload Videos
                    if (! File::isDirectory($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }
                    if ($video->move($path, $video_name)) {
                        $property_video = new PropertyVideos;
                        $property_video->property_id = $property['id'];
                        $property_video->file_name = $video_name;
                        $status = $property_video->save();
                        // Response
                        if ($status) {
                            $data['success'] = 1;
                            $request->session()->flash('success', 'Uploaded Successfully!');
                        } else {
                            // Response
                            $data['success'] = 0;
                            $data['message'] = 'Error saving data.';
                        }
                    } else {
                        $data['success'] = 0;
                        $data['message'] = 'Video not uploaded.';
                    }
                }
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Updating Video !');
        }

    }

    // Cover Video

    public function coverVideo(Request $request)
    {
        $property = session('property');
        if (! is_null($property)) {
            $data = [];
            $video_id = $request['video_id'];

            PropertyVideos::where('property_id', '=', $property['id'])->update(['main_video' => 0]);

            $status = PropertyVideos::where('id', '=', $video_id)->update(['main_video' => 1]);
            if ($status) {
                $data['success'] = 1;
                $data['message'] = 'Cover Video Successfully Saved !';
            } else {
                $data['success'] = 0;
                $data['error'] = 'Cover Video Failed !';
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Updating Cover Video !');
        }
    }

    // Feature Video

    public function featureVideo(Request $request)
    {
        $property = session('property');
        if (! is_null($property)) {
            $data = [];

            $videos = PropertyVideos::where('property_id', '=', $property->id)->get();
            foreach ($videos as $video) {
                $video->featured = 0;
                $video->save();
            }

            $status = PropertyVideos::where('id', '=', $request['video_id'])->update(['featured' => 1]);
            if ($status) {
                $data['success'] = 1;
                $data['message'] = 'Feature Video Successfully Saved !';
            } else {
                $data['success'] = 0;
                $data['error'] = 'Feature Video Failed ! !';
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Updating Featured Video !');
        }
    }

    // Delete Property video

    public function deleteVideo($id)
    {
        if (! is_null($id)) {
            $data = [];
            $property_video = PropertyVideos::find($id);
            $property_id = $property_video->property_id;
            $path = public_path().'/files/property_videos/'.$property_id.'/';
            if (is_null($property_video->video_url)) {
                if (file_exists($path)) {
                    unlink($path.$property_video->file_name);
                }
                if ($property_video->delete()) {
                    $data['success'] = 1;
                    $data['message'] = 'Video is Deleted !';
                } else {
                    $data['success'] = 0;
                    $data['error'] = 'Video is Not Deleted !';
                }
            } elseif ($property_video->delete()) {
                $data['success'] = 1;
                $data['message'] = 'Video is Deleted !';
            } else {
                $data['success'] = 0;
                $data['error'] = 'Video is Not Deleted !';
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Deleting Video !');
        }
    }

    // Save save3dmatterport video url

    public function saveMatterportUrl(Request $request)
    {
        $property = session('property');
        if (! is_null($property)) {
            $data = [];
            $validator = Validator::make($request->all(), [
                'matterport_url' => 'required',
            ]);
            if ($validator->fails()) {
                $data['success'] = 0;
                $data['error'] = $validator->errors()->first('matterport_url'); // Error response
            } else {
                $matterport_url = $request['matterport_url'];
                $property_matterport = new PropertyMatterport;
                $property_matterport->property_id = $property['id'];
                $property_matterport->matterport_url = $matterport_url;

                if ($property_matterport->save()) {
                    $data['success'] = 1;
                    $data['matterport_id'] = $property_matterport->id;
                    $data['matterport_url'] = $matterport_url;
                    $data['message'] = 'Uploaded Successfully!';
                } else {
                    // Response
                    $data['success'] = 0;
                    $data['error'] = 'Uploaded Failed.!';
                }
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Updating 3D Matterport Video Detailes !');
        }
    }

    // Delete save3dmatterport video url

    public function deleteMatterportUrl($id)
    {
        if (! is_null($id)) {
            $data = [];
            $status = PropertyMatterport::find($id)->delete();
            if ($status) {
                $data['success'] = 1;
                $data['message'] = 'Url is Deleted !';
            } else {
                $data['success'] = 0;
                $data['error'] = 'Url is Not Deleted !';
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Deleting  3D Matterport Video !');
        }

    }

    // save url video

    public function Save_Url_Video(Request $request)
    {

        $property = session('property');
        if (! is_null($property)) {
            $property_videos = PropertyVideos::where('property_id', '=', $property['id'])->count();

            if ($property_videos >= 2) {
                return redirect()->back()->with('error', 'You have Exceed your Video Uplaod Limit');
            } else {
                $request->validate([
                    'video_url' => 'required',
                ]);

                $property_video = new PropertyVideos;
                $property_video->property_id = $property['id'];
                $property_video->video_type = $request['video_source'];
                $property_video->video_url = $request['video_url'];
                $status = $property_video->save();
                if ($status) {
                    return redirect('/agent/video/video')->with('success', 'Video Url Has been Saved');
                } else {
                    return back()->with('error', 'Video Url not saved');
                }
            }
        } else {
            return back()->with('error', 'Error on Updating Video Detailes !');
        }
    }
}
