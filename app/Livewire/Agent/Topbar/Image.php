<?php

namespace App\Livewire\Agent\Topbar;

use App\Livewire\Agent\Topbar\Choose;
use App\Models\Properties;
use App\Models\PropertyImages;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('refresh')]
class Image extends Component
{
    use LivewireAlert;

    public bool $show = false;

    public $property_images = [];

    public $property;

    #[On('open-topbar-image')]
    public function openModal(int $propertyId): void
    {
        $this->property = Properties::find($propertyId);
        $this->show = true;
    }

    public function closeModal(): void
    {
        $this->show = false;
    }

    public function saveFeatureImage($image_id)
    {
        $this->property->update(['featured_image' => $image_id]);
        $this->alert('success', 'Feature Image Successfully Selected!');
        $this->dispatch('refresh');
        $this->dispatch('refresh')->to(Choose::class);
        $this->show = false;
    }

    public function render()
    {
        if ($this->property) {
            $this->property_images = PropertyImages::where('property_id', $this->property->id)->get();
        }

        return view('livewire.agent.topbar.image');
    }
}
