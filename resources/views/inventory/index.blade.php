@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <h4 class="mb-1">Inventory</h4>
            <small class="text-muted">
                {{ $items->count() }} supplies tracked
                @if ($lowStockCount)
                    &middot; <span class="text-warning">{{ $lowStockCount }} low stock</span>
                @endif
            </small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#movementModal">
                <i class="bi bi-arrow-left-right me-1"></i>Record Stock In/Out
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="bi bi-plus-lg me-1"></i>Add Supply
            </button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Supply</th>
                        <th>Category</th>
                        <th>On Hand</th>
                        <th>Reorder Level</th>
                        <th>Unit Cost</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td>{{ $item->category }}</td>
                            <td>{{ $item->quantity }} {{ $item->unit }}</td>
                            <td>{{ $item->reorder_level }} {{ $item->unit }}</td>
                            <td>&#8369;{{ $item->unit_cost ? number_format($item->unit_cost, 2) : '—' }}</td>
                            <td>
                                @if ($item->is_low_stock)
                                    <span class="badge text-bg-warning status-badge">Low Stock</span>
                                @else
                                    <span class="badge text-bg-success status-badge">In Stock</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editItemModal{{ $item->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('inventory.items.update', $item) }}" class="modal-content">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Supply</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label">Category</label>
                                                <input type="text" name="category" class="form-control" value="{{ $item->category }}" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Unit</label>
                                                <input type="text" name="unit" class="form-control" value="{{ $item->unit }}" required>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-1">
                                            <div class="col-6">
                                                <label class="form-label">Reorder Level</label>
                                                <input type="number" name="reorder_level" class="form-control" step="0.01" min="0"
                                                       value="{{ $item->reorder_level }}" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Unit Cost (&#8369;)</label>
                                                <input type="number" name="unit_cost" class="form-control" step="0.01" min="0"
                                                       value="{{ $item->unit_cost }}">
                                            </div>
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

    <div class="card">
        <div class="card-header bg-white"><strong>Recent Stock Movements</strong></div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Supply</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Notes</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('M j, Y g:i A') }}</td>
                            <td>{{ $movement->item->name }}</td>
                            <td>
                                <span class="badge {{ $movement->type === 'stock_in' ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ $movement->type === 'stock_in' ? 'IN' : 'OUT' }}
                                </span>
                            </td>
                            <td>{{ $movement->quantity }} {{ $movement->item->unit }}</td>
                            <td>{{ $movement->notes ?? '—' }}</td>
                            <td>{{ $movement->user?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No stock movements yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('inventory.items.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Supply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="pcs, kg, gallon..." required>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-6">
                            <label class="form-label">Initial Quantity</label>
                            <input type="number" name="quantity" class="form-control" step="0.01" min="0" value="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Reorder Level</label>
                            <input type="number" name="reorder_level" class="form-control" step="0.01" min="0" value="5" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Unit Cost (&#8369;)</label>
                        <input type="number" name="unit_cost" class="form-control" step="0.01" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Supply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="movementModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('inventory.movements.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Record Stock Movement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Supply</label>
                        <select name="inventory_item_id" class="form-select" required>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->quantity }} {{ $item->unit }} on hand)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="stock_in">Stock In (restock)</option>
                            <option value="stock_out">Stock Out (used)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit Cost (&#8369;, stock in only)</label>
                        <input type="number" name="unit_cost" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g., Used for today's washing">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Movement</button>
                </div>
            </form>
        </div>
    </div>
@endsection
