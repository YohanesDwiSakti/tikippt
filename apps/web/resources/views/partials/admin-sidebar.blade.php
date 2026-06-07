<aside class="sidebar" aria-label="Navigasi admin">
    <a class="sidebar-link" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>Overview <span>›</span></a>
    <a class="sidebar-link" href="{{ route('admin.packages') }}" @if(request()->routeIs('admin.packages')) aria-current="page" @endif>Paket <span>›</span></a>
    <a class="sidebar-link" href="{{ route('admin.assignments') }}" @if(request()->routeIs('admin.assignments')) aria-current="page" @endif>Assign Driver <span>›</span></a>
    <a class="sidebar-link" href="{{ route('admin.proofs') }}" @if(request()->routeIs('admin.proofs')) aria-current="page" @endif>Bukti Delivery <span>›</span></a>
</aside>
