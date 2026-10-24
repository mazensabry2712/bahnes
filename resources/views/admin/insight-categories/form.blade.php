@extends('admin.layouts.app')

@section('title', $category->exists ? 'Edit Insight category' : 'Add Insight category')

@section('content')
    <header class="mb-7"><a class="admin-link text-sm" href="{{ route('admin.insight-categories.index') }}">← Back to categories</a><h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ $category->exists ? 'Edit Insight category' : 'Add Insight category' }}</h1></header>
    @if (session('status'))<p class="admin-status admin-flash" role="status">{{ session('status') }}</p>@endif
    @if ($errors->any())<div class="admin-error admin-flash" role="alert"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ $formAction }}" class="admin-card">
        @csrf @if ($httpMethod !== 'POST') @method($httpMethod) @endif
        <div class="admin-grid">
            <div class="admin-field"><label for="name_en" class="font-medium">English name</label><input id="name_en" name="name_en" value="{{ old('name_en', $category->name_en) }}" maxlength="120" class="admin-input" required>@error('name_en')<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="slug_en" class="font-medium">English URL slug</label><input id="slug_en" name="slug_en" value="{{ old('slug_en', $category->slug_en) }}" maxlength="140" class="admin-input" required dir="ltr">@error('slug_en')<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="name_ar" class="font-medium">الاسم بالعربية</label><input id="name_ar" name="name_ar" value="{{ old('name_ar', $category->name_ar) }}" maxlength="120" class="admin-input" required dir="rtl">@error('name_ar')<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="slug_ar" class="font-medium">رابط التصنيف (حروف إنجليزية)</label><input id="slug_ar" name="slug_ar" value="{{ old('slug_ar', $category->slug_ar) }}" maxlength="140" class="admin-input" required dir="ltr">@error('slug_ar')<p class="admin-error">{{ $message }}</p>@enderror</div>
        </div>
        <div class="admin-grid">
            <div class="admin-field"><label for="sort_order" class="font-medium">Display order</label><input id="sort_order" name="sort_order" type="number" min="0" max="65535" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="admin-input" required>@error('sort_order')<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label class="flex items-center gap-2 font-medium"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))><span>Category is active</span></label><p class="admin-muted text-sm">Inactive categories hide their articles from the public published scope.</p>@error('is_active')<p class="admin-error">{{ $message }}</p>@enderror</div>
        </div>
        <div class="mt-6 flex flex-wrap gap-3"><button type="submit" class="admin-button">Save category</button><a href="{{ route('admin.insight-categories.index') }}" class="admin-link">Cancel</a></div>
    </form>
@endsection
