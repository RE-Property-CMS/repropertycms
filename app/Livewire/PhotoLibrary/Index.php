<?php

namespace App\Livewire\PhotoLibrary;

use App\Models\Properties;
use App\Models\PropertyImages;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('refresh')]
class Index extends Component
{
    use LivewireAlert;

    public $property;
    public $property_images;

    public function mount(Properties $property)
    {
        $this->property = $property;
    }

    public function doDeleteImage($id)
    {
        $property_image = PropertyImages::find($id);
        if ($property_image->file_name) {
            deleteS3Image($property_image->file_name);
        }
        if ($property_image->thumb) {
            deleteS3Image($property_image->thumb);
        }
        if ($property_image->small) {
            deleteS3Image($property_image->small);
        }
        $property_image->delete();

        $this->dispatch('refresh');
        $this->alert('success', 'Image deleted successfully.', ['toast' => true]);
    }

    public function render()
    {
        $this->property_images = PropertyImages::where('property_id', '=', $this->property->id)->get();
        return view('livewire.photo-library.index');
    }
}
