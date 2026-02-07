<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function financialReport(Request $request)
    {

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now();

        $financialStats = Order::whereBetween('start_date', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(sub_total) as total_revenue'),
                DB::raw('SUM(payment_received) as total_received'),
                DB::raw('SUM(remaining_payment) as total_pending'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('AVG(sub_total) as average_order_value')
            )->first();

        $paymentMethods = Order::whereBetween('start_date', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(payment_received) as total'))
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        $dailyRevenue = Order::whereBetween('start_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(start_date) as date'),
                DB::raw('SUM(sub_total) as revenue'),
                DB::raw('SUM(payment_received) as received'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $customerRevenue = Order::whereBetween('start_date', [$startDate, $endDate])
            ->select(
                'customer_id',
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(sub_total) as total_spent')
            )
            ->with('customer:id,first_name,last_name')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return view('pages.report.financial', compact(
            'financialStats',
            'paymentMethods',
            'dailyRevenue',
            'customerRevenue',
            'startDate',
            'endDate'
        ));

    }

    public function DueList(Request $request)
    {
        $selectedDate = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $selectedStatus = $request->status;
        $selectedDateSelect = $request->date_select;

        $query = Order::with(['customer', 'details.coats:id,coat_no', 'details.trousers:id,coat_no', 'details.wests:id,coat_no', 'details.nationals:id,coat_no'])
            ->select(
                'orders.*',
                'customers.first_name',
                'customers.last_name',
                'customers.phone',
                'customers.address'
            )
            ->join('customers', 'orders.customer_id', '=', 'customers.id');

        //Apply date filter
        if ($selectedDateSelect === null) {
            $query->where('orders.end_date', $selectedDate);
        } elseif ($selectedDateSelect === 'on') {
            $query->where('orders.end_date', $selectedDate);
        } elseif ($selectedDateSelect === 'before') {
            $query->where('orders.end_date', '<=', $selectedDate);
        }

        // Apply status filter
        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('orders.status', $selectedStatus);
        } else {
            $query->whereIn('orders.status', [1, 2]); // Default filter for active and overdue
        }

        $dueReturns = $query->paginate(10);
        // Get item details for each order
        foreach ($dueReturns as $order) {
            $order->items = $order->details->map(function ($detail) {
                return [
                    'trouser' => $detail->trousers?->coat_no,
                    'coat' => $detail->coats?->coat_no,
                    'west' => $detail->wests?->coat_no,
                    'national' => $detail->nationals?->coat_no,
                ];
            });
        }
        $statusList = OrderStatus::whereIn('id', [1, 2])->get();

        return view('pages.report.due-list', compact('dueReturns', 'selectedDate', 'selectedStatus', 'selectedDateSelect', 'statusList'));
    }

    public function saleReport(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        // Get orders with details
        $orders = Order::with(['customer', 'details.coats:id,coat_no', 'details.trousers:id,coat_no', 'details.wests:id,coat_no', 'details.nationals:id,coat_no', 'user'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($orders as $order) {
            $order->items = $order->details->map(function ($detail) {
                return [
                    'trouser' => $detail->trousers?->coat_no,
                    'coat' => $detail->coats?->coat_no,
                    'west' => $detail->wests?->coat_no,
                    'national' => $detail->nationals?->coat_no,
                ];
            });
        }
        // Calculate summary statistics
        $summary = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('sub_total'),
            'total_received' => $orders->sum('payment_received'),
            'total_pending' => $orders->sum('remaining_payment'),
            'average_order_value' => $orders->avg('sub_total'),
        ];

        return view('pages.report.sale-report', compact(
            'orders',
            'summary',
            'startDate',
            'endDate'
        ));
    }

    public function profitReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now();

        $profitAnalysis = OrderDetail::join('orders', 'orders.id', '=', 'order_details.order_id')
            ->with('coats:id,coat_no,cost', 'trousers:id,coat_no,cost', 'wests:id,coat_no,cost', 'nationals:id,coat_no,cost')
            ->whereBetween('orders.start_date', [$startDate, $endDate])
            ->select(
                'order_details.*',
                DB::raw("(
            SELECT SUM(od2.rent_or_sale_price)
            FROM order_details od2
            JOIN orders o2 ON o2.id = od2.order_id
            WHERE o2.start_date BETWEEN '{$startDate}' AND '{$endDate}'
        ) as total_revenue"),
                DB::raw("(
            SELECT COUNT(DISTINCT o3.id)
            FROM orders o3
            JOIN order_details od3 ON od3.order_id = o3.id
            WHERE o3.start_date BETWEEN '{$startDate}' AND '{$endDate}'
        ) as total_orders"),
                DB::raw("(
            SELECT COUNT(od4.id)
            FROM order_details od4
            JOIN orders o4 ON o4.id = od4.order_id
            WHERE o4.start_date BETWEEN '{$startDate}' AND '{$endDate}'
        ) as total_items_rented")
            )
            ->first();

        // Calculate total cost from relationships
        $totalCost = 0;

        if ($profitAnalysis) {
            // Sum up costs from coats
            $totalCost += $profitAnalysis->coats?->sum('cost');
            $totalCost += $profitAnalysis->trousers?->sum('cost');
            $totalCost += $profitAnalysis->wests?->sum('cost');
            $totalCost += $profitAnalysis->nationals?->sum('cost');
            // Add the total_cost property to the result
            $profitAnalysis->total_cost = $totalCost;
        }

        // Item-wise Profit Analysis
        $itemProfits = OrderDetail::join('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('items as coats', 'coats.id', '=', 'order_details.coat')
            ->leftJoin('items as trousers', 'trousers.id', '=', 'order_details.trouser')
            ->leftJoin('items as wests', 'wests.id', '=', 'order_details.west')
            ->leftJoin('items as nationals', 'nationals.id', '=', 'order_details.national')
            ->whereBetween('orders.start_date', [$startDate, $endDate])
            ->select(
                'coats.coat_no as coat_no',
                'coats.name as name',
                'coats.cost as cost',
                DB::raw('COUNT(order_details.id) as rental_count'),
                DB::raw('SUM(order_details.rent_or_sale_price) as total_revenue'),
                DB::raw('SUM(order_details.rent_or_sale_price) - (coats.cost * COUNT(order_details.id)) as estimated_profit')
            )
            ->whereNotNull('order_details.coat')
            ->groupBy('coats.id', 'coats.coat_no', 'coats.name', 'coats.cost')
            ->union(
                OrderDetail::join('orders', 'orders.id', '=', 'order_details.order_id')
                    ->leftJoin('items as trousers', 'trousers.id', '=', 'order_details.trouser')
                    ->whereBetween('orders.start_date', [$startDate, $endDate])
                    ->select(
                        'trousers.coat_no',
                        'trousers.name',
                        'trousers.cost',
                        DB::raw('COUNT(order_details.id) as rental_count'),
                        DB::raw('SUM(order_details.rent_or_sale_price) as total_revenue'),
                        DB::raw('SUM(order_details.rent_or_sale_price) - (trousers.cost * COUNT(order_details.id)) as estimated_profit')
                    )
                    ->whereNotNull('order_details.trouser')
                    ->groupBy('trousers.id', 'trousers.coat_no', 'trousers.name', 'trousers.cost')
            )
            ->union(
                OrderDetail::join('orders', 'orders.id', '=', 'order_details.order_id')
                    ->leftJoin('items as wests', 'wests.id', '=', 'order_details.west')
                    ->whereBetween('orders.start_date', [$startDate, $endDate])
                    ->select(
                        'wests.coat_no',
                        'wests.name',
                        'wests.cost',
                        DB::raw('COUNT(order_details.id) as rental_count'),
                        DB::raw('SUM(order_details.rent_or_sale_price) as total_revenue'),
                        DB::raw('SUM(order_details.rent_or_sale_price) - (wests.cost * COUNT(order_details.id)) as estimated_profit')
                    )
                    ->whereNotNull('order_details.west')
                    ->groupBy('wests.id', 'wests.coat_no', 'wests.name', 'wests.cost')
            )
            ->union(
                OrderDetail::join('orders', 'orders.id', '=', 'order_details.order_id')
                    ->leftJoin('items as nationals', 'nationals.id', '=', 'order_details.national')
                    ->whereBetween('orders.start_date', [$startDate, $endDate])
                    ->select(
                        'nationals.coat_no',
                        'nationals.name',
                        'nationals.cost',
                        DB::raw('COUNT(order_details.id) as rental_count'),
                        DB::raw('SUM(order_details.rent_or_sale_price) as total_revenue'),
                        DB::raw('SUM(order_details.rent_or_sale_price) - (nationals.cost * COUNT(order_details.id)) as estimated_profit')
                    )
                    ->whereNotNull('order_details.national')
                    ->groupBy('nationals.id', 'nationals.coat_no', 'nationals.name', 'nationals.cost')
            )
            ->orderByDesc('estimated_profit')
            ->get();
        //

        return view('pages.report.profit', compact(
            'profitAnalysis',
            'itemProfits',
            'startDate',
            'endDate'
        ));
    }

    public function totalStock()
    {
        $coatCount = Item::where('item_category_id', 1)->count();
        $trouserCount = Item::where('item_category_id', 2)->count();
        $westCount = Item::where('item_category_id', 3)->count();
        $nationalCount = Item::where('item_category_id', 4)->count();

        return view('pages.report.total-stock', compact(
            'coatCount',
            'trouserCount',
            'westCount',
            'nationalCount'
        ));

    }
}
