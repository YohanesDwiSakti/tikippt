@extends('layouts.app')

@section('title', 'Kirim Bukti')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Bukti sampai tujuan</p>
                <h1 style="font-size: 44px;">{{ $package['receipt'] }}</h1>
                <p>Upload bukti saat paket sudah benar sampai tujuan.</p>
            </div>
            <a class="button button-secondary" href="{{ route('driver.index') }}">Kembali</a>
        </div>

        <div class="grid-3" style="margin: 24px 0;">
            <article class="panel">
                <div class="meta-label">Tujuan</div>
                <div class="meta-value">{{ $package['destination'] }}</div>
            </article>
            <article class="panel">
                <div class="meta-label">Status</div>
                <div class="meta-value">{{ $package['status'] }}</div>
            </article>
            <article class="panel">
                <div class="meta-label">Lokasi terakhir</div>
                <div class="meta-value">{{ $package['latest_location'] }}</div>
            </article>
        </div>

        <article class="panel">
            <form class="form-grid">
                <div class="field full">
                    <label>Foto bukti</label>
                    <input class="input" type="file" accept="image/*">
                    <p class="helper">Wajib saat backend upload sudah aktif.</p>
                </div>
                <div class="field">
                    <label>Waktu sampai</label>
                    <input class="input" type="datetime-local">
                </div>
                <div class="field">
                    <label>Lokasi sampai</label>
                    <input class="input">
                </div>
                <div class="field">
                    <label>Latitude</label>
                    <input class="input">
                </div>
                <div class="field">
                    <label>Longitude</label>
                    <input class="input">
                </div>
                <div class="field full">
                    <label>Catatan</label>
                    <textarea class="textarea"></textarea>
                </div>
                <div class="full">
                    <button class="button button-primary" type="button">Kirim Bukti</button>
                </div>
            </form>
        </article>
    </section>
@endsection
