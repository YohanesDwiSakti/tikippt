@extends('layouts.app')

@section('title', 'About')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">About</p>
                <h1 style="font-size: 48px;">Layanan pengiriman yang lebih mudah dipantau</h1>
                <p class="lead">FINPROPPT membantu customer dan tim hub TIKI Denpasar melihat informasi paket dengan jelas: resi, estimasi harga, lokasi gerai, assignment driver, dan bukti sampai tujuan.</p>
            </div>
        </div>

        <div class="about-layout">
            <article class="panel">
                <h2>Tentang layanan ini</h2>
                <p class="helper">Layanan ini dibuat untuk mengurangi kebingungan saat paket sedang berjalan. Customer dapat mengecek informasi dasar secara mandiri, sementara tim hub memiliki alur yang lebih rapi untuk membagi paket ke driver dan menyimpan bukti pengantaran.</p>
            </article>
            <article class="panel panel-muted">
                <h2>Yang bisa dilakukan</h2>
                <ul class="check-list">
                    <li>Melihat status paket berdasarkan nomor resi.</li>
                    <li>Mengecek estimasi harga kirim sebelum mengirim paket.</li>
                    <li>Menemukan hub atau gerai terdekat di area layanan.</li>
                    <li>Melihat bukti pengantaran saat paket sudah diterima.</li>
                </ul>
            </article>
        </div>

        <section class="section-tight">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Cakupan layanan</p>
                    <h2>Informasi yang disiapkan untuk customer</h2>
                </div>
            </div>
            <div class="grid-3">
                <article class="panel">
                    <span class="badge badge-brand">Status</span>
                    <h3 style="margin-top: 16px;">Pergerakan paket</h3>
                    <p class="helper">Customer melihat posisi terakhir, riwayat status, tujuan, dan waktu pembaruan paket.</p>
                </article>
                <article class="panel">
                    <span class="badge badge-brand">Harga</span>
                    <h3 style="margin-top: 16px;">Estimasi biaya</h3>
                    <p class="helper">Perkiraan ongkir membantu customer memilih layanan sesuai kebutuhan dan berat paket.</p>
                </article>
                <article class="panel">
                    <span class="badge badge-brand">Bukti</span>
                    <h3 style="margin-top: 16px;">Konfirmasi sampai</h3>
                    <p class="helper">Saat paket diterima, bukti berupa foto, waktu, lokasi, dan catatan driver dapat dicatat.</p>
                </article>
            </div>
        </section>

        <section class="section-tight">
            <article class="panel panel-muted">
                <h2>Area operasional</h2>
                <p class="helper">Fokus layanan saat ini adalah Denpasar dan area sekitar seperti Sanur, Gianyar, Ubud, dan Tabanan. Informasi lokasi dan jam operasional dapat dilihat pada tab Cek Lokasi di halaman Layanan.</p>
            </article>
        </section>
    </section>
@endsection
