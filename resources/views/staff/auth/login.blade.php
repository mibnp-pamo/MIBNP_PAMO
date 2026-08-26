@extends('layouts.staff')

@section('title', 'Staff sign in')

@section('content')
    <div class="staff-login-shell">
        <section class="staff-login-card" aria-labelledby="staff-login-title">
            <div class="staff-login-brand">
                <img src="{{ \App\Support\PublicSiteContent::optimizedAsset('logo.png') }}" alt="Mts. Iglit-Baco Natural Park logo">
                <p>PAMO publishing</p>
            </div>

            <div>
                <p class="staff-eyebrow">Restricted access</p>
                <h1 id="staff-login-title">Staff sign in</h1>
                <p class="staff-muted">Use an authorized administrator account to manage public news and advisories.</p>
            </div>

            @if ($errors->any())
                <div class="staff-alert staff-alert-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="staff-form" method="POST" action="{{ route('staff.login.store') }}">
                @csrf

                <label class="staff-field">
                    <span>Username</span>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        autocomplete="username"
                        maxlength="80"
                        required
                        autofocus
                    >
                </label>

                <label class="staff-field">
                    <span>Password</span>
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>

                <label class="staff-check">
                    <input type="checkbox" name="remember" value="1">
                    <span>Keep me signed in on this computer</span>
                </label>

                <button class="staff-button staff-button-primary staff-button-full" type="submit">Sign in</button>
            </form>

            <p class="staff-login-note">This page is intentionally not linked from the public website.</p>
        </section>
    </div>
@endsection
