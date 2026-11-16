<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Services\CustomerNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with(['customer', 'service', 'payments'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        $services = Service::where('is_active', true)->get();
        $customers = Customer::latest()->take(200)->get();

        return view('orders.create', compact('services', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')],
            'new_customer' => ['required_without:customer_id', 'array'],
            'new_customer.first_name' => ['required_without:customer_id', 'string', 'max:100'],
            'new_customer.last_name' => ['required_without:customer_id', 'string', 'max:100'],
            'new_customer.phone' => ['required_without:customer_id', 'string', 'max:20'],
            'new_customer.email' => ['nullable', 'email', 'max:150'],
            'new_customer.address' => ['nullable', 'string', 'max:255'],
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')],
            'weight' => ['required', 'numeric', 'min:0.1', 'max:500'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $customerId = $data['customer_id'];

            if (! $customerId) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $data['new_customer']['phone']],
                    [
                        'first_name' => $data['new_customer']['first_name'],
                        'last_name' => $data['new_customer']['last_name'],
                        'email' => $data['new_customer']['email'] ?? null,
                        'address' => $data['new_customer']['address'] ?? null,
                    ],
                );
                $customerId = $customer->id;
            }

            $service = Service::findOrFail($data['service_id']);
            $discount = (float) ($data['discount'] ?? 0);
            $subtotal = round((float) $data['weight'] * (float) $service->price_per_kg, 2);
            $total = max(0, round($subtotal - $discount, 2));

            return Order::create([
                'customer_id' => $customerId,
                'service_id' => $service->id,
                'weight' => (float) $data['weight'],
                'price_per_kg' => $service->price_per_kg,
                'total_amount' => $total,
                'discount' => $discount,
                'status' => 'received',
                'received_at' => now(),
                'expected_pickup_at' => now()->addDays((int) $service->estimated_days),
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('orders.show', $order)
            ->with('success', "Order {$order->order_number} created. Total: PHP ".number_format($order->total_amount, 2));
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'service', 'payments.receiver', 'creator', 'notifications']);

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
        ]);

        $newStatus = $request->string('status')->toString();

        if ($newStatus === $order->status) {
            return back();
        }

        $order->status = $newStatus;

        if ($newStatus === 'released') {
            $order->released_at = now();
        }

        $order->save();

        if ($newStatus === 'ready_for_pickup' && ! $order->notified) {
            app(CustomerNotifier::class)->notifyReadyForPickup($order);
            $order->update(['notified' => true]);
        }

        return back()->with('success', "Order {$order->order_number} moved to ".$order->status_label.'.');
    }
}
