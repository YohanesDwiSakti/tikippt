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
                    <p>Pilih driver dan resi yang akan diangkut. Belum ada submit backend.</p>
                </div>
            </div>

            <article class="panel">
                <form class="form-grid">
                    <div class="field">
                        <label>Driver</label>
                        <select class="select">
                            @foreach($drivers as $driver)
                                <option>{{ $driver['name'] }} · {{ $driver['active'] }} paket aktif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Resi dipilih</label>
                        <input class="input" value="TKI-DEN-260607101500, TKI-DEN-260607150500">
                    </div>
                    <div class="field full">
                        <label>Catatan untuk driver</label>
                        <textarea class="textarea">Paket rute Gianyar dan Ubud. Prioritaskan yang sudah dijadwalkan sampai hari ini.</textarea>
                    </div>
                    <div class="full">
                        <button class="button button-primary" type="button">Assign Mock</button>
                    </div>
                </form>
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
                        @foreach($packages as $package)
                            <tr>
                                <td><input type="checkbox" @checked($package['driver'] === 'Made Driver') aria-label="Pilih {{ $package['receipt'] }}"></td>
                                <td><strong>{{ $package['receipt'] }}</strong></td>
                                <td><span class="badge {{ $package['status'] === 'Sampai Tujuan' ? 'badge-success' : 'badge-brand' }}">{{ $package['status'] }}</span></td>
                                <td>{{ $package['destination'] }}</td>
                                <td>{{ $package['driver'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </div>
@endsection
