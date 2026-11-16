<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $stats = [
            'orders_today' => Order::whereDate('created_at', $today)->count(),
            'active_orders' => Order::whereNotIn('status', ['released'])->count(),
            'ready_for_pickup' => Order::where('status', 'ready_for_pickup')->count(),
            'sales_today' => Payment::whereDate('paid_at', $today)->sum('amount'),
            'expenses_today' => Expense::whereDate('expense_date', $today)->sum('amount'),
            'pending_payments' => Order::whereNotIn('status', ['released'])
                ->get()
                ->sum(fn (Order $order) => $order->balance),
            'customers' => Customer::count(),
        ];

        $lowStockItems = InventoryItem::get()->filter->is_low_stock;

        $recentOrders = Order::with(['customer', 'service', 'payments'])
            ->latest()
            ->take(8)
            ->get();

        $salesLast7Days = collect(range(6, 0))->mapWithKeys(function (int $daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                $date->format('D') => Payment::whereDate('paid_at', $date)->sum('amount'),
            ];
        });

        return view('dashboard', compact('stats', 'lowStockItems', 'recentOrders', 'salesLast7Days'));
    }
}
