<?php

namespace App\Livewire\Amenity;

use App\Models\Amenities;
use App\Models\Properties;
use DB;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('refresh')]
class Index extends Component
{
    public $property;

    public $property_amenities;

    public $amenities;

    public $amenities_array;

    public $custom_amenities = [];

    #[On('add_new_amenity')]
    public function addNewAmenity($value)
    {
        array_push($this->custom_amenities, $value);
        array_push($this->amenities_array, $value);
        $this->dispatch('refresh');
    }

    public function addAmenity($amenity_id)
    {
        $this->property->property_amenities()->create([
            'property_id' => $this->property->id,
            'amenity_id' => $amenity_id,
        ]);
        $this->property_amenities = $this->property->property_amenities()->with('Amenities')->get();
        $this->amenities = Amenities::where('agent_id', 0)
            ->orWhere('agent_id', $this->property->agent_id)
            ->get();
        $this->amenities_array = $this->property->property_amenities()->pluck('amenity_id')->toArray();
        $this->dispatch('refresh');
    }

    public function removeAmenity($value)
    {
        $this->property->property_amenities()->where('amenity_id', $value)->where('property_id', $this->property->id)->delete();
        $this->dispatch('refresh');
    }

    public function removeCustomAmenity($value)
    {
        if (($key = array_search($value, $this->amenities_array)) !== false) {
            unset($this->amenities_array[$key]);
        }
        if (($key = array_search($value, $this->custom_amenities)) !== false) {
            unset($this->custom_amenities[$key]);
        }
        $this->dispatch('refresh');
    }

    public function deleteAmenities()
    {
        $this->property->property_amenities()->where('property_id', $this->property->id)->delete();
        $this->amenities = Amenities::where('agent_id', 0)
            ->orWhere('agent_id', $this->property->agent_id)
            ->get();
        $this->amenities_array = $this->property->property_amenities()->pluck('amenity_id')->toArray();
        $this->property_amenities = [];
        $this->dispatch('refresh');
    }

    public function save()
    {
        DB::transaction(function () {
            $this->property->agent_id = $this->property->agent_id;
            $this->property->property_amenities()->delete();

            $amenityIds = $this->amenities_array;

            foreach ($amenityIds as $amenityId) {
                if (! is_numeric($amenityId)) {
                    $newAmenity = Amenities::create([
                        'agent_id' => $this->property->agent_id,
                        'name' => $amenityId,
                    ]);

                    $amenityId = $newAmenity->id;
                }

                $this->property->property_amenities()->create([
                    'amenity_id' => $amenityId,
                ]);
            }
        });
        session()->flash('success', 'Property amenities have been saved.');

        return $this->redirect('/agent/property/price-feature');
    }

    public function mount(Properties $property)
    {
        $this->property = $property;
        $this->property_amenities = $property->property_amenities()->with('Amenities')->get();
        $this->amenities = Amenities::where('agent_id', 0)
            ->orWhere('agent_id', $property->agent_id)
            ->get();
        $this->amenities_array = $property->property_amenities()->pluck('amenity_id')->toArray();
    }

    public function render()
    {
        return view('livewire.amenity.index');
    }
}
