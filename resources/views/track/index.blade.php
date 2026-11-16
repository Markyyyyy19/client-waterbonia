<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Laundry - {{ config('app.name') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="track-page">
    <header class="track-header">
        <a href="{{ route('track.index') }}" class="track-brand">
            <span class="brand-mark brand-mark-sm"><img src="{{ asset('images/logo3d.png') }}" alt="{{ config('app.name') }} logo"></span>
            <span><strong>{{ config('app.name') }}</strong><small>Laundry Shop</small></span>
        </a>
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-lock me-1"></i>Staff sign in</a>
    </header>

    <main class="track-main">
        <div class="track-hero">
            <span class="brand-mark"><img src="{{ asset('images/logo3d.png') }}" alt=""></span>
            <p class="auth-eyebrow text-primary mb-2">Order tracking</p>
            <h1>Where is my laundry?</h1>
            <p>Enter your order number or the phone number used at drop-off to see the latest status.</p>
        </div>

        <div class="card track-card">
            <div class="card-body">
                <form method="GET" action="{{ route('track.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="track-search-label form-label" for="order_number">Order number</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-receipt"></i>
                                <input id="order_number" type="text" name="order_number" class="form-control" placeholder="e.g. WB-20260818-ED45" value="{{ request('order_number') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="track-search-label form-label" for="phone">Phone number</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-telephone"></i>
                                <input id="phone" type="text" name="phone" class="form-control" placeholder="09XX XXX XXXX" value="{{ request('phone') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" type="submit"><i class="bi bi-search me-1"></i>Track order</button>
                        </div>
                    </div>
                </form>

                @if ($order)
                    <section class="track-result">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
                            <div>
                                <div class="small text-muted mb-1">Order number</div>
                                <h5 class="mb-0">{{ $order->order_number }}</h5>
                            </div>
                            @include('orders.partials.status-badge', ['order' => $order])
                        </div>

                        <div class="card bg-light shadow-none mb-4" style="border-color:#edf1f6;">
                            <div class="card-body py-3">
                                @include('orders.partials.status-timeline', ['order' => $order])
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="small text-muted">Customer</div>
                                <div class="fw-semibold mt-1">{{ $order->customer->full_name }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small text-muted">Service</div>
                                <div class="fw-semibold mt-1">{{ $order->service->name }}</div>
                                <small class="text-muted">{{ $order->weight }} kg</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small text-muted">Received</div>
                                <div class="fw-semibold mt-1">{{ $order->received_at?->format('M j, Y') }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small text-muted">Expected pickup</div>
                                <div class="fw-semibold mt-1">{{ $order->expected_pickup_at?->format('M j, Y') }}</div>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center rounded-3 bg-light px-3 py-3">
                                    <span class="text-muted">Balance</span>
                                    <strong>{{ $order->balance > 0 ? 'PHP ' . number_format($order->balance, 2) : 'Fully Paid' }}</strong>
                                </div>
                            </div>
                        </div>
                    </section>
                @elseif ($orders->isNotEmpty())
                    <section class="track-result">
                        <h6 class="mb-3">Matching orders</h6>
                        <div class="list-group rounded-3 overflow-hidden">
                            @foreach ($orders as $o)
                                <a href="{{ route('track.index', ['order_number' => $o->order_number]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $o->order_number }}</div>
                                        <small class="text-muted">{{ $o->service->name }} &middot; {{ $o->created_at->format('M j, Y') }}</small>
                                    </div>
                                    @include('orders.partials.status-badge', ['order' => $o])
                                </a>
                            @endforeach
                        </div>
                    </section>
                @elseif (request()->filled('order_number') || request()->filled('phone'))
                    <section class="track-result">
                        <div class="alert alert-warning app-alert mb-0">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>No matching order was found. Check the order number or phone number and try again.</div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
