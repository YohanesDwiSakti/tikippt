@extends('layouts.app')

@section('title', 'Support')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Support</p>
                <h1 style="font-size: 48px;">Bantuan operasional paket</h1>
                <p class="lead">Gunakan halaman ini untuk melihat jalur bantuan customer, admin hub, dan driver.</p>
            </div>
        </div>

        <div class="grid-3">
            <article class="panel">
                <span class="badge badge-brand">Customer</span>
                <h3 style="margin-top: 16px;">Bantuan cek resi</h3>
                <p class="helper">Pastikan nomor resi benar, lalu gunakan halaman Cek Resi untuk melihat timeline terbaru.</p>
                <a class="button button-secondary" href="{{ route('tracking') }}">Cek Resi</a>
            </article>
            <article class="panel">
                <span class="badge badge-brand">Admin</span>
                <h3 style="margin-top: 16px;">Status paket</h3>
                <p class="helper">Admin dapat memperbarui status, lokasi terakhir, dan membagi paket ke driver.</p>
                <a class="button button-secondary" href="{{ route('login') }}">Login Admin</a>
            </article>
            <article class="panel">
                <span class="badge badge-brand">Driver</span>
                <h3 style="margin-top: 16px;">Bukti sampai</h3>
                <p class="helper">Driver mengirim foto, waktu, lokasi, dan catatan saat paket diterima di tujuan.</p>
                <a class="button button-secondary" href="{{ route('login') }}">Login Driver</a>
            </article>
        </div>

        <section class="section-tight">
            <article class="panel panel-muted">
                <h2>Kontak bantuan</h2>
                <p class="helper">Hub Denpasar melayani pertanyaan resi dan status paket pada 08.00-17.00 WITA. Untuk kebutuhan mendesak, siapkan nomor resi sebelum menghubungi petugas.</p>
            </article>
        </section>
    </section>
@endsection
