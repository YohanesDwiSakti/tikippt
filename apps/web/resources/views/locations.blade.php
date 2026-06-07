@extends('layouts.app')

@section('title', 'Lokasi')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Cek Lokasi</p>
                <h1 style="font-size: 48px;">Temukan hub dan gerai</h1>
                <p class="lead">Cari lokasi layanan TIKI Denpasar untuk drop paket, ambil paket, dan bantuan resi.</p>
            </div>
        </div>

        <div class="tool-tabs" aria-label="Pilihan layanan publik">
            <a href="{{ route('tracking') }}">Cek Resi</a>
            <a href="{{ route('shipping') }}">Cek Ongkir</a>
            <a href="{{ route('locations') }}" aria-current="page">Cek Lokasi</a>
        </div>

        <form class="tracking-form" action="{{ route('locations') }}" method="get" style="margin-bottom: 24px;">
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
@endsection
