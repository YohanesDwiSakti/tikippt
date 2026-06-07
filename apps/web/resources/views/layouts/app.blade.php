@php
    $locale = session('locale', 'id');
    $nextLocale = $locale === 'id' ? 'en' : 'id';
    $nextLanguageLabel = $locale === 'id' ? 'English' : 'Indonesia';
    app()->setLocale($locale);
@endphp
<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tracking') | FINPROPPT</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <header class="site-header">
        <nav class="nav-shell" aria-label="{{ __('messages.nav.main') }}">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark" aria-hidden="true">TKI</span>
                <span>
                    FINPROPPT
                    <small>TIKI Denpasar</small>
                </span>
            </a>
            <div class="nav-primary" aria-label="{{ __('messages.nav.public') }}">
                <a class="nav-link" href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>{{ __('messages.nav.home') }}</a>
                <a class="nav-link" href="{{ route('tracking') }}" @if(request()->routeIs('tracking')) aria-current="page" @endif>{{ __('messages.nav.services') }}</a>
                <a class="nav-link" href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif>{{ __('messages.nav.about') }}</a>
            </div>
            <div class="nav-account" aria-label="{{ __('messages.nav.account') }}">
                <a class="language-button" href="{{ route('language.switch', ['locale' => $nextLocale]) }}" aria-label="{{ __('messages.nav.language') }}: {{ $nextLanguageLabel }}" title="{{ $nextLanguageLabel }}">
                    <svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18">
                        <path d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z" fill="none" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M3.8 9h16.4M3.8 15h16.4M12 3c2 2.4 3 5.4 3 9s-1 6.6-3 9M12 3c-2 2.4-3 5.4-3 9s1 6.6 3 9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
                    </svg>
                    <span>{{ strtoupper($nextLocale) }}</span>
                </a>
                <a class="nav-link" href="{{ route('support') }}" @if(request()->routeIs('support')) aria-current="page" @endif>{{ __('messages.nav.support') }}</a>
                @if(session('auth_role') === 'admin')
                    <a class="nav-link" href="{{ route('admin.dashboard') }}" @if(request()->is('admin*')) aria-current="page" @endif>{{ __('messages.nav.dashboard') }}</a>
                @elseif(session('auth_role') === 'driver')
                    <a class="nav-link" href="{{ route('driver.index') }}" @if(request()->is('driver*')) aria-current="page" @endif>{{ __('messages.nav.dashboard') }}</a>
                @else
                    <a class="nav-link" href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>{{ __('messages.nav.login') }}</a>
                @endif
                @if(session('auth_role'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="nav-link nav-button" type="submit">{{ __('messages.nav.logout') }}</button>
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
                <span>{{ __('messages.footer.description') }}</span>
            </div>
            <div class="footer-column">
                <span class="footer-title">{{ __('messages.footer.services') }}</span>
                <a href="{{ route('tracking', ['tab' => 'resi']) }}">{{ __('messages.footer.tracking') }}</a>
                <a href="{{ route('tracking', ['tab' => 'harga']) }}">{{ __('messages.footer.price') }}</a>
                <a href="{{ route('tracking', ['tab' => 'lokasi']) }}">{{ __('messages.footer.location') }}</a>
            </div>
            <div class="footer-column">
                <span class="footer-title">{{ __('messages.footer.access') }}</span>
                @if(session('auth_role') === 'admin')
                    <a href="{{ route('admin.dashboard') }}">{{ __('messages.nav.dashboard') }}</a>
                    <a href="{{ route('admin.assignments') }}">{{ __('messages.footer.assign_driver') }}</a>
                @elseif(session('auth_role') === 'driver')
                    <a href="{{ route('driver.index') }}">{{ __('messages.nav.dashboard') }}</a>
                    <a href="{{ route('driver.index') }}">{{ __('messages.footer.driver_packages') }}</a>
                @else
                    <a href="{{ route('login') }}">{{ __('messages.footer.internal_login') }}</a>
                    <a href="{{ route('tracking') }}">{{ __('messages.footer.public_tracking') }}</a>
                @endif
            </div>
            <div class="footer-column">
                <span class="footer-title">{{ __('messages.footer.contact') }}</span>
                <a href="{{ route('support') }}">{{ __('messages.nav.support') }}</a>
                <a href="{{ route('about') }}">{{ __('messages.nav.about') }}</a>
                <a href="https://instagram.com/tikidenpasar">@tikidenpasar</a>
                <a href="https://wa.me/6281234567801">WhatsApp Hub</a>
                <span>Hub Denpasar</span>
                <span>{{ __('messages.footer.hours') }}</span>
            </div>
        </div>
    </footer>
</body>
</html>
