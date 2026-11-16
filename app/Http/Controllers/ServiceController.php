<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::withCount('orders')->get();

        return view('services.index', compact('services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'estimated_days' => ['required', 'integer', 'min:1', 'max:30'],
            'description' => ['nullable', 'string'],
        ]);

        Service::create($data + ['is_active' => true]);

        return redirect()->route('services.index')->with('success', 'Service added successfully.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'price_per_kg' => ['required', 'numeric', 'min:0'],
            'estimated_days' => ['required', 'integer', 'min:1', 'max:30'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $service->update($data);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }
}
