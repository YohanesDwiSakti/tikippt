@extends('layouts.app')

@section('title', 'About')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">About</p>
                <h1 style="font-size: 48px;">Platform operasional untuk pengiriman lokal TIKI Denpasar</h1>
                <p class="lead">FINPROPPT dirancang sebagai dashboard internal dan halaman publik ringan untuk memperjelas pergerakan paket, tanggung jawab driver, dan bukti pengantaran.</p>
            </div>
        </div>

        <div class="about-layout">
            <article class="panel">
                <h2>Kenapa dibuat</h2>
                <p class="helper">Operasional pengantaran sering membutuhkan catatan yang cepat dilihat: paket sedang di mana, siapa driver yang membawa, dan apakah sudah ada bukti sampai. Sistem ini menyimpan informasi tersebut dalam alur yang mudah dipahami customer dan tim hub.</p>
            </article>
            <article class="panel panel-muted">
                <h2>Fokus versi ini</h2>
                <ul class="check-list">
                    <li>Cek resi publik tanpa login.</li>
                    <li>Admin mengubah status dan membagi paket ke driver.</li>
                    <li>Driver melihat tugas dan mengirim bukti foto, waktu, serta lokasi.</li>
                    <li>Tidak ada fitur payment atau checkout.</li>
                </ul>
            </article>
        </div>

        <section class="section-tight">
            <div class="grid-3">
                <article class="panel">
                    <span class="badge badge-brand">Web</span>
                    <h3 style="margin-top: 16px;">Laravel frontend</h3>
                    <p class="helper">Tampilan publik, admin, dan driver dibangun sebagai interface web yang cepat dibuka di cabang atau hub.</p>
                </article>
                <article class="panel">
                    <span class="badge badge-brand">API</span>
                    <h3 style="margin-top: 16px;">Go backend</h3>
                    <p class="helper">Backend disiapkan untuk auth, status paket, assignment driver, dan bukti delivery.</p>
                </article>
                <article class="panel">
                    <span class="badge badge-brand">Data</span>
                    <h3 style="margin-top: 16px;">Supabase</h3>
                    <p class="helper">Database dan auth memakai Supabase agar data resi, profil, assignment, dan bukti bisa dikelola terpusat.</p>
                </article>
            </div>
        </section>
    </section>
@endsection
