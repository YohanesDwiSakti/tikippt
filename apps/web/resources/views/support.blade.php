@extends('layouts.app')

@section('title', 'Support')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Support</p>
                <h1 style="font-size: 48px;">Kontak bantuan dan informasi operasional</h1>
                <p class="lead">Hubungi tim sesuai kebutuhan: pertanyaan resi, bantuan admin, kendala driver, atau permintaan update data paket.</p>
            </div>
        </div>

        <div class="support-layout">
            <article class="support-block">
                <span class="badge badge-brand">Customer Care</span>
                <h2 style="margin-top: 16px;">Kontak utama</h2>
                <div class="contact-list">
                    <a href="tel:+62361000101"><span>Telepon</span><strong>0361 000 101</strong></a>
                    <a href="https://wa.me/6281234567801"><span>WhatsApp</span><strong>0812 3456 7801</strong></a>
                    <a href="mailto:support@finproppt.test"><span>Email</span><strong>support@finproppt.test</strong></a>
                    <a href="https://instagram.com/tikidenpasar"><span>Instagram</span><strong>@tikidenpasar</strong></a>
                </div>
            </article>

            <article class="support-note">
                <h2>Jam layanan</h2>
                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="meta-label">Senin-Jumat</div>
                        <div class="meta-value">08.00-17.00 WITA</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Sabtu</div>
                        <div class="meta-value">08.00-14.00 WITA</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Minggu/libur</div>
                        <div class="meta-value">Monitoring terbatas</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Area</div>
                        <div class="meta-value">Denpasar, Sanur, Gianyar, Ubud</div>
                    </div>
                </div>
            </article>
        </div>

        <section class="section-tight">
            <div class="info-list">
                <article class="info-item">
                    <span class="badge badge-brand">Resi</span>
                    <div>
                        <h3>Sebelum menghubungi</h3>
                        <p class="helper">Siapkan nomor resi, nama penerima, area tujuan, dan status terakhir yang terlihat di halaman layanan.</p>
                    </div>
                </article>
                <article class="info-item">
                    <span class="badge badge-brand">Admin</span>
                    <div>
                        <h3>Koreksi data paket</h3>
                        <p class="helper">Untuk perubahan status, tujuan, atau driver, hubungi admin hub dan sertakan alasan koreksi.</p>
                    </div>
                </article>
                <article class="info-item">
                    <span class="badge badge-brand">Driver</span>
                    <div>
                        <h3>Kendala pengantaran</h3>
                        <p class="helper">Laporkan alamat tidak ditemukan, penerima tidak di tempat, atau bukti foto gagal upload.</p>
                    </div>
                </article>
            </div>
        </section>
    </section>
@endsection
