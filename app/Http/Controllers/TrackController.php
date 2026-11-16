<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackController extends Controller
{
    public function index(Request $request): View
    {
        $order = null;
        $orders = collect();

        if ($request->filled('order_number')) {
            $order = Order::with(['customer', 'service', 'payments'])
                ->where('order_number', 'like', '%' . $request->string('order_number') . '%')
                ->latest()
                ->first();
        } elseif ($request->filled('phone')) {
            $customer = Customer::where('phone', 'like', '%' . $request->string('phone') . '%')->first();

            if ($customer) {
                $orders = $customer->orders()
                    ->with(['service', 'payments'])
                    ->latest()
                    ->limit(10)
                    ->get();
            }
        }

        return view('track.index', compact('order', 'orders'));
    }
}
