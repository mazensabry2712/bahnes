@extends('admin.layouts.app')

@section('title', 'Insight categories')

@section('content')
    <header class="mb-7 flex flex-wrap items-start justify-between gap-4"><div><a class="admin-link text-sm" href="{{ route('admin.insights.index') }}">← Back to Insights</a><h1 class="mt-3 text-3xl font-semibold tracking-tight">Insight categories</h1><p class="admin-muted mt-2">An inactive category cannot be used for a published article.</p></div><a class="admin-button" href="{{ route('admin.insight-categories.create') }}">Add category</a></header>
    @if (session('status'))<p class="admin-status admin-flash" role="status">{{ session('status') }}</p>@endif
    <section class="admin-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Category</th><th>Articles</th><th>State</th><th>Order</th><th></th></tr></thead><tbody>
        @forelse ($categories as $category)
            <tr><td><a class="font-semibold admin-link" href="{{ route('admin.insight-categories.edit', $category) }}">{{ $category->name_en }} / {{ $category->name_ar }}</a><p class="admin-muted mt-1 text-sm">{{ $category->slug_en }} · {{ $category->slug_ar }}</p></td><td>{{ $category->insights_count }}</td><td><span class="admin-badge {{ $category->is_active ? 'admin-badge-published' : 'admin-badge-archived' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td><td>{{ $category->sort_order }}</td><td><a class="admin-link" href="{{ route('admin.insight-categories.edit', $category) }}">Edit</a></td></tr>
        @empty
            <tr><td colspan="5" class="admin-muted" style="padding-block:2rem;text-align:center;">No categories. Run the normal database seeder to create the initial category set.</td></tr>
        @endforelse
    </tbody></table></div><div class="mt-5">{{ $categories->links() }}</div></section>
@endsection
