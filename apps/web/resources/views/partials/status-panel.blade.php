@if($selected)
    @php
        $masked = $masked ?? false;
        $maskedReceiptPrefix = substr($selected['receipt'], 0, 8);
        $maskedReceiptSuffix = substr($selected['receipt'], -4);
    @endphp
    <article class="status-panel" aria-label="{{ __('messages.status_panel.label', ['receipt' => $selected['receipt']]) }}">
        <div class="page-heading">
            <div>
                <span class="badge {{ $selected['status'] === 'Sampai Tujuan' ? 'badge-success' : 'badge-brand' }}">
                    {{ __('messages.package_status.' . $selected['status']) }}
                </span>
                @if($masked)
                    <h2 class="masked-receipt" style="margin-top: 16px;">
                        <span>{{ $maskedReceiptPrefix }}</span>
                        <span class="redacted-inline" aria-hidden="true"></span>
                        <span>{{ $maskedReceiptSuffix }}</span>
                    </h2>
                    <p class="helper">{{ __('messages.status_panel.last_update_masked') }}</p>
                @else
                    <h2 style="margin-top: 16px;">{{ $selected['receipt'] }}</h2>
                    <p class="helper">{{ __('messages.status_panel.last_update', ['time' => $selected['updated_at']]) }}</p>
                @endif
            </div>
            <a class="button button-secondary" href="{{ $masked ? route('tracking') : route('tracking', ['receipt' => $selected['receipt']]) }}">
                {{ $masked ? __('messages.home.check_receipt') : __('messages.status_panel.detail') }}
            </a>
        </div>

        @if($masked)
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-label">{{ __('messages.status_panel.destination') }}</div>
                    <div class="redacted-line redacted-short" aria-hidden="true"></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">{{ __('messages.status_panel.latest_location') }}</div>
                    <div class="redacted-line" aria-hidden="true"></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Driver</div>
                    <div class="redacted-line redacted-medium" aria-hidden="true"></div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">{{ __('messages.status_panel.assignment') }}</div>
                    <div class="meta-value">{{ __('messages.package_status.' . $selected['assignment_status']) }}</div>
                </div>
            </div>

            <h3>{{ __('messages.status_panel.history') }}</h3>
            <ul class="timeline">
                <li>
                    <span class="dot" aria-hidden="true"></span>
                    <div>
                        <strong>{{ __('messages.package_status.' . $selected['status']) }}</strong>
                        <div class="redacted-line redacted-long" aria-hidden="true"></div>
                    </div>
                </li>
                <li>
                    <span class="dot" aria-hidden="true"></span>
                    <div>
                        <div class="redacted-line redacted-medium" aria-hidden="true"></div>
                        <div class="redacted-line redacted-short" aria-hidden="true"></div>
                    </div>
                </li>
            </ul>
        @else
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-label">{{ __('messages.status_panel.destination') }}</div>
                    <div class="meta-value">{{ $selected['destination'] }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">{{ __('messages.status_panel.latest_location') }}</div>
                    <div class="meta-value">{{ $selected['latest_location'] }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Driver</div>
                    <div class="meta-value">{{ $selected['driver'] }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">{{ __('messages.status_panel.assignment') }}</div>
                    <div class="meta-value">{{ __('messages.package_status.' . $selected['assignment_status']) }}</div>
                </div>
            </div>

            <h3>{{ __('messages.status_panel.history') }}</h3>
            <ul class="timeline">
                @foreach($selected['timeline'] as $event)
                    <li>
                        <span class="dot" aria-hidden="true"></span>
                        <div>
                            <strong>{{ __('messages.package_status.' . $event['status']) }}</strong>
                            <div class="helper">{{ $event['location'] }} - {{ $event['time'] }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>

            @if($selected['proof'])
                <div class="panel panel-muted" style="margin-top: 20px;">
                    <strong>{{ __('messages.status_panel.proof_title') }}</strong>
                    <p class="helper">{{ __('messages.status_panel.proof_copy') }}</p>
                </div>
            @endif
        @endif
    </article>
@else
    <article class="status-panel">
        @if(empty($receipt ?? ''))
            <span class="badge badge-brand">{{ __('messages.home.card_receipt_label') }}</span>
            <h2 style="margin-top: 16px;">{{ __('messages.tracking.empty_receipt_title') }}</h2>
            <p class="helper">{{ __('messages.tracking.empty_receipt_copy') }}</p>
        @else
            <span class="badge badge-warning">{{ __('messages.status_panel.missing_badge') }}</span>
            <h2 style="margin-top: 16px;">{{ __('messages.status_panel.missing_title') }}</h2>
            <p class="helper">{{ __('messages.status_panel.missing_copy') }}</p>
        @endif
    </article>
@endif
