@extends('layouts.app')

@section('title', 'Kirim Bukti')

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Bukti sampai tujuan</p>
                <h1 style="font-size: 44px;">{{ $package['receipt'] }}</h1>
                <p>Upload bukti saat paket sudah benar sampai tujuan.</p>
            </div>
            <a class="button button-secondary" href="{{ route('driver.index') }}">Kembali</a>
        </div>

        <div class="grid-3" style="margin: 24px 0;">
            <article class="panel">
                <div class="meta-label">Tujuan</div>
                <div class="meta-value">{{ $package['destination'] }}</div>
            </article>
            <article class="panel">
                <div class="meta-label">Status</div>
                <div class="meta-value">{{ $package['status'] }}</div>
            </article>
            <article class="panel">
                <div class="meta-label">Lokasi terakhir</div>
                <div class="meta-value">{{ $package['latest_location'] }}</div>
            </article>
        </div>

        <article class="panel">
            @if($errors->any())
                <div class="notice notice-danger">{{ $errors->first() }}</div>
            @endif
            <form class="form-grid" method="POST" action="/driver/proof/{{ $package['receipt'] }}" enctype="multipart/form-data">
                @csrf
                <div class="field full">
                    <label>Foto bukti</label>
                    <input class="input" id="proof-photo" name="photo" type="file" accept="image/*" capture="environment" required>
                    <img class="proof-capture-preview" id="proof-photo-preview" alt="Preview foto bukti" hidden>
                    <p class="helper">Ambil foto paket langsung dari kamera HP.</p>
                </div>
                <div class="field">
                    <label>Waktu sampai</label>
                    <input class="input" name="delivered_at" type="datetime-local" value="{{ old('delivered_at') }}" required>
                </div>
                <div class="field">
                    <label>Lokasi sampai</label>
                    <input class="input" name="delivered_location" value="{{ old('delivered_location') }}" required>
                </div>
                <div class="field">
                    <label>Latitude</label>
                    <input class="input" name="latitude" value="{{ old('latitude') }}">
                </div>
                <div class="field">
                    <label>Longitude</label>
                    <input class="input" name="longitude" value="{{ old('longitude') }}">
                </div>
                <div class="field full">
                    <label>Catatan</label>
                    <textarea class="textarea" name="note">{{ old('note') }}</textarea>
                </div>
                <div class="full">
                    <button class="button button-primary" type="submit">Kirim Bukti</button>
                </div>
            </form>
        </article>
    </section>

    <script>
        const proofPhotoInput = document.getElementById('proof-photo');
        const proofPhotoPreview = document.getElementById('proof-photo-preview');

        proofPhotoInput?.addEventListener('change', () => {
            const [file] = proofPhotoInput.files || [];
            if (!file) {
                proofPhotoPreview.hidden = true;
                proofPhotoPreview.removeAttribute('src');
                return;
            }

            proofPhotoPreview.src = URL.createObjectURL(file);
            proofPhotoPreview.hidden = false;
        });
    </script>
@endsection
