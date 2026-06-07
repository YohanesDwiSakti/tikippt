@extends('layouts.app')

@section('title', 'Bukti Delivery')

@section('content')
    <div class="page-shell app-layout">
        @include('partials.admin-sidebar')

        <section class="content-stack">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Bukti sampai tujuan</p>
                    <h1 style="font-size: 44px;">Review foto, waktu, dan lokasi</h1>
                    <p>Admin dapat mengecek bukti paket yang sudah dinyatakan sampai tujuan.</p>
                </div>
            </div>

            @forelse($packages as $package)
                <article class="panel proof-preview">
                    <img class="proof-image" src="{{ $package['proof']['photo'] }}" alt="Bukti delivery {{ $package['receipt'] }}">
                    <div>
                        <span class="badge badge-success">{{ $package['status'] }}</span>
                        <h2 style="margin-top: 16px;">{{ $package['receipt'] }}</h2>
                        <div class="meta-grid">
                            <div class="meta-item">
                                <div class="meta-label">Driver</div>
                                <div class="meta-value">{{ $package['driver'] }}</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Waktu</div>
                                <div class="meta-value">{{ $package['proof']['time'] }}</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Lokasi</div>
                                <div class="meta-value">{{ $package['proof']['location'] }}</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Catatan</div>
                                <div class="meta-value">{{ $package['proof']['note'] }}</div>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <article class="panel panel-muted">
                    <strong>Belum ada bukti delivery</strong>
                    <p class="helper">Bukti akan muncul setelah driver mengirim foto, waktu, dan lokasi.</p>
                </article>
            @endforelse
        </section>
    </div>
@endsection
