@extends('admin.layouts.app')

@section('title', 'Add service')

@section('content')
    <header class="mb-7">
        <a class="admin-link text-sm" href="{{ route('admin.services.index') }}">← Back to services</a>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight">Add service</h1>
        <p class="admin-muted mt-2">Start with a draft. Each language has its own URL slug and SEO fields.</p>
    </header>

    @include('admin.services._form', ['formAction' => route('admin.services.store'), 'httpMethod' => 'POST'])
@endsection
