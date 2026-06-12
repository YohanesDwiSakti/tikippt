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
            <a href="{{ route('tracking', ['tab' => 'resi']) }}#resi-panel" @if($activeTab === 'resi') aria-current="page" @endif>{{ __('messages.tracking.tab_receipt') }}</a>
            <a href="{{ route('tracking', ['tab' => 'harga']) }}#harga-panel" @if($activeTab === 'harga') aria-current="page" @endif>{{ __('messages.tracking.tab_price') }}</a>
            <a href="{{ route('tracking', ['tab' => 'lokasi']) }}#lokasi-panel" @if($activeTab === 'lokasi') aria-current="page" @endif>{{ __('messages.tracking.tab_location') }}</a>
        </div>

        @if($activeTab === 'resi')
            <section id="resi-panel" class="service-section">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">{{ __('messages.tracking.tab_receipt') }}</p>
                        <h2>{{ __('messages.tracking.receipt_heading') }}</h2>
                    </div>
                </div>

                <form class="tracking-form" action="{{ route('tracking') }}#resi-panel" method="get" style="margin-bottom: 24px;">
                    <input type="hidden" name="tab" value="resi">
                    <input class="input" name="receipt" value="{{ $receipt }}" aria-label="{{ __('messages.home.receipt_label') }}">
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
            <section id="harga-panel" class="service-section">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">{{ __('messages.tracking.tab_price') }}</p>
                        <h2>{{ __('messages.tracking.price_heading') }}</h2>
                        <p>{{ __('messages.tracking.price_copy') }}</p>
                    </div>
                </div>

                <form class="panel form-grid" action="{{ route('tracking') }}#harga-panel" method="get">
                    <input type="hidden" name="tab" value="harga">
                    <div class="field">
                        <label>{{ __('messages.tracking.from') }}</label>
                        <input class="input" name="origin" value="{{ $origin }}" placeholder="Denpasar">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.destination') }}</label>
                        <input class="input" name="destination" value="{{ $destination }}" placeholder="Gianyar, Sanur, Ubud">
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.weight') }}</label>
                        <div class="input-addon">
                            <input class="input" name="weight" type="number" min="1" step="0.1" value="{{ $weight }}">
                            <span>Kg</span>
                        </div>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.length') }}</label>
                        <div class="input-addon">
                            <input class="input" name="length" type="number" min="0" value="{{ $length }}">
                            <span>Cm</span>
                        </div>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.width') }}</label>
                        <div class="input-addon">
                            <input class="input" name="width" type="number" min="0" value="{{ $width }}">
                            <span>Cm</span>
                        </div>
                    </div>
                    <div class="field">
                        <label>{{ __('messages.tracking.height') }}</label>
                        <div class="input-addon">
                            <input class="input" name="height" type="number" min="0" value="{{ $height }}">
                            <span>Cm</span>
                        </div>
                    </div>
                    <div class="full form-actions">
                        <button class="button button-primary" type="submit">{{ __('messages.tracking.tab_price') }}</button>
                    </div>
                </form>

                <div class="section-tight">
                    <p class="helper">{{ __('messages.tracking.price_note') }}</p>
                    @if($calculation)
                        <article class="panel calculation-panel">
                            <div>
                                <p class="eyebrow">{{ __('messages.tracking.calculation_title') }}</p>
                                <h3>{{ __('messages.tracking.calculation_heading') }}</h3>
                            </div>
                            <div class="calculation-grid">
                                <div class="meta-item">
                                    <div class="meta-label">{{ __('messages.tracking.actual_weight') }}</div>
                                    <div class="meta-value">{{ rtrim(rtrim(number_format($calculation['actual_weight'], 2, ',', '.'), '0'), ',') }} kg</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">{{ __('messages.tracking.volume_formula') }}</div>
                                    <div class="meta-value">{{ rtrim(rtrim(number_format($calculation['length'], 2, ',', '.'), '0'), ',') }} x {{ rtrim(rtrim(number_format($calculation['width'], 2, ',', '.'), '0'), ',') }} x {{ rtrim(rtrim(number_format($calculation['height'], 2, ',', '.'), '0'), ',') }} / 6000 = {{ $calculation['volume_weight'] }} kg</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">{{ __('messages.tracking.billable_label') }}</div>
                                    <div class="meta-value">max({{ rtrim(rtrim(number_format($calculation['actual_weight'], 2, ',', '.'), '0'), ',') }}, {{ $calculation['volume_weight'] }}) = {{ $calculation['chargeable_weight'] }} kg</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">{{ __('messages.tracking.area_rate') }}</div>
                                    <div class="meta-value">Rp{{ number_format($calculation['base_rate'], 0, ',', '.') }} + Rp{{ number_format($calculation['route_surcharge'], 0, ',', '.') }} = Rp{{ number_format($calculation['rate_per_kg'], 0, ',', '.') }}/kg</div>
                                </div>
                            </div>
                        </article>
                    @endif
                    <div class="rate-grid">
                        @forelse($rates as $rate)
                            <article class="panel">
                                <span class="badge badge-brand">{{ $rate['service'] }}</span>
                                <h3 style="margin-top: 16px;">{{ $rate['label'] }}</h3>
                                <p class="helper">{{ __('messages.tracking.route_eta', ['origin' => $origin, 'destination' => $destination, 'eta' => $rate['eta']]) }}</p>
                                @if($calculation)
                                    <p class="helper">{{ __('messages.tracking.service_formula', [
                                        'rate' => 'Rp' . number_format($calculation['rate_per_kg'], 0, ',', '.'),
                                        'weight' => $calculation['chargeable_weight'],
                                        'multiplier' => rtrim(rtrim(number_format($rate['multiplier'], 2, ',', '.'), '0'), ','),
                                    ]) }}</p>
                                @endif
                                <div class="price">Rp{{ number_format($rate['price'], 0, ',', '.') }}</div>
                            </article>
                        @empty
                            <article class="panel panel-muted">
                                <strong>{{ __('messages.tracking.empty_rates_title') }}</strong>
                                <p class="helper">{{ __('messages.tracking.empty_rates_copy') }}</p>
                            </article>
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        @if($activeTab === 'lokasi')
            <section id="lokasi-panel" class="service-section">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">{{ __('messages.tracking.tab_location') }}</p>
                        <h2>{{ __('messages.tracking.location_heading') }}</h2>
                        <p>{{ __('messages.tracking.location_copy') }}</p>
                    </div>
                </div>

                <form class="tracking-form" action="{{ route('tracking') }}#lokasi-panel" method="get" style="margin-bottom: 24px;">
                    <input type="hidden" name="tab" value="lokasi">
                    <input class="input" name="q" value="{{ $query }}" placeholder="Kapten Regug, Gatsu, Nangka" aria-label="{{ __('messages.tracking.search_location') }}">
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
                            <div class="location-actions">
                                <a class="button button-secondary" href="{{ $location['maps_url'] }}" target="_blank" rel="noopener noreferrer">{{ __('messages.tracking.open_maps') }}</a>
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
