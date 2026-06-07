@extends('layouts.app')

@section('title', 'Paket')

@section('content')
    <div class="page-shell app-layout">
        @include('partials.admin-sidebar')

        <section class="content-stack">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Admin Paket</p>
                    <h1 style="font-size: 44px;">Create dan update status resi</h1>
                    <p>Kelola resi, status, tujuan, lokasi terakhir, dan catatan operasional.</p>
                </div>
            </div>

            <article class="panel">
                <h2>Update status</h2>
                @if(session('status'))
                    <div class="notice notice-success">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="notice notice-danger">{{ $errors->first() }}</div>
                @endif
                <form class="form-grid" method="POST" action="/admin/packages">
                    @csrf
                    <div class="field">
                        <label>Nomor resi</label>
                        <input class="input" name="receipt" value="{{ old('receipt') }}" required>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select class="select" name="status" required>
                            @foreach(['Terdaftar', 'Diangkut Driver', 'Dalam Perjalanan', 'Sampai Tujuan', 'Gagal Dikirim', 'Cancel'] as $status)
                                <option value="{{ $status }}" @selected(old('status') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Tujuan</label>
                        <input class="input" name="destination" value="{{ old('destination') }}" required>
                    </div>
                    <div class="field">
                        <label>Lokasi terakhir</label>
                        <input class="input" name="latest_location" value="{{ old('latest_location') }}" required>
                    </div>
                    <div class="field full">
                        <label>Catatan status</label>
                        <textarea class="textarea" name="note">{{ old('note') }}</textarea>
                    </div>
                    <div class="full">
                        <button class="button button-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </article>

            <article class="panel">
                <h2>Daftar paket</h2>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Resi</th>
                            <th>Status</th>
                            <th>Tujuan</th>
                            <th>Lokasi</th>
                            <th>Driver</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td><strong>{{ $package['receipt'] }}</strong></td>
                                <td><span class="badge {{ $package['status'] === 'Sampai Tujuan' ? 'badge-success' : 'badge-brand' }}">{{ $package['status'] }}</span></td>
                                <td>{{ $package['destination'] }}</td>
                                <td>{{ $package['latest_location'] }}</td>
                                <td>{{ $package['driver'] }}</td>
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
