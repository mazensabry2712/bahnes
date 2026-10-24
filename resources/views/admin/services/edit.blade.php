@extends('admin.layouts.app')

@section('title', 'Edit service')

@section('content')
    <header class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a class="admin-link text-sm" href="{{ route('admin.services.index') }}">← Back to services</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight">Edit service</h1>
            <p class="admin-muted mt-2">ID #{{ $service->id }} · {{ $service->serviceLine?->name_en }}</p>
        </div>
        <span class="admin-badge {{ $service->status === 'published' ? 'admin-badge-published' : ($service->status === 'archived' ? 'admin-badge-archived' : '') }}">
            {{ ucfirst($service->status) }}
        </span>
    </header>

    @include('admin.services._form', ['formAction' => route('admin.services.update', $service), 'httpMethod' => 'PUT'])
@endsection
