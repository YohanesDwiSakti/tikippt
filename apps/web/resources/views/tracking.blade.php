@extends('layouts.app')

@section('title', 'Tracking')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Cek Resi</p>
                <h1 style="font-size: 48px;">Status paket</h1>
                <p class="lead">Masukkan nomor resi untuk melihat posisi paket dan bukti sampai tujuan jika sudah dikirim driver.</p>
            </div>
        </div>

        <form class="tracking-form" action="{{ route('tracking') }}" method="get" style="margin-bottom: 24px;">
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
@endsection
