@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <h4 class="mb-1">Customers</h4>
            <small class="text-muted">{{ $customers->total() }} registered customers</small>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>Add Customer
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search name, phone or email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Orders</th>
                            <th>Registered</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td><a href="{{ route('customers.show', $customer) }}">{{ $customer->full_name }}</a></td>
                                <td>
                                    <div>{{ $customer->phone }}</div>
                                    @if ($customer->email)
                                        <small class="text-muted">{{ $customer->email }}</small>
                                    @endif
                                </td>
                                <td>{{ $customer->address ?? '—' }}</td>
                                <td><span class="badge text-bg-light">{{ $customer->orders_count }}</span></td>
                                <td>{{ $customer->created_at->format('M j, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $customers->links() }}
        </div>
    </div>
@endsection
