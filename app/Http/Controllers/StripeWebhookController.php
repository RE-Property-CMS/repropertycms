<?php

namespace App\Http\Controllers;

use App\Models\Agents;
use App\Models\Backend\Admin;
use App\Models\Payments;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;
use App\Notifications\AdminSubscriptionExpired;
use App\Notifications\AdminSubscriptionNotification;
use App\Notifications\AdminSubscriptionRenewed;
use App\Notifications\AgentSubscriptionExpired;
use App\Notifications\AgentSubscriptionRenewed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /*
     * Stripe Customer Webhooks
     * */
    public function receiveWebhook(Request $request)
    {
        $endpoint_secret = config('stripe.api_keys.webhook_secret');
        $payload = @file_get_contents('php://input');
        Log::info('Stripe Webhook Received:', ['payload' => $payload]);

        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;
        if (! $sig_header) {
            Log::error('Stripe Webhook Error: Missing signature header');

            return response()->json(['error' => 'Missing Stripe signature'], 400);
        }

        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
            Log::info('Stripe Webhook Parsed Successfully', ['event_type' => $event->type]);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook Error: Invalid payload', ['exception' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook Error: Invalid signature', ['exception' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'customer.subscription.created':
                $subscription = $event->data->object;
                Log::info('Handling customer.subscription.created', ['subscription_id' => $subscription->id]);
                $this->handleSubscriptionCreated($subscription);
                break;

            case 'customer.subscription.updated':
                $subscription = $event->data->object;
                Log::info('Handling customer.subscription.updated', ['subscription_id' => $subscription->id]);
                $this->handleSubscriptionUpdated($subscription);
                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                Log::info('Handling customer.subscription.deleted', ['subscription_id' => $subscription->id]);
                $this->handleSubscriptionDeleted($subscription);
                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                Log::info('Handling invoice.payment_succeeded', ['invoice_id' => $invoice->id]);
                $this->handleInvoicePaymentSucceeded($invoice);
                break;

            default:
                Log::warning('Received unknown event type', ['event_type' => $event->type]);
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function handleSubscriptionCreated($subscription)
    {
        $agent = Agents::where('customer_id', $subscription->customer)->first();

        if ($agent) {
            $stripePriceId = $subscription->items->data[0]->price->id;
            $startDate     = Carbon::createFromTimestamp($subscription->start_date);
            $plan          = Plan::where('stripe_plan_id', $stripePriceId)->first();

            if ($plan && $plan->interval === 'year') {
                $periodEnd = $startDate->copy()->addYear();
            } else {
                $periodEnd = $startDate->copy()->addMonth();
            }

            Subscription::create([
                'agent_id' => $agent->id,
                'name' => $plan?->name ?? $subscription->metadata->plan ?? null,
                'stripe_id' => $subscription->id,
                'stripe_status' => $subscription->status,
                'stripe_price' => $stripePriceId,
                'quantity' => $subscription->quantity,
                'start_date' => $startDate->format('Y-m-d H:i:s'),
                'current_period_end' => $periodEnd->format('Y-m-d H:i:s'),
            ]);

            $agent->notify(new \App\Notifications\SubscriptionWelcome($agent));

            $adminEmail = Admin::first()?->email;
            if ($adminEmail) {
                Notification::route('mail', $adminEmail)
                    ->notify(new AdminSubscriptionNotification($agent, $subscription));
            }
        }
    }

    protected function handleSubscriptionUpdated($subscription)
    {
        $sub = Subscription::where('stripe_id', $subscription->id)->first();

        if ($sub) {
            $stripePriceId = $subscription->items->data[0]->price->id;
            $plan          = Plan::where('stripe_plan_id', $stripePriceId)->first();
            $startDate     = Carbon::parse($sub->start_date);

            if ($plan && $plan->interval === 'year') {
                $periodEnd = $startDate->copy()->addYear();
            } else {
                $periodEnd = $startDate->copy()->addMonth();
            }

            $sub->stripe_status      = $subscription->status;
            $sub->current_period_end = $periodEnd->format('Y-m-d H:i:s');
            $sub->quantity           = $subscription->quantity;
            $sub->stripe_price       = $stripePriceId;
            $sub->save();

            if ($subscription->status === 'active') {
                $agent = Agents::where('customer_id', $subscription->customer)->first();

                if ($agent) {
                    $published_properties = $agent->totalPublishedPropertiesCount;
                    $activeSubscription = $agent->activeSubscription;

                    if (! $activeSubscription || ! $activeSubscription->plan) {
                        Log::warning('No active subscription or plan found for agent during subscription.updated', ['agent_id' => $agent->id]);
                        return;
                    }

                    $activePlanCredits = $activeSubscription->plan->credits;

                    Log::info('Agent Subscription Check', [
                        'agent_id' => $agent->id,
                        'published_properties' => $published_properties,
                        'activePlanCredits' => $activePlanCredits,
                    ]);

                    if ($published_properties > $activePlanCredits) {
                        $unpublishPropertiesCount = $published_properties - $activePlanCredits;

                        Log::info('Unpublishing Properties', [
                            'agent_id' => $agent->id,
                            'unpublishPropertiesCount' => $unpublishPropertiesCount,
                        ]);

                        $agent->publishedProperties()
                            ->orderBy('publish_date', 'desc')
                            ->limit($unpublishPropertiesCount)
                            ->update(['published' => false]);
                    }

                    $agent->notify(new AgentSubscriptionRenewed($subscription, $agent));

                    $adminEmail = Admin::first()?->email;
                    if ($adminEmail) {
                        Notification::route('mail', $adminEmail)
                            ->notify(new AdminSubscriptionRenewed($agent));
                    }
                }
            }
        }
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        // Skip $0 invoices (e.g. trial starts)
        if ($invoice->amount_paid === 0) {
            return;
        }

        $agent = Agents::where('customer_id', $invoice->customer)->first();
        if (! $agent) {
            Log::warning('invoice.payment_succeeded: agent not found', ['customer' => $invoice->customer]);
            return;
        }

        $priceId = $invoice->lines->data[0]->price->id ?? null;
        $plan    = $priceId ? Plan::where('stripe_plan_id', $priceId)->first() : null;

        Payments::create([
            'agent_id'     => $agent->id,
            'plan_id'      => $plan?->id ?? 0,
            'payment_date' => now()->toDateString(),
            'payment_id'   => $invoice->id,
            'amount'       => $invoice->amount_paid / 100,
            'currency'     => strtoupper($invoice->currency),
            'status'       => 'Paid',
            'active'       => true,
        ]);

        Log::info('Payment recorded', [
            'agent_id'   => $agent->id,
            'invoice_id' => $invoice->id,
            'amount'     => $invoice->amount_paid / 100,
        ]);
    }

    protected function handleSubscriptionDeleted($subscription)
    {
        $sub = Subscription::where('stripe_id', $subscription->id)->first();

        if ($sub) {
            $sub->stripe_status = $subscription->status;
            $sub->ends_at = date('Y-m-d H:i:s', $subscription->ended_at);
            $sub->save();

            $agent = Agents::where('customer_id', $subscription->customer)->first();
            if ($agent) {
                $agent->notify(new AgentSubscriptionExpired($subscription));

                // Soft-delete all published properties instead of hard-deleting
                $agent->publishedProperties()->each(fn ($p) => $p->delete());

                $adminEmail = Admin::first()?->email;
                if ($adminEmail) {
                    Notification::route('mail', $adminEmail)
                        ->notify(new AgentSubscriptionExpired($agent));
                }
            }
        }
    }
}
