<?php

namespace App\Livewire\Agent\Slider;

use App\Enums\BannerType;
use App\Models\Properties;
use App\Models\PropertyImages;
use App\Models\PropertySlider;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('refresh')]
class Index extends Component
{
    use LivewireAlert;

    public $property;

    public $property_slider;

    public $property_images;

    public $property_header;

    public function saveImageSlider() {}

    public function updateImageOrder($orderedIds)
    {
        dd($orderedIds);
        //		$this->tasks = collect($orderedIds)
        //			->map(function ($id) {
        //				return collect($this->tasks)->firstWhere('id', $id);
        //			})
        //			->toArray();
    }

    public function updatedPropertyHeader($value)
    {
        if ($value) {
            $this->property->update(['main_section' => BannerType::Slider]);

            $this->alert('success', 'Property Banner Set as Slider Successfully', [
                'toast' => true,
            ]);
        } else {
            $this->property->update(['main_section' => null]);
            $this->alert('success', 'Slider removed from Property Banner', [
                'toast' => true,
            ]);
        }

        $this->dispatch('refresh');
    }

    public function mount(Properties $property)
    {
        $this->property = $property;
        $this->property_header = $this->property->main_section == BannerType::Slider;
    }

    public function render()
    {
        $this->property_slider = PropertySlider::where('property_id', '=', $this->property->id)->with('property_images')->get();
        $this->property_images = PropertyImages::where('property_id', '=', $this->property->id)->get();

        return view('livewire.agent.slider.index');
    }
}
