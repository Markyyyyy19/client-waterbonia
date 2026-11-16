@php
    $currentIndex = array_search($order->status, array_keys(\App\Models\Order::STATUSES));
@endphp
<div class="order-timeline">
    @foreach (\App\Models\Order::STATUSES as $value => $label)
        @php
            $stepIndex = array_search($value, array_keys(\App\Models\Order::STATUSES));
        @endphp
        <div class="step {{ $stepIndex < $currentIndex ? 'done' : '' }} {{ $value === $order->status ? 'current' : '' }}">
            <span class="dot"></span>
            <span>{{ $label }}</span>
            @if ($value === $order->status && $order->status !== 'released')
                <i class="bi bi-arrow-right ms-auto small"></i>
            @endif
        </div>
    @endforeach
</div>
