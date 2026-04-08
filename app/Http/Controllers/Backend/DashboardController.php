<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Agents;
use App\Models\Backend\Admin;
use App\Models\Payments;
use App\Models\Properties;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->session()->get('login_admin_59ba36addc2b2f9401580f014c7f58ea4e30989d');
        $admin = Admin::where('id', '=', $id)->first();
        $request->session()->put('admin', $admin);

        // KPI Stats
        $totalAgents         = Agents::count();
        $newAgentsThisMonth  = Agents::whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)->count();
        $totalProperties     = Properties::count();
        $publishedProperties = Properties::where('published', 1)->count();
        $activeSubscriptions = Subscription::where('stripe_status', 'active')->count();
        $totalRevenue        = Payments::where('status', 'Paid')->sum('amount');

        // Chart: new agents per month — last 6 months
        $agentRows = Agents::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get()->keyBy(fn($r) => $r->year.'-'.str_pad($r->month, 2, '0', STR_PAD_LEFT));

        // Chart: new properties per month — last 6 months
        $propertyRows = Properties::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get()->keyBy(fn($r) => $r->year.'-'.str_pad($r->month, 2, '0', STR_PAD_LEFT));

        // Build continuous 6-month label + count arrays
        $agentMonthLabels    = [];
        $agentMonthCounts    = [];
        $propertyMonthLabels = [];
        $propertyMonthCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $key   = $date->format('Y-m');
            $label = $date->format('M Y');
            $agentMonthLabels[]    = $label;
            $agentMonthCounts[]    = isset($agentRows[$key]) ? (int) $agentRows[$key]->count : 0;
            $propertyMonthLabels[] = $label;
            $propertyMonthCounts[] = isset($propertyRows[$key]) ? (int) $propertyRows[$key]->count : 0;
        }

        // Chart: active subscriptions by plan name (join plans table to resolve NULL names)
        $planRows = Subscription::where('subscriptions.stripe_status', 'active')
            ->leftJoin('plans', 'plans.stripe_plan_id', '=', 'subscriptions.stripe_price')
            ->select(
                DB::raw("COALESCE(subscriptions.name, plans.name, 'Unknown Plan') as plan_name"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('plan_name')
            ->get();
        $planNames  = $planRows->pluck('plan_name')->toArray();
        $planCounts = $planRows->pluck('count')->map(fn($v) => (int) $v)->toArray();

        $data = compact(
            'totalAgents', 'newAgentsThisMonth', 'totalProperties',
            'publishedProperties', 'activeSubscriptions', 'totalRevenue',
            'agentMonthLabels', 'agentMonthCounts',
            'propertyMonthLabels', 'propertyMonthCounts',
            'planNames', 'planCounts'
        );

        return view('admin.dashboard')->with($data);
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::with('agent')
            ->orderBy('id', 'desc')
            ->paginate(25);

        $totalActive   = Subscription::where('stripe_status', 'active')->count();
        $totalAll      = Subscription::count();

        return view('admin.subscriptions', compact('subscriptions', 'totalActive', 'totalAll'));
    }

    public function revenue()
    {
        $payments     = Payments::with('agent')->orderBy('id', 'desc')->paginate(25);
        $totalRevenue = Payments::where('status', 'Paid')->sum('amount');
        $totalPaid    = Payments::where('status', 'Paid')->count();
        $totalPending = Payments::where('status', 'Pending')->count();

        return view('admin.revenue', compact('payments', 'totalRevenue', 'totalPaid', 'totalPending'));
    }
}
