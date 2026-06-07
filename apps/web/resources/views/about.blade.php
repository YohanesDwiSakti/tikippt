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
            <article class="text-block">
                <h2>Tentang layanan ini</h2>
                <p class="helper">Layanan ini dibuat untuk mengurangi kebingungan saat paket sedang berjalan. Customer dapat mengecek informasi dasar secara mandiri, sementara tim hub memiliki alur yang lebih rapi untuk membagi paket ke driver dan menyimpan bukti pengantaran.</p>
            </article>
            <article class="muted-block">
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
            <div class="info-list">
                <article class="info-item">
                    <span class="badge badge-brand">Status</span>
                    <div>
                        <h3>Pergerakan paket</h3>
                        <p class="helper">Customer melihat posisi terakhir, riwayat status, tujuan, dan waktu pembaruan paket.</p>
                    </div>
                </article>
                <article class="info-item">
                    <span class="badge badge-brand">Harga</span>
                    <div>
                        <h3>Estimasi biaya</h3>
                        <p class="helper">Perkiraan ongkir membantu customer memilih layanan sesuai kebutuhan dan berat paket.</p>
                    </div>
                </article>
                <article class="info-item">
                    <span class="badge badge-brand">Bukti</span>
                    <div>
                        <h3>Konfirmasi sampai</h3>
                        <p class="helper">Saat paket diterima, bukti berupa foto, waktu, lokasi, dan catatan driver dapat dicatat.</p>
                    </div>
                </article>
            </div>
        </section>

        <section class="section-tight">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Area operasional</p>
                    <h2>Cek wilayah layanan secara visual</h2>
                    <p>Gunakan peta untuk melihat cakupan Denpasar dan area sekitar yang menjadi fokus layanan.</p>
                </div>
            </div>
            <div class="map-layout">
                <article class="map-panel">
                    <iframe
                        title="Peta area operasional TIKI Denpasar"
                        src="https://www.openstreetmap.org/export/embed.html?bbox=115.091%2C-8.788%2C115.344%2C-8.552&layer=mapnik&marker=-8.6705%2C115.2126"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </article>
                <article class="map-copy">
                    <h3>Area yang dilayani</h3>
                    <ul class="check-list">
                        <li>Denpasar sebagai hub utama pemrosesan paket.</li>
                        <li>Sanur untuk drop paket dan pengambilan paket area pesisir.</li>
                        <li>Gianyar dan Ubud untuk pengantaran rute harian.</li>
                        <li>Tabanan sebagai cakupan estimasi layanan sekitar.</li>
                    </ul>
                    <div class="section-tight">
                        <a class="button button-primary" href="{{ route('tracking', ['tab' => 'lokasi']) }}">Cek Lokasi Gerai</a>
                        <a class="button button-secondary" href="https://www.openstreetmap.org/?mlat=-8.6705&mlon=115.2126#map=11/-8.6705/115.2126">Buka Peta Besar</a>
                    </div>
                </article>
            </div>
        </section>
    </section>
@endsection
