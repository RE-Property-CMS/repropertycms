<?php

namespace App\Livewire\Video;

use App\Models\Properties;
use App\Models\PropertyVideos;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class View extends Component
{
    use LivewireAlert;

    public bool $show = false;

    public $property;

    public $property_video;

    public $display_on = 'both';

    #[On('open-video-view')]
    public function openModal(int $propertyId, int $videoId): void
    {
        $this->property = Properties::find($propertyId);
        $this->property_video = PropertyVideos::find($videoId);
        $this->display_on = $this->getInitialDisplayOnValue();
        $this->show = true;
    }

    public function closeModal(): void
    {
        $this->show = false;
    }

    public function updatedDisplayOn($value)
    {
        switch ($value) {
            case 'both':
                $this->property->property_videos()->update(['main_video' => 0, 'featured' => 0]);
                PropertyVideos::where('property_id', $this->property->id)->update([
                    'main_video' => 1,
                    'featured' => 1,
                ]);
                $this->alert('success', 'Property Video set as cover & featured.');
                break;
            case 'cover':
                $this->property->property_videos()->update(['main_video' => 0]);
                PropertyVideos::where('property_id', $this->property->id)->update([
                    'main_video' => 1,
                    'featured' => 0,
                ]);
                $this->alert('success', 'Property Video set as '.$value);
                break;
            case 'featured':
                $this->property->property_videos()->update(['featured' => 0]);
                PropertyVideos::where('property_id', $this->property->id)->update([
                    'main_video' => 0,
                    'featured' => 1,
                ]);
                $this->alert('success', 'Property Video set as '.$value);
                break;
            default:
                $this->property_video->refresh(); //Reverts back to the DB state
                break;

        }

        $this->dispatch('refresh');
    }

    public function render()
    {
        return view('livewire.video.view');
    }

    private function getInitialDisplayOnValue(): string
    {
        if (! $this->property_video) {
            return 'both';
        }
        if ($this->property_video->featured && $this->property_video->main_video) {
            return 'both';
        } elseif ($this->property_video->main_video) {
            return 'cover';
        } elseif ($this->property_video->featured) {
            return 'featured';
        } else {
            return '';
        }
    }
}
