@extends('admin.layouts.app')

@section('title', 'Administration')

@section('content')
    <header class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES IT SOLUTIONS</p>
            <h1 class="text-3xl font-semibold tracking-tight">Administration</h1>
            <p class="admin-muted mt-2">Signed in as {{ auth()->user()->name }}.</p>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="admin-button">Sign out</button></form>
    </header>

    @if (session('status'))<p class="admin-status admin-flash" role="status">{{ session('status') }}</p>@endif

    <section class="admin-card">
        <h2 class="text-xl font-semibold">Content management</h2>
        <p class="admin-muted mt-2">Manage the first CMS modules. Other areas will be added in tested milestones.</p>
        <div class="mt-5 flex flex-wrap gap-3">
            <a class="admin-button" href="{{ route('admin.services.index') }}">Manage services</a>
            <a class="admin-button" href="{{ route('admin.projects.index') }}">Manage projects</a>
        </div>
    </section>
@endsection
