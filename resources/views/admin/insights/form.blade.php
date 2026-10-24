@extends('admin.layouts.app')

@section('title', $insight->exists ? 'Edit Insight' : 'Add Insight')

@section('content')
    <header class="mb-7"><a class="admin-link text-sm" href="{{ route('admin.insights.index') }}">← Back to Insights</a><h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ $insight->exists ? 'Edit Insight' : 'Add Insight' }}</h1><p class="admin-muted mt-2">Use reviewed copy. The article body is plain text and will be escaped when rendered publicly.</p></header>
    @if (session('status'))<p class="admin-status admin-flash" role="status">{{ session('status') }}</p>@endif
    @if ($errors->any())<div class="admin-error admin-flash" role="alert"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" enctype="multipart/form-data" action="{{ $formAction }}" class="admin-card">
        @csrf
        @if ($httpMethod !== 'POST') @method($httpMethod) @endif
        <div class="admin-field"><label for="insight_category_id" class="font-medium">Category</label><select id="insight_category_id" name="insight_category_id" class="admin-select" required><option value="">Choose a category</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) old('insight_category_id', $insight->insight_category_id) === (string) $category->id)>{{ $category->name_en }} — {{ $category->name_ar }}{{ $category->is_active ? '' : ' (inactive)' }}</option>@endforeach</select>@error('insight_category_id')<p class="admin-error">{{ $message }}</p>@enderror</div>
        @foreach (['en' => 'English content', 'ar' => 'المحتوى العربي'] as $locale => $localeLabel)
            <h2 class="admin-section-heading">{{ $localeLabel }}</h2>
            <div class="admin-field"><label for="title_{{ $locale }}" class="font-medium">Title</label><input id="title_{{ $locale }}" name="title_{{ $locale }}" maxlength="180" value="{{ old('title_'.$locale, $insight->{'title_'.$locale}) }}" class="admin-input" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">@error('title_'.$locale)<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="slug_{{ $locale }}" class="font-medium">URL slug (ASCII, lowercase, hyphens)</label><input id="slug_{{ $locale }}" name="slug_{{ $locale }}" maxlength="180" value="{{ old('slug_'.$locale, $insight->{'slug_'.$locale}) }}" class="admin-input" dir="ltr">@error('slug_'.$locale)<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="excerpt_{{ $locale }}" class="font-medium">Summary</label><textarea id="excerpt_{{ $locale }}" name="excerpt_{{ $locale }}" maxlength="2000" class="admin-textarea" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">{{ old('excerpt_'.$locale, $insight->{'excerpt_'.$locale}) }}</textarea>@error('excerpt_'.$locale)<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="body_{{ $locale }}" class="font-medium">Article body</label><textarea id="body_{{ $locale }}" name="body_{{ $locale }}" maxlength="100000" class="admin-textarea" style="min-height:20rem" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">{{ old('body_'.$locale, $insight->{'body_'.$locale}) }}</textarea>@error('body_'.$locale)<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-grid">
                <div class="admin-field"><label for="meta_title_{{ $locale }}" class="font-medium">SEO title</label><input id="meta_title_{{ $locale }}" name="meta_title_{{ $locale }}" maxlength="180" value="{{ old('meta_title_'.$locale, $insight->{'meta_title_'.$locale}) }}" class="admin-input" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">@error('meta_title_'.$locale)<p class="admin-error">{{ $message }}</p>@enderror</div>
                <div class="admin-field"><label for="meta_description_{{ $locale }}" class="font-medium">SEO description</label><textarea id="meta_description_{{ $locale }}" name="meta_description_{{ $locale }}" maxlength="320" class="admin-textarea" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">{{ old('meta_description_'.$locale, $insight->{'meta_description_'.$locale}) }}</textarea>@error('meta_description_'.$locale)<p class="admin-error">{{ $message }}</p>@enderror</div>
            </div>
        @endforeach
        @include('admin.partials.cover-image-field', ['entity' => $insight])
        <h2 class="admin-section-heading">Publication</h2>
        <div class="admin-grid">
            <div class="admin-field"><label for="status" class="font-medium">Status</label><select id="status" name="status" class="admin-select" required>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(old('status', $insight->status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>@error('status')<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="sort_order" class="font-medium">Display order</label><input id="sort_order" name="sort_order" type="number" min="0" max="65535" value="{{ old('sort_order', $insight->sort_order ?? 0) }}" class="admin-input" required>@error('sort_order')<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="published_at" class="font-medium">Publish at (optional, server timezone)</label><input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $insight->published_at?->format('Y-m-d\TH:i')) }}" class="admin-input">@error('published_at')<p class="admin-error">{{ $message }}</p>@enderror</div>
        </div>
        <div class="mt-6 flex flex-wrap items-center gap-3"><button type="submit" class="admin-button">Save Insight</button><a href="{{ route('admin.insights.index') }}" class="admin-link">Cancel</a></div>
    </form>
@endsection
