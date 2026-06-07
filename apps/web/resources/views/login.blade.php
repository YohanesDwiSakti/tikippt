@extends('layouts.app')

@section('title', __('messages.login.title'))

@section('content')
    <section class="page-shell section" style="max-width: 720px;">
        <p class="eyebrow">{{ __('messages.login.eyebrow') }}</p>
        <h1 style="font-size: 48px;">{{ __('messages.login.heading') }}</h1>
        <p class="lead">{{ __('messages.login.copy') }}</p>

        @if(session('auth_role'))
            <div class="notice notice-success">
                {{ __('messages.login.logged_in', ['name' => session('auth_name')]) }}
            </div>
        @endif

        @if(session('auth_notice'))
            <div class="notice">
                {{ session('auth_notice') }}
            </div>
        @endif

        @if($errors->any())
            <div class="notice notice-danger">
                {{ __('messages.login.error') }}
            </div>
        @endif

        <form class="panel form-grid" method="POST" action="{{ route('login.submit') }}">
            @csrf
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            <div class="field full">
                <label>Email</label>
                <input class="input" name="email" type="email" value="{{ old('email') }}" required>
            </div>
            <div class="field full">
                <label>Password</label>
                <input class="input" name="password" type="password" required>
            </div>
            <div class="field full">
                <label>{{ __('messages.login.role') }}</label>
                <select class="select" name="role" required>
                    <option value="admin" @selected(old('role', 'admin') === 'admin')>Admin</option>
                    <option value="driver" @selected(old('role') === 'driver')>Driver</option>
                </select>
            </div>
            <div class="full" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button class="button button-primary" type="submit">{{ __('messages.login.submit') }}</button>
                @if(session('auth_role') === 'admin')
                    <a class="button button-secondary" href="{{ route('admin.dashboard') }}">{{ __('messages.login.open_admin') }}</a>
                @elseif(session('auth_role') === 'driver')
                    <a class="button button-secondary" href="{{ route('driver.index') }}">{{ __('messages.login.open_driver') }}</a>
                @endif
            </div>
        </form>
    </section>
@endsection
