@extends('admin.layouts.app')

@section('title', 'Administrator sign in')

@section('content')
    <header class="mb-8">
        <p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES IT SOLUTIONS</p>
        <h1 class="text-3xl font-semibold tracking-tight">Administrator sign in</h1>
        <p class="admin-muted mt-2">This area is restricted to authorized administrators.</p>
    </header>

    <section class="admin-card">
        @if (session('status'))
            <p class="admin-status mb-4" role="status">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('admin.authenticate') }}">
            @csrf

            <div class="admin-field">
                <label for="email" class="font-medium">Email address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    class="admin-input"
                    autocomplete="username"
                    inputmode="email"
                    maxlength="255"
                    required
                    autofocus
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                >
                @error('email')
                    <p id="email-error" class="admin-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="admin-field">
                <label for="password" class="font-medium">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="admin-input"
                    autocomplete="current-password"
                    required
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                >
                @error('password')
                    <p id="password-error" class="admin-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="admin-button mt-4 w-full">Sign in securely</button>
        </form>
    </section>

    <p class="admin-muted mt-5 text-sm">Administrator accounts are provisioned through the server command line. Public registration is disabled.</p>
@endsection
