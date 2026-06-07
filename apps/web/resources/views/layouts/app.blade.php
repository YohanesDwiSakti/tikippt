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
            <div class="nav-primary" aria-label="Navigasi publik">
                <a class="nav-link" href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
                <a class="nav-link" href="{{ route('tracking') }}" @if(request()->routeIs('tracking')) aria-current="page" @endif>Cek Resi</a>
            </div>
            <div class="nav-account" aria-label="Akses akun">
                @if(session('auth_role') === 'admin')
                    <a class="nav-link" href="{{ route('admin.dashboard') }}" @if(request()->is('admin*')) aria-current="page" @endif>Dashboard</a>
                @elseif(session('auth_role') === 'driver')
                    <a class="nav-link" href="{{ route('driver.index') }}" @if(request()->is('driver*')) aria-current="page" @endif>Dashboard</a>
                @else
                    <a class="nav-link" href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Login</a>
                @endif
                @if(session('auth_role'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="nav-link nav-button" type="submit">Logout</button>
                    </form>
                @endif
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="page-shell footer-grid">
            <div class="footer-brand">
                <strong>FINPROPPT TIKI Denpasar</strong>
                <span>Tracking resi, pembagian paket driver, dan bukti sampai tujuan.</span>
            </div>
            <div class="footer-column">
                <span class="footer-title">Layanan</span>
                <a href="{{ route('tracking') }}">Cek resi</a>
                <a href="{{ route('home') }}">Alur pengiriman</a>
            </div>
            <div class="footer-column">
                <span class="footer-title">Akses</span>
                @if(session('auth_role') === 'admin')
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.assignments') }}">Assign driver</a>
                @elseif(session('auth_role') === 'driver')
                    <a href="{{ route('driver.index') }}">Dashboard</a>
                    <a href="{{ route('driver.index') }}">Paket driver</a>
                @else
                    <a href="{{ route('login') }}">Login internal</a>
                    <a href="{{ route('tracking') }}">Tracking publik</a>
                @endif
            </div>
            <div class="footer-column">
                <span class="footer-title">Kontak</span>
                <span>Hub Denpasar</span>
                <span>Operasional 08.00-17.00 WITA</span>
            </div>
        </div>
    </footer>
</body>
</html>
