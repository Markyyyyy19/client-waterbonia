@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <h4 class="mb-1">Services &amp; Pricing</h4>
            <small class="text-muted">Manage laundry services and rates per kilogram</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="bi bi-plus-lg me-1"></i>Add Service
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Price per kg</th>
                        <th>Ready In</th>
                        <th>Orders</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $service->name }}</div>
                                @if ($service->description)
                                    <small class="text-muted">{{ $service->description }}</small>
                                @endif
                            </td>
                            <td>&#8369;{{ number_format($service->price_per_kg, 2) }}</td>
                            <td>{{ $service->estimated_days }} day{{ $service->estimated_days > 1 ? 's' : '' }}</td>
                            <td>{{ $service->orders_count }}</td>
                            <td>
                                <form method="POST" action="{{ route('services.update', $service) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ $service->name }}">
                                    <input type="hidden" name="price_per_kg" value="{{ $service->price_per_kg }}">
                                    <input type="hidden" name="estimated_days" value="{{ $service->estimated_days }}">
                                    <input type="hidden" name="description" value="{{ $service->description }}">
                                    <button type="submit" name="is_active" value="{{ $service->is_active ? 0 : 1 }}"
                                            class="badge border-0 {{ $service->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editServiceModal{{ $service->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="editServiceModal{{ $service->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('services.update', $service) }}" class="modal-content">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Service</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $service->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Price per kg (&#8369;)</label>
                                            <input type="number" name="price_per_kg" class="form-control" step="0.01" min="0"
                                                   value="{{ $service->price_per_kg }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Estimated Days</label>
                                            <input type="number" name="estimated_days" class="form-control" min="1"
                                                   value="{{ $service->estimated_days }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="2">{{ $service->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('services.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price per kg (&#8369;)</label>
                        <input type="number" name="price_per_kg" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estimated Days</label>
                        <input type="number" name="estimated_days" class="form-control" min="1" value="2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Service</button>
                </div>
            </form>
        </div>
    </div>
@endsection
