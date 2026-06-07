@extends('layouts.app')

@section('title', __('messages.tracking.title'))

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ __('messages.tracking.eyebrow') }}</p>
                <h1 style="font-size: 48px;">{{ __('messages.tracking.heading') }}</h1>
                <p class="lead">{{ __('messages.tracking.copy') }}</p>
            </div>
        </div>

        <div class="tool-tabs" aria-label="Pilihan layanan publik">
            <a href="{{ route('tracking', ['tab' => 'resi']) }}" @if($activeTab === 'resi') aria-current="page" @endif>{{ __('messages.tracking.tab_receipt') }}</a>
            <a href="{{ route('tracking', ['tab' => 'harga']) }}" @if($activeTab === 'harga') aria-current="page" @endif>{{ __('messages.tracking.tab_price') }}</a>
            <a href="{{ route('tracking', ['tab' => 'lokasi']) }}" @if($activeTab === 'lokasi') aria-current="page" @endif>{{ __('messages.tracking.tab_location') }}</a>
        </div>

        @if($activeTab === 'resi')
            <section class="service-section">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">{{ __('messages.tracking.tab_receipt') }}</p>
                        <h2>{{ __('messages.tracking.receipt_heading') }}</h2>
                    </div>
                </div>

                <form class="tracking-form" action="{{ route('tracking') }}" method="get" style="margin-bottom: 24px;">
                    <input type="hidden" name="tab" value="resi">
                    <input class="input" name="receipt" value="{{ $receipt }}" placeholder="TKI-DEN-260607101500" aria-label="{{ __('messages.home.receipt_label') }}">
                    <button class="button button-primary" type="submit">{{ __('messages.tracking.tab_receipt') }}</button>
                </form>

                @if($receipt)
                    @include('partials.status-panel', ['selected' => $selected])
                @else
                    <article class="panel panel-muted">
                        <strong>{{ __('messages.tracking.empty_receipt_title') }}</strong>
                        <p class="helper">{{ __('messages.tracking.empty_receipt_copy') }}</p>
                    </article>
                @endif
            </section>
        @endif

        @if($activeTab === 'harga')
            <section class="service-section">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">{{ __('messages.tracking.tab_price') }}</p>
                        <h2>{{ __('messages.tracking.price_heading') }}</h2>
                        <p>{{ __('messages.tracking.price_copy') }}</p>
                    </div>
                </div>

                <form class="panel form-grid" action="{{ route('tracking') }}" method="get">
                    <input type="hidden" name="tab" value="harga">
                    <div class="field">
                        <label>{{ __('messages.tracking.from') }}</label>
                        <input class="input" name="origin" value="{{ $origin }}" placeholder="Denpasar">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.destination') }}</label>
                        <input class="input" name="destination" value="{{ $destination }}" placeholder="Gianyar">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.weight') }}</label>
                        <div class="input-addon">
                            <input class="input" name="weight" type="number" min="1" step="0.1" value="{{ $weight }}" placeholder="1">
                            <span>Kg</span>
                        </div>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.length') }}</label>
                        <div class="input-addon">
                            <input class="input" name="length" type="number" min="0" value="{{ $length }}" placeholder="{{ __('messages.tracking.length') }}">
                            <span>Cm</span>
                        </div>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.width') }}</label>
                        <div class="input-addon">
                            <input class="input" name="width" type="number" min="0" value="{{ $width }}" placeholder="{{ __('messages.tracking.width') }}">
                            <span>Cm</span>
                        </div>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.height') }}</label>
                        <div class="input-addon">
                            <input class="input" name="height" type="number" min="0" value="{{ $height }}" placeholder="{{ __('messages.tracking.height') }}">
                            <span>Cm</span>
                        </div>
                    </div>
                    <div class="full form-actions">
                        <button class="button button-primary" type="submit">{{ __('messages.tracking.tab_price') }}</button>
                    </div>
                </form>

                <div class="section-tight">
                    <p class="helper">{{ __('messages.tracking.billable_weight', ['weight' => $chargeableWeight]) }} @if($volumeWeight > 0) {{ __('messages.tracking.volume_weight', ['weight' => $volumeWeight]) }} @endif.</p>
                    <div class="rate-grid">
                        @foreach($rates as $rate)
                            <article class="panel">
                                <span class="badge badge-brand">{{ $rate['service'] }}</span>
                                <h3 style="margin-top: 16px;">{{ $rate['label'] }}</h3>
                                <p class="helper">{{ __('messages.tracking.route_eta', ['origin' => $origin, 'destination' => $destination, 'eta' => $rate['eta']]) }}</p>
                                <div class="price">Rp{{ number_format($rate['price'], 0, ',', '.') }}</div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if($activeTab === 'lokasi')
            <section class="service-section">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">{{ __('messages.tracking.tab_location') }}</p>
                        <h2>{{ __('messages.tracking.location_heading') }}</h2>
                        <p>{{ __('messages.tracking.location_copy') }}</p>
                    </div>
                </div>

                <form class="tracking-form" action="{{ route('tracking') }}" method="get" style="margin-bottom: 24px;">
                    <input type="hidden" name="tab" value="lokasi">
                    <input class="input" name="q" value="{{ $query }}" placeholder="{{ __('messages.tracking.location_placeholder') }}" aria-label="{{ __('messages.tracking.search_location') }}">
                    <button class="button button-primary" type="submit">{{ __('messages.tracking.search_location') }}</button>
                </form>

                <div class="location-grid">
                    @forelse($locations as $location)
                        <article class="panel location-card">
                            <span class="badge badge-brand">{{ $location['type'] }}</span>
                            <h3 style="margin-top: 16px;">{{ $location['name'] }}</h3>
                            <p class="helper">{{ $location['address'] }}</p>
                            <div class="meta-grid">
                                <div class="meta-item">
                                    <div class="meta-label">{{ __('messages.tracking.open_hours') }}</div>
                                    <div class="meta-value">{{ $location['hours'] }}</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">{{ __('messages.tracking.phone') }}</div>
                                    <div class="meta-value">{{ $location['phone'] }}</div>
                                </div>
                            </div>
                            <div class="service-list">
                                @foreach($location['services'] as $service)
                                    <span>{{ $service }}</span>
                                @endforeach
                            </div>
                        </article>
                    @empty
                        <article class="panel panel-muted">
                            <strong>{{ __('messages.tracking.not_found_title') }}</strong>
                            <p class="helper">{{ __('messages.tracking.not_found_copy') }}</p>
                        </article>
                    @endforelse
                </div>
            </section>
        @endif
    </section>
@endsection
