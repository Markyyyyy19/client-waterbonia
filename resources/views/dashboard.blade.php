@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <p class="text-primary fw-semibold small text-uppercase mb-1">Overview</p>
            <h4 class="mb-1">Dashboard</h4>
            <small class="text-muted">{{ now()->format('l, F j, Y') }} &middot; A quick look at today's shop activity</small>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New order
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100" style="--stat-wash: rgba(37,99,235,.08);">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-cash-stack"></i></div>
                    <div class="min-w-0">
                        <div class="stat-label">Sales today</div>
                        <div class="stat-value">&#8369;{{ number_format($stats['sales_today'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100" style="--stat-wash: rgba(25,135,84,.08);">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-bag-check"></i></div>
                    <div>
                        <div class="stat-label">Orders today</div>
                        <div class="stat-value">{{ $stats['orders_today'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100" style="--stat-wash: rgba(245,158,11,.09);">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-bell"></i></div>
                    <div>
                        <div class="stat-label">Ready for pickup</div>
                        <div class="stat-value">{{ $stats['ready_for_pickup'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100" style="--stat-wash: rgba(220,53,69,.07);">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-wallet2"></i></div>
                    <div class="min-w-0">
                        <div class="stat-label">Unpaid balance</div>
                        <div class="stat-value">&#8369;{{ number_format($stats['pending_payments'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 align-items-stretch">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <strong>Recent orders</strong>
                        <div class="small text-muted">Latest laundry activity</div>
                    </div>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td><a href="{{ route('orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                    <td>{{ $order->customer->full_name }}</td>
                                    <td>{{ $order->service->name }}</td>
                                    <td class="fw-semibold">&#8369;{{ number_format($order->total_amount, 2) }}</td>
                                    <td>@include('orders.partials.status-badge', ['order' => $order])</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-5">No orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="d-flex flex-column gap-3 h-100">
                @if ($lowStockItems->isNotEmpty())
                    <div class="card">
                        <div class="card-header d-flex align-items-center gap-2">
                            <span class="stat-icon bg-warning-subtle text-warning" style="width:34px;height:34px;border-radius:9px;font-size:.9rem;"><i class="bi bi-exclamation-triangle"></i></span>
                            <div>
                                <strong>Low stock</strong>
                                <div class="small text-muted">Supplies that need attention</div>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            @foreach ($lowStockItems as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                    <div class="min-w-0">
                                        <div class="fw-semibold">{{ $item->name }}</div>
                                        <small class="text-muted">{{ $item->quantity }} {{ $item->unit }} left &middot; reorder at {{ $item->reorder_level }}</small>
                                    </div>
                                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary">Restock</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card flex-grow-1">
                    <div class="card-header">
                        <strong>7-day sales</strong>
                        <div class="small text-muted">Daily sales performance</div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-3">
                            @foreach ($salesLast7Days as $day => $amount)
                                <div>
                                    <div class="d-flex justify-content-between align-items-center small mb-2">
                                        <span class="text-muted">{{ $day }}</span>
                                        <span class="fw-semibold">&#8369;{{ number_format($amount, 2) }}</span>
                                    </div>
                                    <div class="progress" style="height: 7px;">
                                        @php
                                            $max = $salesLast7Days->max() ?: 1;
                                            $width = $max > 0 ? ($amount / $max) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar" role="progressbar" style="width: {{ $width }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
