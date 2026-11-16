@extends('layouts.app')

@section('content')
    <div class="page-header mb-4">
        <h4 class="mb-1">New Laundry Order</h4>
        <small class="text-muted">Record the customer's laundry details — the system computes the fee automatically.</small>
    </div>

    <div class="card" style="max-width: 780px;">
        <div class="card-body">
            <form method="POST" action="{{ route('orders.store') }}">
                @csrf
                <h6 class="text-uppercase text-muted mb-3">Customer</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Existing Customer</label>
                        <select name="customer_id" id="customer_id" class="form-select">
                            <option value="">— Select customer —</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id || request('customer_id') == $customer->id)>
                                    {{ $customer->full_name }} ({{ $customer->phone }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">...or fill in a new customer below.</small>
                    </div>
                </div>

                <div id="new-customer-fields">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="new_customer[first_name]" class="form-control" value="{{ old('new_customer.first_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="new_customer[last_name]" class="form-control" value="{{ old('new_customer.last_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="new_customer[phone]" class="form-control" value="{{ old('new_customer.phone') }}" placeholder="09XX XXX XXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <small class="text-muted">(for pickup notification)</small></label>
                            <input type="email" name="new_customer[email]" class="form-control" value="{{ old('new_customer.email') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="new_customer[address]" class="form-control" value="{{ old('new_customer.address') }}">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="text-uppercase text-muted mb-3">Laundry Details</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service <span class="text-danger">*</span></label>
                        <select name="service_id" id="service_id" class="form-select" required>
                            <option value="">— Select service —</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" data-price="{{ $service->price_per_kg }}" @selected(old('service_id') == $service->id)>
                                    {{ $service->name }} — &#8369;{{ number_format($service->price_per_kg, 2) }}/kg
                                    (ready in ~{{ $service->estimated_days }} day{{ $service->estimated_days > 1 ? 's' : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                        <input type="number" name="weight" id="weight" class="form-control" step="0.01" min="0.1"
                               value="{{ old('weight') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Discount (&#8369;)</label>
                        <input type="number" name="discount" id="discount" class="form-control" step="0.01" min="0"
                               value="{{ old('discount', 0) }}">
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <span>Estimated Total Fee:</span>
                            <h5 class="mb-0" id="total-display">&#8369;0.00</h5>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Special instructions, stains, delicate items...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Create Order
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function updateTotal() {
                const service = document.getElementById('service_id');
                const weight = document.getElementById('weight');
                const discount = document.getElementById('discount');
                const price = parseFloat(service.selectedOptions[0]?.dataset.price || 0);
                const w = parseFloat(weight.value || 0);
                const d = parseFloat(discount.value || 0);
                const total = Math.max(0, price * w - d);
                document.getElementById('total-display').textContent = '\u20B1' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            ['service_id', 'weight', 'discount'].forEach((id) => {
                document.getElementById(id).addEventListener('input', updateTotal);
                document.getElementById(id).addEventListener('change', updateTotal);
            });

            updateTotal();
        </script>
    @endpush
@endsection
