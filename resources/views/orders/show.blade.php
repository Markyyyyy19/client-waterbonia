@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4 no-print">
        <div>
            <h4 class="mb-1">{{ $order->order_number }}</h4>
            <small class="text-muted">
                Received {{ $order->received_at?->format('M j, Y g:i A') }} by {{ $order->creator?->name ?? '—' }}
            </small>
        </div>
        <div class="d-flex gap-2">
            @if ($order->payments->isNotEmpty())
                <a href="{{ route('receipt.show', $order) }}" class="btn btn-outline-primary">
                    <i class="bi bi-receipt me-1"></i>View Receipt
                </a>
            @endif
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Back to Orders</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Order Details</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Customer</div>
                            <div>{{ $order->customer->full_name }}</div>
                            <div class="small">{{ $order->customer->phone }}</div>
                            @if ($order->customer->email)
                                <div class="small">{{ $order->customer->email }}</div>
                            @endif
                            @if ($order->customer->address)
                                <div class="small">{{ $order->customer->address }}</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Service</div>
                            <div>{{ $order->service->name }}</div>
                            <div class="small">&#8369;{{ number_format($order->price_per_kg, 2) }}/kg &times; {{ $order->weight }} kg</div>
                            <div class="small">Expected pickup: {{ $order->expected_pickup_at?->format('M j, Y') }}</div>
                        </div>
                        @if ($order->notes)
                            <div class="col-12">
                                <div class="small text-muted">Notes</div>
                                <div>{{ $order->notes }}</div>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-4">
                            <div class="small text-muted">Subtotal</div>
                            <div>&#8369;{{ number_format($order->weight * $order->price_per_kg, 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Discount</div>
                            <div>- &#8369;{{ number_format($order->discount, 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Total</div>
                            <h5>&#8369;{{ number_format($order->total_amount, 2) }}</h5>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="small text-muted">Amount Paid</div>
                            <div class="text-success">&#8369;{{ number_format($order->amount_paid, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Balance</div>
                            @if ($order->balance > 0)
                                <div class="text-danger">&#8369;{{ number_format($order->balance, 2) }}</div>
                            @else
                                <div class="text-success">Fully Paid</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Payment History</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Tendered</th>
                                <th>Change</th>
                                <th>Received By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->payments as $payment)
                                <tr>
                                    <td>{{ $payment->paid_at->format('M j, Y g:i A') }}</td>
                                    <td>{{ strtoupper($payment->method) }}</td>
                                    <td>&#8369;{{ number_format($payment->amount, 2) }}</td>
                                    <td>&#8369;{{ number_format($payment->amount_tendered, 2) }}</td>
                                    <td>&#8369;{{ number_format($payment->change_due, 2) }}</td>
                                    <td>{{ $payment->receiver?->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No payments recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($order->notifications->isNotEmpty())
                <div class="card">
                    <div class="card-header bg-white"><strong>Pickup Notifications</strong></div>
                    <ul class="list-group list-group-flush">
                        @foreach ($order->notifications as $notification)
                            <li class="list-group-item small">
                                <span class="badge text-bg-info text-uppercase">{{ $notification->channel }}</span>
                                sent to {{ $notification->recipient }} at {{ $notification->created_at->format('M j, Y g:i A') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card mb-3 no-print">
                <div class="card-header bg-white"><strong>Order Status</strong></div>
                <div class="card-body">
                    <div class="order-timeline mb-4">
                        @include('orders.partials.status-timeline', ['order' => $order])
                    </div>

                    @if ($order->status !== 'released')
                        <form method="POST" action="{{ route('orders.status', $order) }}" class="d-flex gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select">
                                @foreach (\App\Models\Order::STATUSES as $value => $label)
                                    <option value="{{ $value }}" @selected($value === $order->status)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary text-nowrap">Update</button>
                        </form>
                        <small class="text-muted d-block mt-2">
                            Moving the status to <strong>Ready for Pickup</strong> automatically sends an SMS/email
                            notification to the customer.
                        </small>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-1"></i>
                            Released {{ $order->released_at?->format('M j, Y g:i A') }}
                        </div>
                    @endif
                </div>
            </div>

            @if ($order->balance > 0)
                <div class="card">
                    <div class="card-header bg-white"><strong>Record Payment</strong></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('payments.store', $order) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Amount to Pay (&#8369;)</label>
                                <input type="number" name="amount" class="form-control" step="0.01"
                                       min="0.01" max="{{ $order->balance }}" value="{{ $order->balance }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount Tendered (&#8369;)</label>
                                <input type="number" name="amount_tendered" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="method" class="form-select">
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="maya">Maya</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-cash me-1"></i>Record Payment
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
