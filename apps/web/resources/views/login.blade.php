@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <section class="page-shell section" style="max-width: 720px;">
        <p class="eyebrow">Akses internal</p>
        <h1 style="font-size: 48px;">Login admin atau driver</h1>
        <p class="lead">Masuk sesuai role untuk membuka dashboard internal.</p>

        @if(session('auth_role'))
            <div class="notice notice-success">
                Anda sedang login sebagai {{ session('auth_name') }}.
            </div>
        @endif

        @if(session('auth_notice'))
            <div class="notice">
                {{ session('auth_notice') }}
            </div>
        @endif

        @if($errors->any())
            <div class="notice notice-danger">
                Email, password, dan role wajib diisi dengan benar.
            </div>
        @endif

        <form class="panel form-grid" method="POST" action="{{ route('login.submit') }}">
            @csrf
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            <div class="field full">
                <label>Email</label>
                <input class="input" name="email" type="email" value="{{ old('email', 'admin@tiki.test') }}" required>
            </div>
            <div class="field full">
                <label>Password</label>
                <input class="input" name="password" type="password" value="admin123" required>
            </div>
            <div class="field full">
                <label>Role</label>
                <select class="select" name="role" required>
                    <option value="admin" @selected(old('role', 'admin') === 'admin')>Admin</option>
                    <option value="driver" @selected(old('role') === 'driver')>Driver</option>
                </select>
                <p class="helper">Login ini menyimpan session frontend. Integrasi backend auth dapat memakai endpoint Go yang sudah dibuat.</p>
            </div>
            <div class="full" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button class="button button-primary" type="submit">Masuk</button>
                @if(session('auth_role') === 'admin')
                    <a class="button button-secondary" href="{{ route('admin.dashboard') }}">Buka Admin</a>
                @elseif(session('auth_role') === 'driver')
                    <a class="button button-secondary" href="{{ route('driver.index') }}">Buka Driver</a>
                @endif
            </div>
        </form>
    </section>
@endsection
