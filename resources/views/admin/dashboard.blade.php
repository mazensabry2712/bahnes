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
        <p class="admin-muted mt-2">Manage the current CMS modules. Other areas will be added in tested milestones.</p>
        <div class="mt-5 flex flex-wrap gap-3">
            @if (auth()->user()->hasPermissionTo('cms.services.view'))<a class="admin-button" href="{{ route('admin.services.index') }}">Services</a>@endif
            @if (auth()->user()->hasPermissionTo('cms.projects.view'))<a class="admin-button" href="{{ route('admin.projects.index') }}">Projects</a>@endif
            @if (auth()->user()->hasPermissionTo('cms.products.view'))<a class="admin-button" href="{{ route('admin.products.index') }}">Products</a>@endif
            @if (auth()->user()->hasPermissionTo('cms.templates.view'))<a class="admin-button" href="{{ route('admin.templates.index') }}">Website templates</a>@endif
            @if (auth()->user()->hasPermissionTo('cms.insights.view'))<a class="admin-button" href="{{ route('admin.insights.index') }}">Insights</a>@endif
            @if (auth()->user()->hasPermissionTo('cms.insights.view'))<a class="admin-button" href="{{ route('admin.insight-categories.index') }}">Insight categories</a>@endif
            @if (auth()->user()->hasPermissionTo('inquiries.view'))<a class="admin-button" href="{{ route('admin.inquiries.index') }}">Customer inquiries</a>@endif
            @if (auth()->user()->hasPermissionTo('clients.view'))<a class="admin-button" href="{{ route('admin.clients.index') }}">Clients & delivery</a>@endif
            @if (auth()->user()->is_admin)<a class="admin-button" href="{{ route('admin.access.index') }}">Staff & permissions</a>@endif
        </div>
    </section>
@endsection
