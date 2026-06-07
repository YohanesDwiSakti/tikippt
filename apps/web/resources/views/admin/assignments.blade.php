@extends('layouts.app')

@section('title', 'Assign Driver')

@section('content')
    <div class="page-shell app-layout">
        @include('partials.admin-sidebar')

        <section class="content-stack">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Assignment</p>
                    <h1 style="font-size: 44px;">Bagikan paket ke driver</h1>
                    <p>Pilih driver dan resi yang akan diangkut.</p>
                </div>
            </div>

            @if(session('status'))
                <div class="notice notice-success">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="notice notice-danger">{{ $errors->first() }}</div>
            @endif
            <form class="content-stack" method="POST" action="/admin/assignments">
                @csrf

                <article class="panel">
                    <div class="form-grid">
                    <div class="field">
                        <label>Driver</label>
                        <select class="select" name="driver_id" required>
                            @forelse($drivers as $driver)
                                <option value="{{ $driver['id'] }}" @selected(old('driver_id') === $driver['id'])>{{ $driver['name'] }} - {{ $driver['active'] }} paket aktif</option>
                            @empty
                                <option value="">Belum ada driver tersedia</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="field">
                        <label>Resi dipilih</label>
                        <input class="input" name="manual_receipt" value="{{ old('manual_receipt') }}">
                        <p class="helper">Bisa isi manual atau centang dari tabel.</p>
                    </div>
                    <div class="field full">
                        <label>Catatan untuk driver</label>
                        <textarea class="textarea" name="note">{{ old('note') }}</textarea>
                    </div>
                    <div class="full">
                        <button class="button button-primary" type="submit">Assign</button>
                    </div>
                    </div>
                </article>

                <article class="panel">
                    <h2>Paket yang bisa diassign</h2>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                            <tr>
                                <th>Pilih</th>
                                <th>Resi</th>
                                <th>Status</th>
                                <th>Tujuan</th>
                                <th>Driver sekarang</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($packages as $package)
                                <tr>
                                    <td><input type="checkbox" name="receipts[]" value="{{ $package['receipt'] }}" aria-label="Pilih {{ $package['receipt'] }}"></td>
                                    <td><strong>{{ $package['receipt'] }}</strong></td>
                                    <td><span class="badge {{ $package['status'] === 'Sampai Tujuan' ? 'badge-success' : 'badge-brand' }}">{{ $package['status'] }}</span></td>
                                    <td>{{ $package['destination'] }}</td>
                                    <td>{{ $package['driver'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Belum ada paket yang bisa diassign.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </form>
        </section>
    </div>
@endsection
