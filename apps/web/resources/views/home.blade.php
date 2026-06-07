@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <section class="page-shell hero">
        <div>
            <p class="eyebrow">Tracking TIKI Denpasar</p>
            <h1>Cek status resi dan bukti sampai tujuan dari satu layar.</h1>
            <p class="lead">Customer cukup memasukkan nomor resi. Admin mengatur status dan driver. Driver mengirim bukti foto, waktu, dan lokasi saat paket sudah sampai.</p>
            <form class="tracking-form" action="{{ route('tracking') }}" method="get">
                <label class="field" style="flex: 1;">
                    <span class="meta-label">Nomor resi</span>
                    <input class="input" name="receipt" value="{{ $receipt }}" placeholder="TKI-DEN-260607101500">
                </label>
                <button class="button button-primary" type="submit">Cek Resi</button>
            </form>
        </div>

        @include('partials.status-panel', ['selected' => $selected])
    </section>

    <section class="section section-muted">
        <div class="page-shell">
            <div class="grid-3">
                <article class="panel">
                    <span class="badge badge-brand">Customer</span>
                    <h3 style="margin-top: 16px;">Cek resi</h3>
                    <p class="helper">Status, lokasi terakhir, timeline, dan bukti sampai tujuan jika sudah tersedia.</p>
                </article>
                <article class="panel">
                    <span class="badge badge-brand">Admin</span>
                    <h3 style="margin-top: 16px;">Atur paket</h3>
                    <p class="helper">Buat resi, update status, lalu bagikan paket yang diangkut ke driver.</p>
                </article>
                <article class="panel">
                    <span class="badge badge-brand">Driver</span>
                    <h3 style="margin-top: 16px;">Kirim bukti</h3>
                    <p class="helper">Driver melihat tugasnya dan mengirim foto, waktu, serta lokasi saat paket sampai.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
