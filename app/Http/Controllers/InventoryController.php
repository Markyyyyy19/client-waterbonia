<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $items = InventoryItem::withSum(['movements as total_in' => fn ($q) => $q->where('type', 'stock_in')], 'quantity')
            ->withSum(['movements as total_out' => fn ($q) => $q->where('type', 'stock_out')], 'quantity')
            ->latest()
            ->get();

        $movements = InventoryMovement::with(['item', 'user'])->latest()->take(30)->get();

        $lowStockCount = $items->filter->is_low_stock->count();

        return view('inventory.index', compact('items', 'movements', 'lowStockCount'));
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:60'],
            'unit' => ['required', 'string', 'max:20'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $item = InventoryItem::create($data);

        if ((float) $data['quantity'] > 0) {
            $item->movements()->create([
                'type' => 'stock_in',
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'] ?? null,
                'notes' => 'Initial stock',
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Supply added to inventory.');
    }

    public function updateItem(Request $request, InventoryItem $item): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:60'],
            'unit' => ['required', 'string', 'max:20'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $item->update($data);

        return redirect()->route('inventory.index')->with('success', 'Supply updated.');
    }

    public function storeMovement(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'inventory_item_id' => ['required', Rule::exists('inventory_items', 'id')],
            'type' => ['required', Rule::in(['stock_in', 'stock_out'])],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $item = InventoryItem::findOrFail($data['inventory_item_id']);

        DB::transaction(function () use ($item, $data) {
            $quantity = (float) $data['quantity'];

            if ($data['type'] === 'stock_out') {
                if ($quantity > (float) $item->quantity) {
                    abort(422, 'Not enough stock available for '.$item->name.'.');
                }
                $item->decrement('quantity', $quantity);
            } else {
                $item->increment('quantity', $quantity);
                if (! empty($data['unit_cost'])) {
                    $item->update(['unit_cost' => $data['unit_cost']]);
                }
            }

            $item->movements()->create([
                'type' => $data['type'],
                'quantity' => $quantity,
                'unit_cost' => $data['unit_cost'] ?? null,
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('inventory.index')->with('success', 'Stock movement recorded.');
    }
}
