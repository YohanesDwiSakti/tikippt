@if($selected)
    <article class="status-panel" aria-label="Status resi {{ $selected['receipt'] }}">
        <div class="page-heading">
            <div>
                <span class="badge {{ $selected['status'] === 'Sampai Tujuan' ? 'badge-success' : 'badge-brand' }}">
                    {{ $selected['status'] }}
                </span>
                <h2 style="margin-top: 16px;">{{ $selected['receipt'] }}</h2>
                <p class="helper">Update terakhir: {{ $selected['updated_at'] }}</p>
            </div>
            <a class="button button-secondary" href="{{ route('tracking', ['receipt' => $selected['receipt']]) }}">Detail</a>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <div class="meta-label">Tujuan</div>
                <div class="meta-value">{{ $selected['destination'] }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Lokasi terakhir</div>
                <div class="meta-value">{{ $selected['latest_location'] }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Driver</div>
                <div class="meta-value">{{ $selected['driver'] }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Assignment</div>
                <div class="meta-value">{{ $selected['assignment_status'] }}</div>
            </div>
        </div>

        <h3>Riwayat status</h3>
        <ul class="timeline">
            @foreach($selected['timeline'] as $event)
                <li>
                    <span class="dot" aria-hidden="true"></span>
                    <div>
                        <strong>{{ $event['status'] }}</strong>
                        <div class="helper">{{ $event['location'] }} · {{ $event['time'] }}</div>
                    </div>
                </li>
            @endforeach
        </ul>

        @if($selected['proof'])
            <div class="panel panel-muted" style="margin-top: 20px;">
                <strong>Bukti sampai tujuan tersedia</strong>
                <p class="helper">Foto, waktu, dan lokasi delivery sudah dicatat oleh driver.</p>
            </div>
        @endif
    </article>
@else
    <article class="status-panel">
        <span class="badge badge-warning">Belum ditemukan</span>
        <h2 style="margin-top: 16px;">Resi belum ada di data mock</h2>
        <p class="helper">Coba gunakan contoh resi TKI-DEN-260607101500 atau TKI-DEN-260607132000.</p>
    </article>
@endif
