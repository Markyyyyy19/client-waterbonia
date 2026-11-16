@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <h4 class="mb-1">Laundry Orders</h4>
            <small class="text-muted">{{ $orders->total() }} orders</small>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New Order
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search order #, customer or phone..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (\App\Models\Order::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i> Filter</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Weight</th>
                            <th>Total</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td><a href="{{ route('orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                <td>
                                    {{ $order->customer->full_name }}
                                    <div class="small text-muted">{{ $order->customer->phone }}</div>
                                </td>
                                <td>{{ $order->service->name }}</td>
                                <td>{{ $order->weight }} kg</td>
                                <td>&#8369;{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    @if ($order->balance > 0)
                                        <span class="text-danger">&#8369;{{ number_format($order->balance, 2) }}</span>
                                    @else
                                        <span class="text-success">Paid</span>
                                    @endif
                                </td>
                                <td>@include('orders.partials.status-badge', ['order' => $order])</td>
                                <td>{{ $order->created_at->format('M j, Y g:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        </div>
    </div>
@endsection
