@extends('admin.layouts.app')

@section('title', 'Insights')

@section('content')
    <header class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES IT SOLUTIONS / EDITORIAL</p>
            <h1 class="text-3xl font-semibold tracking-tight">Insights</h1>
            <p class="admin-muted mt-2">Manage bilingual articles without publishing incomplete translations.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="admin-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="admin-link" href="{{ route('admin.insight-categories.index') }}">Categories</a>
            <a class="admin-button" href="{{ route('admin.insights.create') }}">Add Insight</a>
        </div>
    </header>
    @if (session('status'))<p class="admin-status admin-flash" role="status">{{ session('status') }}</p>@endif
    <section class="admin-card">
        <form method="GET" action="{{ route('admin.insights.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
            <div class="admin-field" style="flex:1 1 15rem;margin:0;"><label for="q" class="font-medium">Search</label><input id="q" name="q" value="{{ $search }}" class="admin-input" placeholder="Title, slug or summary"></div>
            <div class="admin-field" style="flex:0 1 11rem;margin:0;"><label for="status" class="font-medium">Status</label><select id="status" name="status" class="admin-select"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected($selectedStatus === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <div class="admin-field" style="flex:0 1 14rem;margin:0;"><label for="category" class="font-medium">Category</label><select id="category" name="category" class="admin-select"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected($selectedCategory === (string) $category->id)>{{ $category->name_en }} / {{ $category->name_ar }}</option>@endforeach</select></div>
            <button type="submit" class="admin-button">Filter</button><a href="{{ route('admin.insights.index') }}" class="admin-link">Reset</a>
        </form>
        <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Article</th><th>Category</th><th>Status</th><th>Updated</th><th></th></tr></thead><tbody>
        @forelse ($insights as $insight)
            <tr><td><a class="font-semibold admin-link" href="{{ route('admin.insights.edit', $insight) }}">{{ $insight->title_en ?: $insight->title_ar ?: 'Untitled draft' }}</a><p class="admin-muted mt-1 text-sm">{{ $insight->title_en && $insight->title_ar ? 'EN + AR' : ($insight->title_en ? 'English only' : ($insight->title_ar ? 'Arabic only' : 'No title yet')) }}</p></td><td>{{ $insight->category?->name_en ?? 'Unknown' }}</td><td><span class="admin-badge {{ $insight->status === 'published' ? 'admin-badge-published' : ($insight->status === 'archived' ? 'admin-badge-archived' : '') }}">{{ ucfirst($insight->status) }}</span></td><td class="admin-muted">{{ $insight->updated_at?->format('Y-m-d H:i') }}</td><td><a class="admin-link" href="{{ route('admin.insights.edit', $insight) }}">Edit</a></td></tr>
        @empty
            <tr><td colspan="5" class="admin-muted" style="padding-block:2.5rem;text-align:center;">No Insights yet. Add reviewed articles only; there are no sample posts.</td></tr>
        @endforelse
        </tbody></table></div>
        <div class="mt-5">{{ $insights->links() }}</div>
    </section>
@endsection
