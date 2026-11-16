@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-4 no-print">
        <h4 class="mb-0">Official Receipt</h4>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-print"><i class="bi bi-printer me-1"></i>Print Receipt</button>
            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">Back to Order</a>
        </div>
    </div>

    <div class="card no-print" style="max-width: 400px;">
        <div class="card-body p-0">
            <div class="receipt p-4">
                <div class="receipt-header mb-3">
                    <div class="fs-5 fw-bold">WATERBONIA</div>
                    <div class="small">Laundry Shop</div>
                    <div class="small">8:00 AM - 8:00 PM Daily</div>
                    <div class="dashed my-3"></div>
                </div>

                <table class="w-100 small">
                    <tr><td>Receipt For</td><td class="text-end">{{ $order->order_number }}</td></tr>
                    <tr><td>Date</td><td class="text-end">{{ $order->created_at->format('M j, Y g:i A') }}</td></tr>
                    <tr><td>Customer</td><td class="text-end">{{ $order->customer->full_name }}</td></tr>
                    <tr><td>Contact</td><td class="text-end">{{ $order->customer->phone }}</td></tr>
                </table>

                <div class="dashed my-3"></div>

                <table class="w-100 small">
                    <tr><td>{{ $order->service->name }}</td><td class="text-end">{{ $order->weight }} kg</td></tr>
                    <tr><td>Rate</td><td class="text-end">&#8369;{{ number_format($order->price_per_kg, 2) }}/kg</td></tr>
                    <tr><td>Subtotal</td><td class="text-end">&#8369;{{ number_format($order->weight * $order->price_per_kg, 2) }}</td></tr>
                    <tr><td>Discount</td><td class="text-end">- &#8369;{{ number_format($order->discount, 2) }}</td></tr>
                    <tr class="fw-bold"><td>Total Amount</td><td class="text-end">&#8369;{{ number_format($order->total_amount, 2) }}</td></tr>
                </table>

                <div class="dashed my-3"></div>

                @foreach ($order->payments as $payment)
                    <table class="w-100 small">
                        <tr><td>{{ strtoupper($payment->method) }} Paid</td><td class="text-end">&#8369;{{ number_format($payment->amount, 2) }}</td></tr>
                        <tr><td>Amount Tendered</td><td class="text-end">&#8369;{{ number_format($payment->amount_tendered, 2) }}</td></tr>
                        <tr><td>Change</td><td class="text-end">&#8369;{{ number_format($payment->change_due, 2) }}</td></tr>
                    </table>
                    <div class="dashed my-3"></div>
                @endforeach

                <table class="w-100 small">
                    <tr class="fw-bold"><td>Balance</td><td class="text-end">&#8369;{{ number_format($order->balance, 2) }}</td></tr>
                </table>

                <div class="dashed my-3"></div>

                <div class="text-center small">
                    <div>Expected Pickup: {{ $order->expected_pickup_at?->format('M j, Y') }}</div>
                    <div>Order Status: {{ $order->status_label }}</div>
                    <div class="mt-3">Thank you for choosing Waterbonia!</div>
                </div>
            </div>
        </div>
    </div>
@endsection
