@extends('layouts.app')

@section('title', 'Admin')

@section('content')
    <div class="page-shell app-layout">
        @include('partials.admin-sidebar')

        <section class="content-stack">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin Hub</p>
                    <h1 style="font-size: 44px;">Overview paket</h1>
                    <p>Ringkasan status paket, assignment driver, dan bukti delivery dari data operasional.</p>
                </div>
                <a class="button button-primary" href="{{ route('admin.packages') }}">Update Status</a>
            </div>

            <div class="grid-3">
                <article class="panel">
                    <div class="meta-label">Total paket</div>
                    <h2>{{ count($packages) }}</h2>
                    <p class="helper">Data paket aktif.</p>
                </article>
                <article class="panel">
                    <div class="meta-label">Driver aktif</div>
                    <h2>{{ count(array_filter($drivers, fn($driver) => $driver['active'] > 0)) }}</h2>
                    <p class="helper">Driver dengan paket aktif.</p>
                </article>
                <article class="panel">
                    <div class="meta-label">Sudah sampai</div>
                    <h2>{{ count(array_filter($packages, fn($package) => $package['status'] === 'Sampai Tujuan')) }}</h2>
                    <p class="helper">Punya bukti foto/waktu/lokasi.</p>
                </article>
            </div>

            <article class="panel">
                <div class="page-heading" style="margin-bottom: 12px;">
                    <div>
                        <h2>Paket terbaru</h2>
                        <p>Status dan driver yang sedang membawa paket.</p>
                    </div>
                    <a class="button button-secondary" href="{{ route('admin.assignments') }}">Assign Driver</a>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Resi</th>
                            <th>Status</th>
                            <th>Tujuan</th>
                            <th>Driver</th>
                            <th>Update</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td><strong>{{ $package['receipt'] }}</strong></td>
                                <td><span class="badge {{ $package['status'] === 'Sampai Tujuan' ? 'badge-success' : 'badge-brand' }}">{{ $package['status'] }}</span></td>
                                <td>{{ $package['destination'] }}</td>
                                <td>{{ $package['driver'] }}</td>
                                <td>{{ $package['updated_at'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Belum ada data paket.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </div>
@endsection
