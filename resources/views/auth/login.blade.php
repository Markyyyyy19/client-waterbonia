<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in - {{ config('app.name') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-brand-panel d-none d-lg-flex">
            <div class="auth-brand-content">
                <span class="auth-logo-wrap"><img src="{{ asset('images/logo3d.png') }}" alt="{{ config('app.name') }} logo"></span>
                <p class="auth-eyebrow">Laundry operations, simplified</p>
                <h1>Keep every order moving from drop-off to pickup.</h1>
                <p class="auth-copy">Manage customers, orders, inventory, and daily shop activity in one clean workspace.</p>
                <div class="auth-feature-row">
                    <span><i class="bi bi-bag-check"></i> Order tracking</span>
                    <span><i class="bi bi-box-seam"></i> Inventory</span>
                    <span><i class="bi bi-graph-up-arrow"></i> Reports</span>
                </div>
            </div>
        </section>

        <section class="auth-form-panel">
            <div class="auth-card">
                <div class="d-lg-none auth-mobile-brand">
                    <span class="brand-mark"><img src="{{ asset('images/logo3d.png') }}" alt="{{ config('app.name') }} logo"></span>
                    <div><strong>{{ config('app.name') }}</strong><small>Laundry Shop</small></div>
                </div>

                <div class="mb-4">
                    <p class="auth-eyebrow text-primary mb-2">Welcome back</p>
                    <h2 class="mb-2">Sign in to your workspace</h2>
                    <p class="text-muted mb-0">Use your employee account to continue.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger app-alert align-items-start">
                        <i class="bi bi-exclamation-circle-fill mt-1"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Username or email</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person"></i>
                            <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your username or email" required autofocus autocomplete="username">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Keep me signed in</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Sign in <i class="bi bi-arrow-right ms-1"></i></button>
                </form>

                <div class="auth-divider"><span>Customer</span></div>
                <a href="{{ route('track.index') }}" class="btn btn-soft w-100">
                    <i class="bi bi-search me-2"></i>Track a laundry order
                </a>
            </div>
        </section>
    </main>
</body>
</html>
