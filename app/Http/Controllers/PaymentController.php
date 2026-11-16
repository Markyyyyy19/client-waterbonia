<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.max(1, $order->balance)],
            'amount_tendered' => ['nullable', 'numeric', 'min:0'],
            'method' => ['required', 'in:cash,gcash,maya'],
        ]);

        $amount = (float) $request->input('amount');
        $tendered = $request->filled('amount_tendered') ? (float) $request->input('amount_tendered') : $amount;
        $change = $tendered >= $amount ? round($tendered - $amount, 2) : 0;

        $order->payments()->create([
            'amount' => $amount,
            'amount_tendered' => $tendered,
            'change_due' => $change,
            'method' => $request->string('method')->toString(),
            'paid_at' => now(),
            'received_by' => auth()->id(),
        ]);

        return redirect()->route('receipt.show', $order)
            ->with('success', 'Payment of PHP '.number_format($amount, 2).' recorded.');
    }

    public function receipt(Order $order): View
    {
        $order->load(['customer', 'service', 'payments.receiver']);

        return view('receipts.show', compact('order'));
    }
}
