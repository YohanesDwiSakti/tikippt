@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <section class="page-shell hero">
        <div>
            <p class="eyebrow">TIKI Denpasar Service Desk</p>
            <h1>Pantau kiriman dari resi sampai bukti diterima.</h1>
            <p class="lead">FINPROPPT menyatukan cek resi, estimasi harga, lokasi gerai, dan bukti pengantaran untuk operasional TIKI Denpasar.</p>
            <form class="tracking-form" action="{{ route('tracking') }}" method="get">
                <input type="hidden" name="tab" value="resi">
                <label class="field" style="flex: 1;">
                    <span class="meta-label">Nomor resi</span>
                    <input class="input" name="receipt" value="{{ $receipt }}" placeholder="TKI-DEN-260607101500">
                </label>
                <button class="button button-primary" type="submit">Cek Resi</button>
            </form>
        </div>

        @include('partials.status-panel', ['selected' => $selected])
    </section>

    <section class="section section-muted">
        <div class="page-shell">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Layanan cepat</p>
                    <h2>Yang paling sering dibutuhkan customer</h2>
                </div>
            </div>

            <div class="service-link-grid">
                <a class="service-link" href="{{ route('tracking', ['tab' => 'resi']) }}">
                    <span class="badge badge-brand">Resi</span>
                    <strong>Status kiriman</strong>
                    <span>Lihat posisi terakhir, riwayat status, tujuan, driver, dan bukti diterima jika sudah tersedia.</span>
                </a>
                <a class="service-link" href="{{ route('tracking', ['tab' => 'harga']) }}">
                    <span class="badge badge-brand">Harga</span>
                    <strong>Estimasi ongkir</strong>
                    <span>Masukkan asal, tujuan, berat, dan dimensi untuk melihat pilihan layanan REG, ONS, dan ECO.</span>
                </a>
                <a class="service-link" href="{{ route('tracking', ['tab' => 'lokasi']) }}">
                    <span class="badge badge-brand">Lokasi</span>
                    <strong>Hub dan gerai</strong>
                    <span>Cari lokasi drop paket, pengambilan paket, jam operasional, dan nomor telepon gerai.</span>
                </a>
            </div>
        </div>
    </section>

    <section class="page-shell section">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Alur kerja</p>
                <h2>Dari paket masuk sampai bukti sampai</h2>
            </div>
        </div>
        <div class="process-grid">
            <article class="process-step">
                <span>01</span>
                <strong>Paket dicatat</strong>
                <p>Admin membuat resi, tujuan, status awal, dan lokasi hub.</p>
            </article>
            <article class="process-step">
                <span>02</span>
                <strong>Driver ditugaskan</strong>
                <p>Paket dipilih dan dibagikan ke driver sesuai rute pengantaran.</p>
            </article>
            <article class="process-step">
                <span>03</span>
                <strong>Customer memantau</strong>
                <p>Customer mengecek resi tanpa perlu login dan melihat riwayat terbaru.</p>
            </article>
            <article class="process-step">
                <span>04</span>
                <strong>Bukti dikirim</strong>
                <p>Driver mengirim foto, waktu, lokasi, dan catatan saat paket sudah diterima.</p>
            </article>
        </div>
    </section>
@endsection
