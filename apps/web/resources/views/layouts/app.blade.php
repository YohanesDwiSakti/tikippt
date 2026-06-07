<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tracking') | FINPROPPT</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <header class="site-header">
        <nav class="nav-shell" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark" aria-hidden="true">TKI</span>
                <span>
                    FINPROPPT
                    <small>TIKI Denpasar</small>
                </span>
            </a>
            <div class="nav-links">
                <a class="nav-link" href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
                <a class="nav-link" href="{{ route('tracking') }}" @if(request()->routeIs('tracking')) aria-current="page" @endif>Cek Resi</a>
                <a class="nav-link" href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Login</a>
                <a class="nav-link" href="{{ route('admin.dashboard') }}" @if(request()->is('admin*')) aria-current="page" @endif>Admin</a>
                <a class="nav-link" href="{{ route('driver.index') }}" @if(request()->is('driver*')) aria-current="page" @endif>Driver</a>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="page-shell footer-grid">
            <strong>FINPROPPT TIKI Denpasar</strong>
            <span>Tracking, assignment driver, dan bukti sampai tujuan.</span>
        </div>
    </footer>
</body>
</html>
