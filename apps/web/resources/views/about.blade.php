@extends('layouts.app')

@section('title', __('messages.about.title'))

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ __('messages.about.title') }}</p>
                <h1 style="font-size: 48px;">{{ __('messages.about.heading') }}</h1>
                <p class="lead">{{ __('messages.about.copy') }}</p>
            </div>
        </div>

        <div class="about-layout">
            <article class="text-block">
                <h2>{{ __('messages.about.about_service') }}</h2>
                <p class="helper">{{ __('messages.about.about_copy') }}</p>
            </article>
            <article class="muted-block">
                <h2>{{ __('messages.about.capabilities') }}</h2>
                <ul class="check-list">
                    <li>{{ __('messages.about.capability_1') }}</li>
                    <li>{{ __('messages.about.capability_2') }}</li>
                    <li>{{ __('messages.about.capability_3') }}</li>
                    <li>{{ __('messages.about.capability_4') }}</li>
                </ul>
            </article>
        </div>

        <section class="section-tight">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">{{ __('messages.about.coverage') }}</p>
                    <h2>{{ __('messages.about.coverage_heading') }}</h2>
                </div>
            </div>
            <div class="info-list">
                <article class="info-item">
                    <span class="badge badge-brand">{{ __('messages.home.card_receipt_title') }}</span>
                    <div>
                        <h3>{{ __('messages.about.movement') }}</h3>
                        <p class="helper">{{ __('messages.about.movement_copy') }}</p>
                    </div>
                </article>
                <article class="info-item">
                    <span class="badge badge-brand">{{ __('messages.home.card_price_label') }}</span>
                    <div>
                        <h3>{{ __('messages.about.cost') }}</h3>
                        <p class="helper">{{ __('messages.about.cost_copy') }}</p>
                    </div>
                </article>
                <article class="info-item">
                    <span class="badge badge-brand">{{ __('messages.about.proof') }}</span>
                    <div>
                        <h3>{{ __('messages.about.proof') }}</h3>
                        <p class="helper">{{ __('messages.about.proof_copy') }}</p>
                    </div>
                </article>
            </div>
        </section>

        <section class="section-tight">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">{{ __('messages.about.area') }}</p>
                    <h2>{{ __('messages.about.area_heading') }}</h2>
                    <p>{{ __('messages.about.area_copy') }}</p>
                </div>
            </div>
            <div class="map-layout">
                <article class="map-panel">
                    <iframe
                        title="{{ __('messages.about.map_title') }}"
                        src="https://www.openstreetmap.org/export/embed.html?bbox=115.091%2C-8.788%2C115.344%2C-8.552&layer=mapnik&marker=-8.6705%2C115.2126"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </article>
                <article class="map-copy">
                    <h3>{{ __('messages.about.served_area') }}</h3>
                    <ul class="check-list">
                        <li>{{ __('messages.about.area_1') }}</li>
                        <li>{{ __('messages.about.area_2') }}</li>
                        <li>{{ __('messages.about.area_3') }}</li>
                        <li>{{ __('messages.about.area_4') }}</li>
                    </ul>
                    <div class="section-tight">
                        <a class="button button-primary" href="{{ route('tracking', ['tab' => 'lokasi']) }}">{{ __('messages.about.check_branch') }}</a>
                        <a class="button button-secondary" href="https://www.openstreetmap.org/?mlat=-8.6705&mlon=115.2126#map=11/-8.6705/115.2126">{{ __('messages.about.open_map') }}</a>
                    </div>
                </article>
            </div>
        </section>
    </section>
@endsection
