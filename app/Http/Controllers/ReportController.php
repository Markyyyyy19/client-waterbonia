<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type', 'daily')->toString();

        if ($type === 'daily') {
            $date = $request->filled('date') ? Carbon::parse($request->string('date')) : Carbon::today();
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();
            $label = 'Daily Sales Report - '.$date->format('F j, Y');
        } elseif ($type === 'weekly') {
            $date = $request->filled('date') ? Carbon::parse($request->string('date')) : Carbon::today();
            $start = $date->copy()->startOfWeek();
            $end = $date->copy()->endOfWeek();
            $label = 'Weekly Sales Report - '.$start->format('M j').' to '.$end->format('M j, Y');
        } else {
            $date = $request->filled('date') ? Carbon::parse($request->string('date')) : Carbon::today();
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            $label = 'Monthly Sales Report - '.$date->format('F Y');
        }

        $payments = Payment::with(['order.customer', 'order.service', 'receiver'])
            ->whereBetween('paid_at', [$start, $end])
            ->latest('paid_at')
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->latest()
            ->get();

        $orders = Order::with(['customer', 'service'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $totals = [
            'sales' => $payments->sum('amount'),
            'expenses' => $expenses->sum('amount'),
            'net' => $payments->sum('amount') - $expenses->sum('amount'),
            'orders' => $orders->count(),
            'transactions' => $payments->count(),
            'released' => $orders->where('status', 'released')->count(),
        ];

        $byService = $orders->groupBy('service.name')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'weight' => $group->sum('weight'),
                'revenue' => $group->sum('total_amount'),
            ]);

        return view('reports.index', compact('type', 'date', 'label', 'payments', 'expenses', 'orders', 'totals', 'byService'));
    }
}
