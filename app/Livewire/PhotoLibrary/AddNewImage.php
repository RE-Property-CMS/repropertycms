<?php

namespace App\Livewire\PhotoLibrary;

use App\Models\Properties;
use App\Models\PropertyImages;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
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
        if (empty($this->thumbnail)) {
            $this->alert('error', 'Please select at least one image.');
            return;
        }

        $files = is_array($this->thumbnail) ? array_values($this->thumbnail) : [$this->thumbnail];
        $isS3  = config('filesystems.default') === 's3';

        foreach ($files as $file) {
            if (empty($file['path'])) {
                continue;
            }

            if ($isS3) {
                $image     = new File($file['path']);
                $path      = uploadS3Image('property_images', $image);
                $thumbPath = uploadS3ImageThumb('property_images_thumb', $image, env('THUMB_WIDTH'));
            } else {
                $ext      = pathinfo($file['name'] ?? '', PATHINFO_EXTENSION) ?: 'jpg';
                $filename = uniqid() . '.' . strtolower($ext);

                // Store in storage/app/public/property_images/ — accessible via /storage/
                Storage::disk('public')->put('property_images/' . $filename, file_get_contents($file['path']));

                // Path stored in DB — asset_s3() with local disk returns asset(path)
                // which resolves to /storage/property_images/filename via the storage symlink
                $path      = 'storage/property_images/' . $filename;
                $thumbPath = $path;
            }

            PropertyImages::create([
                'property_id' => $this->property->id,
                'file_name'   => $path,
                'thumb'       => $thumbPath,
            ]);
        }

        $this->alert('success', 'Images added successfully.');
        $this->dispatch('refresh');
        $this->show = false;
        $this->reset('thumbnail');
    }

    public function render()
    {
        return view('livewire.photo-library.add-new-image');
    }
}
