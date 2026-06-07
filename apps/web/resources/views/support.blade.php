@extends('layouts.app')

@section('title', __('messages.support.title'))

@section('content')
    <section class="page-shell section">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ __('messages.support.title') }}</p>
                <h1 style="font-size: 48px;">{{ __('messages.support.heading') }}</h1>
                <p class="lead">{{ __('messages.support.copy') }}</p>
            </div>
        </div>

        <div class="support-layout">
            <article class="support-block">
                <span class="badge badge-brand">{{ __('messages.support.customer_care') }}</span>
                <h2 style="margin-top: 16px;">{{ __('messages.support.main_contact') }}</h2>
                <div class="contact-list">
                    <a href="tel:+62361000101"><span>{{ __('messages.support.phone') }}</span><strong>0361 000 101</strong></a>
                    <a href="https://wa.me/6281234567801"><span>WhatsApp</span><strong>0812 3456 7801</strong></a>
                    <a href="mailto:support@finproppt.test"><span>Email</span><strong>support@finproppt.test</strong></a>
                    <a href="https://instagram.com/tikidenpasar"><span>Instagram</span><strong>@tikidenpasar</strong></a>
                </div>
            </article>

            <article class="support-note">
                <h2>{{ __('messages.support.service_hours') }}</h2>
                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="meta-label">{{ __('messages.support.weekday') }}</div>
                        <div class="meta-value">08.00-17.00 WITA</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">{{ __('messages.support.saturday') }}</div>
                        <div class="meta-value">08.00-14.00 WITA</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">{{ __('messages.support.sunday') }}</div>
                        <div class="meta-value">{{ __('messages.support.limited_monitoring') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">{{ __('messages.support.area') }}</div>
                        <div class="meta-value">Denpasar, Sanur, Gianyar, Ubud</div>
                    </div>
                </div>
            </article>
        </div>

        <section class="section-tight">
            <div class="info-list">
                <article class="info-item">
                    <span class="badge badge-brand">{{ __('messages.home.card_receipt_label') }}</span>
                    <div>
                        <h3>{{ __('messages.support.before_contact') }}</h3>
                        <p class="helper">{{ __('messages.support.before_contact_copy') }}</p>
                    </div>
                </article>
                <article class="info-item">
                    <span class="badge badge-brand">Admin</span>
                    <div>
                        <h3>{{ __('messages.support.data_fix') }}</h3>
                        <p class="helper">{{ __('messages.support.data_fix_copy') }}</p>
                    </div>
                </article>
                <article class="info-item">
                    <span class="badge badge-brand">Driver</span>
                    <div>
                        <h3>{{ __('messages.support.delivery_issue') }}</h3>
                        <p class="helper">{{ __('messages.support.delivery_issue_copy') }}</p>
                    </div>
                </article>
            </div>
        </section>
    </section>
@endsection
