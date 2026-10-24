@extends('admin.layouts.app')

@section('title', $entity->exists ? 'Edit ' . $catalogLabel : 'Add ' . $catalogLabel)

@section('content')
    <header class="mb-7">
        <a class="admin-link text-sm" href="{{ route($indexRoute) }}">← Back to {{ strtolower($catalogLabel) }} catalog</a>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ $entity->exists ? 'Edit ' . $catalogLabel : 'Add ' . $catalogLabel }}</h1>
        <p class="admin-muted mt-2">Keep the catalog unpublished until the offer, license, delivery and support terms are accurate.</p>
    </header>

    @if (session('status'))<p class="admin-status admin-flash" role="status">{{ session('status') }}</p>@endif
    @if ($errors->any())
        <div class="admin-error admin-flash" role="alert"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ $formAction }}" class="admin-card">
        @csrf
        @if ($httpMethod !== 'POST') @method($httpMethod) @endif

        @foreach (['en' => 'English content', 'ar' => 'المحتوى العربي'] as $locale => $localeLabel)
            <h2 class="admin-section-heading">{{ $localeLabel }}</h2>
            @foreach ($localizedFields as $field)
                @php($fieldName = $field['key'] . '_' . $locale)
                @php($fieldLabel = $locale === 'ar' ? $field['label_ar'] : $field['label_en'])
                <div class="admin-field">
                    <label for="{{ $fieldName }}" class="font-medium">{{ $fieldLabel }}</label>
                    @if ($field['type'] === 'textarea')
                        <textarea id="{{ $fieldName }}" name="{{ $fieldName }}" maxlength="{{ $field['max'] }}" class="admin-textarea" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">{{ old($fieldName, $entity->{$fieldName}) }}</textarea>
                    @else
                        <input id="{{ $fieldName }}" name="{{ $fieldName }}" maxlength="{{ $field['max'] }}" value="{{ old($fieldName, $entity->{$fieldName}) }}" class="admin-input" dir="{{ $field['key'] === 'slug' ? 'ltr' : ($locale === 'ar' ? 'rtl' : 'ltr') }}">
                    @endif
                    @error($fieldName)<p class="admin-error">{{ $message }}</p>@enderror
                </div>
            @endforeach
        @endforeach

        @include('admin.partials.cover-image-field', ['entity' => $entity])

        <h2 class="admin-section-heading">Preview and publication</h2>
        <div class="admin-field">
            <label for="{{ $urlField }}" class="font-medium">{{ $urlLabel }}</label>
            <input id="{{ $urlField }}" name="{{ $urlField }}" type="url" maxlength="2048" value="{{ old($urlField, $entity->{$urlField}) }}" class="admin-input" placeholder="https://example.com">
            @error($urlField)<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        @if ($catalogLabel === 'Website template')
            <div class="admin-field">
                <label for="demo_url" class="font-medium">Live demo URL (optional)</label>
                <input id="demo_url" name="demo_url" type="url" maxlength="2048" value="{{ old('demo_url', $entity->demo_url) }}" class="admin-input" placeholder="https://example.com/demo">
                @error('demo_url')<p class="admin-error">{{ $message }}</p>@enderror
            </div>
        @endif
        <div class="admin-grid">
            <div class="admin-field">
                <label for="status" class="font-medium">Status</label>
                <select id="status" name="status" class="admin-select" required>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(old('status', $entity->status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>
                @error('status')<p class="admin-error">{{ $message }}</p>@enderror
            </div>
            <div class="admin-field">
                <label for="sort_order" class="font-medium">Display order</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="65535" value="{{ old('sort_order', $entity->sort_order ?? 0) }}" class="admin-input" required>
                @error('sort_order')<p class="admin-error">{{ $message }}</p>@enderror
            </div>
            <div class="admin-field">
                <label for="published_at" class="font-medium">Publish at (optional, server timezone)</label>
                <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $entity->published_at?->format('Y-m-d\TH:i')) }}" class="admin-input">
                @error('published_at')<p class="admin-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <p class="admin-muted text-sm">No price is assumed. Public purchase/checkout is not enabled; use the future request-a-quote/demo flow until licensing and delivery are fully defined.</p>
        <div class="mt-6 flex flex-wrap items-center gap-3"><button type="submit" class="admin-button">Save {{ strtolower($catalogLabel) }}</button><a href="{{ route($indexRoute) }}" class="admin-link">Cancel</a></div>
    </form>
@endsection
