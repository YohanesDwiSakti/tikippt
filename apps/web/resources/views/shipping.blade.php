@extends('layouts.app')

@section('title', 'Cek Ongkir')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Cek Ongkir</p>
                <h1 style="font-size: 48px;">Estimasi biaya kirim</h1>
                <p class="lead">Hitung ongkir berdasarkan kota asal, tujuan, berat, dan dimensi paket.</p>
            </div>
        </div>

        <div class="tool-tabs" aria-label="Pilihan layanan publik">
            <a href="{{ route('tracking') }}">Cek Resi</a>
            <a href="{{ route('shipping') }}" aria-current="page">Cek Ongkir</a>
            <a href="{{ route('locations') }}">Cek Lokasi</a>
        </div>

        <form class="panel form-grid" action="{{ route('shipping') }}" method="get">
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
                <button class="button button-primary" type="submit">Cek Ongkir</button>
            </div>
        </form>

        <section class="section-tight">
            <div class="page-heading">
                <div>
                    <h2>Hasil estimasi</h2>
                    <p>Berat tagihan {{ $chargeableWeight }} kg @if($volumeWeight > 0) dari berat volume {{ $volumeWeight }} kg @endif.</p>
                </div>
            </div>
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
        </section>
    </section>
@endsection
