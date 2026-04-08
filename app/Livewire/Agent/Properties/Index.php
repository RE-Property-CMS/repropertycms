<?php

namespace App\Livewire\Agent\Properties;

use App\Models\Plan;
use App\Models\Properties;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use LivewireAlert;

    public $agent;
    public function publishProperty($id)
    {
        $activeSubscription = $this->agent->hasActiveSubscription();

        if ($activeSubscription) {
            $plan = Plan::where('stripe_plan_id', $this->agent->activeSubscription->stripe_price)->first();
            if ($plan) {
                $publishedPropertiesCount = Properties::where('agent_id', $this->agent->id)->where('published', 1)->count();
                if ($plan->credits > $publishedPropertiesCount) {
                    $this->dispatch('show-publish-confirm', id: $id);
                } else {
                    $this->alert('error', 'You already have used full credit. To Publish more property, please Upgrade your plan.', [
                        'toast' => true,
                    ]);
                }
                return;
            } else {
                $this->alert('error', 'No Plan Found!', ['toast' => true]);
            }
        }

        return $this->dispatch('open-agent-plans');
    }

    public function doPublishProperty($id)
    {
        $property = Properties::find($id);
        if ($property) {
            $property->update([
                'published' => true,
                'reviewed'  => true,
                'publish_date' => Carbon::now(),
            ]);
            $this->alert('success', 'Property published successfully!', ['toast' => true]);
            $this->dispatch('refresh');
        }
    }

    public function doDeleteProperty($id)
    {
        $property = Properties::find($id);
        $property->delete();
        $this->alert('success', 'Property deleted successfully!', ['toast' => true]);
        $this->dispatch('refresh');
    }

    public function mount()
    {
        $this->agent = auth()->user();
    }

    public function render()
    {
        $properties = $this->agent->properties()->orderBy('id', 'desc')->paginate(10);
        return view('livewire.agent.properties.index', compact('properties'));
    }
}
