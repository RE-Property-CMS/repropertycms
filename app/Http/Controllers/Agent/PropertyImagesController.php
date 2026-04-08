<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\Property_images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class PropertyImagesController extends Controller
{
    public function images()
    {
        $property = session('property');
        if (! is_null($property)) {
            $properties = Properties::find($property->id);

            return view('agent/property-images/images', [
                'property' => $properties,
            ]);
        } else {
            return redirect('agent/property/listing')->with('error', 'You can not access Property Images directly !');
        }
    }

    public function storeImage(Request $request)
    {
        $data = [];
        $validator = Validator::make($request->all(), [
            'file'        => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'property_id' => 'required',
        ]);
        $property_id = $request['property_id'];
        if ($validator->fails()) {
            $data['success'] = 0;
            $data['error'] = $validator->errors()->first('file'); // Error response
        } else {
            // Deep MIME type verification
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $request->file->getRealPath());
            finfo_close($finfo);

            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                return response()->json(['success' => 0, 'error' => 'Invalid file type detected. Only image files are allowed.']);
            }
            // Upload image on S3

            $path = uploadS3Image('property_images', $request->file);
            $thumb_path = uploadS3ImageThumb('property_images_thumb', $request->file, env('THUMB_WIDTH'));

            /* Store $imageName name in DATABASE from HERE */
            $property_image = new Property_images;
            $property_image->property_id = $property_id;
            $property_image->file_name = $path;
            $property_image->thumb = $thumb_path;

            $size = $_FILES['file']['size'];
            if ($size >= 1024 && $size < (1024 * 1024)) {
                $size = round($size / 1024, 2).'kb';
            } elseif ($size >= (1024 * 1024)) {
                $size = round($size / (1024 * 1024), 2).'mb';
            } else {
                $size = round($size, 2).'byte';
            }

            if ($property_image->save()) {
                $data['success'] = 1;
                $data['message'] = 'Uploaded Successfully!';
                $data['size'] = $size;
                $data['image_id'] = $property_image->id;
                $data['property_id'] = $property_image->property_id;
                $data['file_name'] = $property_image->file_name;
                $data['thumb_name'] = $property_image->thumb;
                $data['message'] = 'Uploaded Successfully!';
            } else {
                // Response
                $data['success'] = 0;
                $data['error'] = 'File not uploaded.';
            }

            /* $file = $request->file('file');
            $filename = time() . '_' . str_replace(" ", "_", $file->getClientOriginalName());

            // sepreate image name and extension
            $remove_extension = explode('.',$filename);

            // get image name and add extension JPG
            $image_name = $remove_extension[0].'.jpg';

            // File upload location
            $path = public_path() . '/files/property_images/' . $property_id;

            // create image with gd extension
            $imgFile = Image::make($file->path());

            $size = $_FILES["file"]["size"];
            if ($size >= 1024 && $size < (1024 * 1024)) {
                $size = round($size / 1024, 2) . 'kb';
            } elseif ($size >= (1024 * 1024)) {
                $size = round($size / (1024 * 1024), 2) . 'mb';
            } else {
                $size = round($size, 2) . 'byte';
            }

            // Upload file
            if (!File::exists($path)) {
                File::makeDirectory($path);
            }
            if ($file->move($path, $image_name)) {
                // Resize image resolution and save on given path
                $imgFile->resize(1080, 1920, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path.'/'.$image_name);
                $property_image = new Property_images;
                $property_image->property_id = $property_id;
                $property_image->file_name = $image_name;

                if($property_image->save()){
                    $property_image = Property_images::where('file_name', '=', $image_name)->first();
                    $data['success'] = 1;
                    $data['message'] = 'Uploaded Successfully!';
                    $data['size'] = $size;
                    $data['image_id'] = $property_image->id;
                    $data['property_id'] = $property_image->property_id;
                    $data['file_name'] = $property_image->file_name;
                    $data['message'] = 'Uploaded Successfully!';
                } else {
                    // Response
                    $data['success'] = 0;
                    $data['error'] = 'File not uploaded.';
                }
            }else{
                $data['success'] = 0;
                $data['message'] = 'File not uploaded.';
            }  */
        }

        return response()->json($data);
    }

    //Rotate Image

    public function rotateImage($id, Request $request)
    {
        if (! is_null($id)) {
            $property_image = Property_images::find($id);
            $image_file = $property_image->file_name;
            $image_path = public_path().'/files/property_images/'.$property_image->property_id.'/';
            $degrees = $request->degrees;
            $degrees = $degrees * -1;

            if (str_starts_with($property_image->file_name, 'http')) {
                /* $assetPath = Storage::disk('s3')->url('property_images/isem9uVyntrKHoLsFUE4P3FneeTp3tmX7e3GL0ys.jpg');
                $extension = pathinfo($property_image->file_name, PATHINFO_EXTENSION);
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=" . basename($assetPath));
                header("Content-Type: " . $extension);

                $image = readfile($assetPath); */
                $image_path = public_path().'/files/property_images/';

                $ext = strtolower(pathinfo($property_image->file_name, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    return response()->json(['error' => 'Unsupported image format.'], 422);
                }
                $url = $property_image->file_name;
                $image_file = 'new_rotate.'.$ext;
                $image = public_path().'/files/property_images/new_rotate.'.$ext;
                file_put_contents($image, file_get_contents($url));

                $source = '';
                if ($ext == 'png') {
                    $source = imagecreatefrompng($image);
                } elseif ($ext == 'jpg' || $ext == 'jpeg') {
                    $source = imagecreatefromjpeg($image);
                } elseif ($ext == 'gif') {
                    $source = imagecreatefromgif($image);
                }

                // Rotate
                $rotate = imagerotate($source, $degrees, 0);
                // Output
                if ($ext == 'png' || $ext == 'PNG') {
                    if (imagepng($rotate, $image_path.$image_file, 0)) {
                    }
                } elseif ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG') {
                    imagejpeg($rotate, $image_path.$image_file, 90);
                } elseif ($ext == 'gif' || $ext == 'GIF') {
                    imagegif($rotate, $image_path.$image_file);
                }

                //Delete image from S3
                deleteS3Image($property_image->file_name);
                $imageName = time().'_'.$image_file;
                $path = uploadS3Base64Image('property_images', $imageName, file_get_contents($image_path.$image_file));
                Property_images::where('id', '=', $property_image->id)->update(['file_name' => $path]);

                imagedestroy($source);
                imagedestroy($rotate);
                $data = [];
                $data['file_name'] = $path;
                $data['path'] = $path;
                $data['property_id'] = $property_image->property_id;
                unlink($image_path.$image_file);
            } else {
                // Load
                $ext = strtolower(substr(basename($image_file), strrpos(basename($image_file), '.') + 1));
                $image = $image_path.$image_file;
                $source = '';
                if ($ext == 'png') {
                    $source = imagecreatefrompng($image);
                } elseif ($ext == 'jpg' || $ext == 'jpeg') {
                    $source = imagecreatefromjpeg($image);
                } elseif ($ext == 'gif') {
                    $source = imagecreatefromgif($image);
                }
                // Rotate
                $rotate = imagerotate($source, $degrees, 0);
                // Output
                if ($ext == 'png' || $ext == 'PNG') {
                    if (imagepng($rotate, $image_path.$image_file, 0)) {
                        echo $degrees;
                    }
                } elseif ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG') {
                    imagejpeg($rotate, $image_path.$image_file, 90);
                } elseif ($ext == 'gif' || $ext == 'GIF') {
                    imagegif($rotate, $image_path.$image_file);
                }
                //     // Free the memory
                imagedestroy($source);
                imagedestroy($rotate);
                $data = [];
                $data['file_name'] = $image_file;
                $data['path'] = $image_path;
                $data['property_id'] = $property_image->property_id;
            }

            return response()->json($data);
        } else {
            return back()->with('error', 'Error on Rotating Image !');
        }
    }
}
