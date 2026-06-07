@extends('layouts.app')

@section('title', 'Layanan')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Layanan Publik</p>
                <h1 style="font-size: 48px;">Cek resi, harga, dan lokasi dalam satu halaman</h1>
                <p class="lead">Customer bisa melihat status paket, estimasi ongkir, dan lokasi hub atau gerai tanpa pindah halaman.</p>
            </div>
        </div>

        <div class="tool-tabs" aria-label="Pilihan layanan publik">
            <a href="#resi" aria-current="page">Cek Resi</a>
            <a href="#harga">Cek Harga</a>
            <a href="#lokasi">Cek Lokasi</a>
        </div>

        <section class="service-section" id="resi">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Cek Resi</p>
                    <h2>Status paket</h2>
                </div>
            </div>

            <form class="tracking-form" action="{{ route('tracking') }}#resi" method="get" style="margin-bottom: 24px;">
                <input class="input" name="receipt" value="{{ $receipt }}" placeholder="TKI-DEN-260607101500" aria-label="Nomor resi">
                <button class="button button-primary" type="submit">Cek Resi</button>
            </form>

            @if($receipt)
                @include('partials.status-panel', ['selected' => $selected])
            @else
                <article class="panel panel-muted">
                    <strong>Belum ada resi dicari</strong>
                    <p class="helper">Gunakan contoh TKI-DEN-260607101500 untuk melihat state mock.</p>
                </article>
            @endif
        </section>

        <section class="service-section" id="harga">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Cek Harga</p>
                    <h2>Estimasi ongkir</h2>
                    <p>Hitung biaya kirim berdasarkan asal, tujuan, berat, dan dimensi paket.</p>
                </div>
            </div>

            <form class="panel form-grid" action="{{ route('tracking') }}#harga" method="get">
                <div class="field">
                    <label>Dari</label>
                    <input class="input" name="origin" value="{{ $origin }}" placeholder="Denpasar">
                </div>
                <div class="field">
                    <label>Tujuan</label>
                    <input class="input" name="destination" value="{{ $destination }}" placeholder="Gianyar">
                </div>
                <div class="field">
                    <label>Berat</label>
                    <div class="input-addon">
                        <input class="input" name="weight" type="number" min="1" step="0.1" value="{{ $weight }}" placeholder="1">
                        <span>Kg</span>
                    </div>
                </div>
                <div class="field">
                    <label>Panjang</label>
                    <div class="input-addon">
                        <input class="input" name="length" type="number" min="0" value="{{ $length }}" placeholder="Panjang">
                        <span>Cm</span>
                    </div>
                </div>
                <div class="field">
                    <label>Lebar</label>
                    <div class="input-addon">
                        <input class="input" name="width" type="number" min="0" value="{{ $width }}" placeholder="Lebar">
                        <span>Cm</span>
                    </div>
                </div>
                <div class="field">
                    <label>Tinggi</label>
                    <div class="input-addon">
                        <input class="input" name="height" type="number" min="0" value="{{ $height }}" placeholder="Tinggi">
                        <span>Cm</span>
                    </div>
                </div>
                <div class="full form-actions">
                    <button class="button button-primary" type="submit">Cek Harga</button>
                </div>
            </form>

            <div class="section-tight">
                <p class="helper">Berat tagihan {{ $chargeableWeight }} kg @if($volumeWeight > 0) dari berat volume {{ $volumeWeight }} kg @endif.</p>
                <div class="rate-grid">
                    @foreach($rates as $rate)
                        <article class="panel">
                            <span class="badge badge-brand">{{ $rate['service'] }}</span>
                            <h3 style="margin-top: 16px;">{{ $rate['label'] }}</h3>
                            <p class="helper">{{ $origin }} ke {{ $destination }} · {{ $rate['eta'] }}</p>
                            <div class="price">Rp{{ number_format($rate['price'], 0, ',', '.') }}</div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="service-section" id="lokasi">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Cek Lokasi</p>
                    <h2>Hub dan gerai</h2>
                    <p>Cari lokasi layanan untuk drop paket, ambil paket, dan bantuan resi.</p>
                </div>
            </div>

            <form class="tracking-form" action="{{ route('tracking') }}#lokasi" method="get" style="margin-bottom: 24px;">
                <input class="input" name="q" value="{{ $query }}" placeholder="Cari Denpasar, Sanur, Ubud" aria-label="Cari lokasi">
                <button class="button button-primary" type="submit">Cari Lokasi</button>
            </form>

            <div class="location-grid">
                @forelse($locations as $location)
                    <article class="panel location-card">
                        <span class="badge badge-brand">{{ $location['type'] }}</span>
                        <h3 style="margin-top: 16px;">{{ $location['name'] }}</h3>
                        <p class="helper">{{ $location['address'] }}</p>
                        <div class="meta-grid">
                            <div class="meta-item">
                                <div class="meta-label">Jam buka</div>
                                <div class="meta-value">{{ $location['hours'] }}</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Telepon</div>
                                <div class="meta-value">{{ $location['phone'] }}</div>
                            </div>
                        </div>
                        <div class="service-list">
                            @foreach($location['services'] as $service)
                                <span>{{ $service }}</span>
                            @endforeach
                        </div>
                    </article>
                @empty
                    <article class="panel panel-muted">
                        <strong>Lokasi tidak ditemukan</strong>
                        <p class="helper">Coba cari berdasarkan area seperti Denpasar, Sanur, atau Ubud.</p>
                    </article>
                @endforelse
            </div>
        </section>
    </section>
@endsection
