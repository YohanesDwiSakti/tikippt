@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <section class="page-shell section" style="max-width: 720px;">
        <p class="eyebrow">Akses internal</p>
        <h1 style="font-size: 48px;">Login admin atau driver</h1>
        <p class="lead">Form ini masih frontend mock. Pilih role untuk melihat halaman yang akan dipakai setelah auth backend tersedia.</p>

        <form class="panel form-grid">
            <div class="field full">
                <label>Email</label>
                <input class="input" type="email" value="admin@tiki.test">
            </div>
            <div class="field full">
                <label>Password</label>
                <input class="input" type="password" value="admin123">
            </div>
            <div class="field full">
                <label>Role</label>
                <select class="select">
                    <option>Admin</option>
                    <option>Driver</option>
                </select>
                <p class="helper">Belum mengirim data ke backend.</p>
            </div>
            <div class="full" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a class="button button-primary" href="{{ route('admin.dashboard') }}">Masuk Admin</a>
                <a class="button button-secondary" href="{{ route('driver.index') }}">Masuk Driver</a>
            </div>
        </form>
    </section>
@endsection
