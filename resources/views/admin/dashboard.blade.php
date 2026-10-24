@extends('admin.layouts.app')

@section('title', 'Administration')

@section('content')
    <header class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES IT SOLUTIONS</p>
            <h1 class="text-3xl font-semibold tracking-tight">Administration</h1>
            <p class="admin-muted mt-2">Signed in as {{ auth()->user()->name }}.</p>
        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="admin-button">Sign out</button>
        </form>
    </header>

    @if (session('status'))
        <p class="admin-status admin-flash" role="status">{{ session('status') }}</p>
    @endif

    <section class="admin-card">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">Content management</h2>
                <p class="admin-muted mt-1">Manage the first CMS content type.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.services.index') }}">Manage services</a>
        </div>
        <h2 class="text-xl font-semibold">Secure access is ready</h2>
        <p class="admin-muted mt-2 leading-7">Service management is the first content module. Other CMS modules will be added in separate, tested backend milestones.</p>
    </section>
@endsection
