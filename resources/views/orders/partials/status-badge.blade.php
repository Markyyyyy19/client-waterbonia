@php
    $colors = [
        'received' => 'text-bg-secondary',
        'washing' => 'text-bg-primary',
        'drying' => 'text-bg-info',
        'ironing' => 'text-bg-warning',
        'ready_for_pickup' => 'text-bg-success',
        'released' => 'text-bg-dark',
    ];
@endphp
<span class="badge {{ $colors[$order->status] ?? 'text-bg-light' }} status-badge">
    {{ $order->status_label }}
</span>
