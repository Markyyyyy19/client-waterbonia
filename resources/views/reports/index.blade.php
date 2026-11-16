@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h4 class="mb-1">Sales Reports</h4>
            <small class="text-muted">{{ $label }}</small>
        </div>
        <button class="btn btn-primary btn-print"><i class="bi bi-printer me-1"></i>Print Report</button>
    </div>

    <div class="card mb-4 no-print">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select name="type" class="form-select">
                        <option value="daily" @selected($type === 'daily')>Daily</option>
                        <option value="weekly" @selected($type === 'weekly')>Weekly</option>
                        <option value="monthly" @selected($type === 'monthly')>Monthly</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" type="submit">Generate</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="text-center mb-4">
                <h5 class="mb-0">Waterbonia Laundry Shop</h5>
                <div>{{ $label }}</div>
                <small class="text-muted">Generated {{ now()->format('M j, Y g:i A') }}</small>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Total Sales</div>
                        <h5 class="mb-0">&#8369;{{ number_format($totals['sales'], 2) }}</h5>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Total Expenses</div>
                        <h5 class="mb-0">&#8369;{{ number_format($totals['expenses'], 2) }}</h5>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Net Income</div>
                        <h5 class="mb-0 {{ $totals['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                            &#8369;{{ number_format($totals['net'], 2) }}
                        </h5>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted small">Orders / Transactions</div>
                        <h5 class="mb-0">{{ $totals['orders'] }} / {{ $totals['transactions'] }}</h5>
                    </div>
                </div>
            </div>

            <h6 class="text-uppercase text-muted">Sales by Service</h6>
            <div class="table-responsive mb-4">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Orders</th>
                            <th>Total Weight</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byService as $name => $data)
                            <tr>
                                <td>{{ $name }}</td>
                                <td>{{ $data['count'] }}</td>
                                <td>{{ $data['weight'] }} kg</td>
                                <td>&#8369;{{ number_format($data['revenue'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No orders in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h6 class="text-uppercase text-muted">Payment Transactions</h6>
            <div class="table-responsive mb-4">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Received By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>{{ $payment->paid_at->format('M j, Y g:i A') }}</td>
                                <td>{{ $payment->order->order_number }}</td>
                                <td>{{ $payment->order->customer->full_name }}</td>
                                <td>{{ strtoupper($payment->method) }}</td>
                                <td>&#8369;{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->receiver?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No payments in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h6 class="text-uppercase text-muted">Expenses</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td>{{ $expense->expense_date->format('M j, Y') }}</td>
                                <td>{{ $expense->description }}</td>
                                <td>{{ $expense->category ?? '—' }}</td>
                                <td>&#8369;{{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No expenses in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card no-print">
        <div class="card-header bg-white"><strong>Record Expense</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('expenses.store') }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="e.g., Detergent restock" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Amount (&#8369;)</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" placeholder="Supplies, Utilities...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="expense_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
@endsection
