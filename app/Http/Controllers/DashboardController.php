<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    private $metricsPassword;

    public function __construct()
    {
        $this->metricsPassword = config('services.matrics.password');
    }

    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Get key metrics only if authorized
        $metrics = [];
        if (Session::get('show_metrics', false)) {
            $metrics = [
                'total_revenue' => Order::sum('sub_total'),
                'monthly_revenue' => Order::where('start_date', '>=', $startOfMonth)->sum('sub_total'),
                'pending_payments' => Order::sum('remaining_payment'),
                'total_orders' => Order::count(),
                'monthly_orders' => Order::where('start_date', '>=', $startOfMonth)->count(),
                'total_items' => Item::count(),
                'total_customers' => Customer::count(),
            ];
            // Monthly revenue trend
            $revenueTrend = Order::select(
                DB::raw('DATE(start_date) as date'),
                DB::raw('SUM(sub_total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
                ->where('start_date', '>=', Carbon::now()->subMonths(6))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

        } else {
            $metrics = [
                'total_revenue' => '***',
                'monthly_revenue' => '***',
                'pending_payments' => '***',
                'total_orders' => '***',
                'monthly_orders' => '***',
                'total_items' => Item::count(),
                'total_customers' => Customer::count(),
            ];

            $revenueTrend = collect([
                ['date' => now()->subDays(5)->format('Y-m-d'), 'revenue' => '***', 'orders' => '***'],
                ['date' => now()->subDays(4)->format('Y-m-d'), 'revenue' => '***', 'orders' => '***'],
                ['date' => now()->subDays(3)->format('Y-m-d'), 'revenue' => '***', 'orders' => '***'],
                ['date' => now()->subDays(2)->format('Y-m-d'), 'revenue' => '***', 'orders' => '***'],
                ['date' => now()->subDays(1)->format('Y-m-d'), 'revenue' => '***', 'orders' => '***'],
            ]);

        }

        // Recent orders
        $recentOrders = Order::with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'metrics',
            'recentOrders',
            'revenueTrend',
        ));
    }

    public function toggleMetrics(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (Hash::check($request->password, Hash::make($this->metricsPassword))) {
            Session::put('show_metrics', ! Session::get('show_metrics', false));

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid password']);
    }
}
