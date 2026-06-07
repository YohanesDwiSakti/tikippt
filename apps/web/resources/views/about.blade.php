@extends('layouts.app')

@section('title', 'About')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">About</p>
                <h1 style="font-size: 48px;">Sistem tracking untuk TIKI Denpasar</h1>
                <p class="lead">FINPROPPT membantu customer mengecek resi, admin mengatur status paket, dan driver mengirim bukti sampai tujuan.</p>
            </div>
        </div>

        <div class="grid-3">
            <article class="panel">
                <span class="badge badge-brand">1</span>
                <h3 style="margin-top: 16px;">Customer transparan</h3>
                <p class="helper">Customer melihat status, lokasi terakhir, timeline, dan ringkasan bukti sampai tujuan.</p>
            </article>
            <article class="panel">
                <span class="badge badge-brand">2</span>
                <h3 style="margin-top: 16px;">Admin terkontrol</h3>
                <p class="helper">Admin membuat resi, update status, dan menentukan paket yang diangkut driver.</p>
            </article>
            <article class="panel">
                <span class="badge badge-brand">3</span>
                <h3 style="margin-top: 16px;">Driver jelas</h3>
                <p class="helper">Driver hanya melihat paket yang ditugaskan dan mengirim bukti saat paket sampai.</p>
            </article>
        </div>
    </section>
@endsection
