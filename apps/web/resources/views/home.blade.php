@extends('layouts.app')

@section('title', __('messages.home.title'))

@section('content')
    <section class="page-shell hero">
        <div>
            <p class="eyebrow">{{ __('messages.home.hero_eyebrow') }}</p>
            <h1>{{ __('messages.home.hero_title') }}</h1>
            <p class="lead">{{ __('messages.home.hero_copy') }}</p>
            <form class="tracking-form" action="{{ route('tracking') }}" method="get">
                <input type="hidden" name="tab" value="resi">
                <label class="field" style="flex: 1;">
                    <span class="meta-label">{{ __('messages.home.receipt_label') }}</span>
                    <input class="input" name="receipt" value="{{ $receipt }}" placeholder="TKI-DEN-260607101500">
                </label>
                <button class="button button-primary" type="submit">{{ __('messages.home.check_receipt') }}</button>
            </form>
        </div>

        @include('partials.status-panel', ['selected' => $selected, 'masked' => true])
    </section>

    <section class="section section-muted">
        <div class="page-shell">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">{{ __('messages.home.quick_eyebrow') }}</p>
                    <h2>{{ __('messages.home.quick_title') }}</h2>
                </div>
            </div>

            <div class="service-link-grid">
                <a class="service-link" href="{{ route('tracking', ['tab' => 'resi']) }}">
                    <span class="badge badge-brand">{{ __('messages.home.card_receipt_label') }}</span>
                    <strong>{{ __('messages.home.card_receipt_title') }}</strong>
                    <span>{{ __('messages.home.card_receipt_copy') }}</span>
                </a>
                <a class="service-link" href="{{ route('tracking', ['tab' => 'harga']) }}">
                    <span class="badge badge-brand">{{ __('messages.home.card_price_label') }}</span>
                    <strong>{{ __('messages.home.card_price_title') }}</strong>
                    <span>{{ __('messages.home.card_price_copy') }}</span>
                </a>
                <a class="service-link" href="{{ route('tracking', ['tab' => 'lokasi']) }}">
                    <span class="badge badge-brand">{{ __('messages.home.card_location_label') }}</span>
                    <strong>{{ __('messages.home.card_location_title') }}</strong>
                    <span>{{ __('messages.home.card_location_copy') }}</span>
                </a>
            </div>
        </div>
    </section>

    <section class="page-shell section">
        <div class="section-heading">
            <div>
                <p class="eyebrow">{{ __('messages.home.workflow_eyebrow') }}</p>
                <h2>{{ __('messages.home.workflow_title') }}</h2>
            </div>
        </div>
        <div class="process-grid">
            <article class="process-step">
                <span>01</span>
                <strong>{{ __('messages.home.step_1_title') }}</strong>
                <p>{{ __('messages.home.step_1_copy') }}</p>
            </article>
            <article class="process-step">
                <span>02</span>
                <strong>{{ __('messages.home.step_2_title') }}</strong>
                <p>{{ __('messages.home.step_2_copy') }}</p>
            </article>
            <article class="process-step">
                <span>03</span>
                <strong>{{ __('messages.home.step_3_title') }}</strong>
                <p>{{ __('messages.home.step_3_copy') }}</p>
            </article>
            <article class="process-step">
                <span>04</span>
                <strong>{{ __('messages.home.step_4_title') }}</strong>
                <p>{{ __('messages.home.step_4_copy') }}</p>
            </article>
        </div>
    </section>
@endsection
