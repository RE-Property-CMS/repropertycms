<?php

namespace App\Livewire\Agent;

use App\Models\Backend\Admin;
use App\Models\Plan;
use App\Models\Properties;
use App\Models\Subscription;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

#[On("refresh")]
class Dashboard extends Component
{
    use LivewireAlert;

    public $agent;
    public $portal = null;
    public $property_descending_order;
    public $published_properties;
    public $property_update;
    public $subscriptions;
    public $publishedPropertiesCount;
    public $subscriptionAlert;
    public function publishProperty($id)
    {
        $activeSubscription = $this->agent->hasActiveSubscription();

        if ($activeSubscription) {
            $plan = Plan::where("stripe_plan_id", $this->agent->activeSubscription->stripe_price)->first();
            if ($plan) {
                $publishedPropertiesCount = Properties::where("agent_id", $this->agent->id)->where("published", 1)->count();
                if ($plan->credits > $publishedPropertiesCount) {
                    // Dispatch a browser event — Alpine handles the modal
                    $this->dispatch('show-publish-confirm', id: $id);
                } else {
                    $this->alert("error", "You already have used full credit. To Publish more property, please Upgrade your plan.", ["toast" => true]);
                }
                return;
            } else {
                $this->alert("error", "No Plan Found!", ["toast" => true]);
            }
        }
        return $this->dispatch("open-agent-plans");
    }

    public function doPublishProperty($id)
    {
        $property = Properties::find($id);
        if ($property) {
            $property->update(["published" => true, "reviewed" => true, "publish_date" => Carbon::now()]);
            $this->alert("success", "Property published successfully!", ["toast" => true]);
            $this->dispatch("refresh");
        }
    }

    public function doDeleteProperty($id)
    {
        $property = Properties::find($id);
        $property->delete();
        $this->alert("success", "Property deleted successfully!", ["toast" => true]);
        $this->dispatch("refresh");
    }

    public function stripePortal()
    {
        $stripe = new StripeClient(config("stripe.api_keys.secret_key"));
        $stripeCustomer = $this->agent->createOrGetStripeCustomer();
        try {
            $response = $stripe->billingPortal->sessions->create([
                "customer" => $stripeCustomer->id,
                "return_url" => route("agent.dashboard"),
            ]);
            $this->portal = $response->url;
            return redirect($this->portal);
        } catch (ApiErrorException $e) {
            $this->alert("error", "Error generating portal session: ".$e->getMessage());
            \Log::error("Stripe API Error: ".$e->getMessage());
        }
    }

    public function mount()
    {
        $this->agent = auth()->user();
        $this->subscriptions = Subscription::where("agent_id", $this->agent->id)->get();
        $this->getSubscriptionAlert();
        $this->publishedPropertiesCount = Properties::where("agent_id", $this->agent->id)->where("published", 1)->count();
    }

    public function render()
    {
        $this->published_properties = $this->agent->properties()->where("published", 1)->orderBy("id", "desc")->limit(5)->with("state")->get();
        $this->property_descending_order = $this->agent->properties()->orderBy("id", "desc")->limit(5)->with("state")->get();
        $this->property_update = $this->agent->properties()->where("published", 0)->orderBy("updated_at", "desc")->limit(5)->get();
        return view("livewire.agent.dashboard");
    }

    public function subscriptionPlan()
    {
        return $this->dispatch("open-agent-plans");
    }

    public function getSubscriptionAlert()
    {
        $subscription = Subscription::where("agent_id", $this->agent->id)->orderByDesc("created_at")->first();
        $this->subscriptionAlert = null;
        if ($subscription) {
            $now = Carbon::now();
            if ($subscription->current_period_end) {
                $currentPeriodEnd = Carbon::parse($subscription->current_period_end);
                if ($currentPeriodEnd->greaterThan($now) && $now->diffInDays($currentPeriodEnd) <= 5) {
                    $this->subscriptionAlert = [
                        "alert_message" => "Your subscription will expire in " . intval($now->diffInDays($currentPeriodEnd)) . " days.",
                        "alert_color" => "#ffe5b4",
                        "alert_foreground_color" => "#ff8c00",
                        "alert_icon" => "<i class=\"fa fa-exclamation-triangle text-warning\"></i>",
                        "alert_section" => "Subscription Expiring Soon",
                    ];
                }
                if ($currentPeriodEnd->lessThan($now)) {
                    $this->subscriptionAlert = [
                        "alert_message" => "Your subscription has expired. All your properties have been unpublished. Please renew your subscription.",
                        "alert_color" => "#ffe5e5",
                        "alert_foreground_color" => "#b71c1c",
                        "alert_icon" => "<i class=\"fa fa-exclamation-circle text-danger\"></i>",
                        "alert_section" => "Subscription Expired",
                    ];
                }
            }
        }
    }
}
