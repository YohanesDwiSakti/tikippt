@extends('layouts.app')

@section('title', 'Driver')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Tugas Driver</p>
                <h1 style="font-size: 44px;">Paket yang harus diangkut</h1>
                <p>Paket yang muncul di sini berasal dari assignment operasional.</p>
            </div>
        </div>

        <div class="task-list" style="margin-top: 24px;">
            @forelse($packages as $package)
                <article class="task-card">
                    <div>
                        <span class="badge {{ $package['status'] === 'Sampai Tujuan' ? 'badge-success' : 'badge-brand' }}">{{ $package['assignment_status'] }}</span>
                        <h2 style="margin-top: 16px;">{{ $package['receipt'] }}</h2>
                        <p class="helper">{{ $package['destination'] }} - {{ $package['latest_location'] }}</p>
                        <p>{{ $package['admin_note'] }}</p>
                    </div>
                    <a class="button button-primary" href="{{ route('driver.proof', ['receipt' => $package['receipt']]) }}">Kirim Bukti</a>
                </article>
            @empty
                <article class="panel panel-muted">
                    <strong>Tidak ada paket aktif</strong>
                    <p class="helper">Paket akan muncul saat admin melakukan assignment.</p>
                </article>
            @endforelse
        </div>
    </section>
@endsection
