@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <h4 class="mb-1">{{ $customer->full_name }}</h4>
            <small class="text-muted">
                {{ $customer->phone }}
                @if ($customer->email)
                    &middot; {{ $customer->email }}
                @endif
                @if ($customer->address)
                    &middot; {{ $customer->address }}
                @endif
            </small>
        </div>
        <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New Order for this Customer
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-white"><strong>Order History</strong></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Service</th>
                        <th>Weight</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ $order->service->name }}</td>
                            <td>{{ $order->weight }} kg</td>
                            <td>&#8369;{{ number_format($order->total_amount, 2) }}</td>
                            <td>&#8369;{{ number_format($order->amount_paid, 2) }}</td>
                            <td>@include('orders.partials.status-badge', ['order' => $order])</td>
                            <td>{{ $order->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No orders yet for this customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="card-body">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
