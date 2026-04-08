<?php

namespace App\Livewire\PhotoLibrary;

use App\Models\Properties;
use App\Models\PropertyImages;
use Illuminate\Http\File;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddNewImage extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public bool $show = false;

    public $property;

    public $thumbnail;

    #[On('open-photo-add')]
    public function openModal(int $propertyId): void
    {
        $this->property = Properties::find($propertyId);
        $this->show = true;
    }

    public function closeModal(): void
    {
        $this->show = false;
        $this->reset('thumbnail');
    }

    public function save()
    {
        // Validate files server-side
        $this->validate([
            'thumbnail.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        if (is_array($this->thumbnail)) {
            foreach ($this->thumbnail as $file) {
                // Additional MIME type verification
                if (!$this->validateMimeType($file, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                    $this->alert('error', 'Invalid file type detected. Only image files are allowed.');
                    return;
                }

                $image = new File($file['path']);
                $path = uploadS3Image('property_images', $image);
				$thumb_path = uploadS3ImageThumb('property_images_thumb', $image, env('THUMB_WIDTH'));

                $property_image = new PropertyImages;
                $property_image->property_id = $this->property->id;
                $property_image->file_name = $path;
                $property_image->thumb = $thumb_path;
                $property_image->save();
            }
        } else {
            if(isset($this->thumbnail['path'])) {
                $image = new File($this->thumbnail['path']);
                $path = uploadS3Image('property_images', $image);
                $thumb_path = uploadS3ImageThumb('property_images_thumb', $image, env('THUMB_WIDTH'));
                $property_image = new PropertyImages;
                $property_image->property_id = $this->property->id;
                $property_image->file_name = $path;
                $property_image->thumb = $thumb_path;
                $property_image->save();
            }
        }

        $this->alert('success', 'Image added successfully.');

        $this->dispatch('refresh');
        $this->show = false;
        $this->reset('thumbnail');
    }

    /**
     * Validate file MIME type to prevent spoofing
     */
    private function validateMimeType($file, array $allowedMimeTypes): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['path']);
        finfo_close($finfo);

        return in_array($mimeType, $allowedMimeTypes);
    }

    public function render()
    {
        return view('livewire.photo-library.add-new-image');
    }
}
