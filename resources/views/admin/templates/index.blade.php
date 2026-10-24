@extends('admin.layouts.app')

@section('title', 'Website Templates')

@section('content')
    <header class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div><p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES IT SOLUTIONS / CATALOG</p><h1 class="text-3xl font-semibold tracking-tight">Website Templates</h1><p class="admin-muted mt-2">Reusable site templates have their own terms and catalog, separate from software products.</p></div>
        <div class="flex flex-wrap gap-3"><a class="admin-link" href="{{ route('admin.dashboard') }}">Dashboard</a><a class="admin-link" href="{{ route('admin.products.index') }}">Products</a><a class="admin-button" href="{{ route('admin.templates.create') }}">Add template</a></div>
    </header>

    <section class="admin-card">
        <form method="GET" action="{{ route('admin.templates.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
            <div class="admin-field" style="flex: 1 1 15rem; margin: 0;"><label for="q" class="font-medium">Search</label><input id="q" name="q" value="{{ $search }}" class="admin-input" placeholder="Template or slug"></div>
            <div class="admin-field" style="flex: 0 1 12rem; margin: 0;"><label for="status" class="font-medium">Status</label><select id="status" name="status" class="admin-select"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected($selectedStatus === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <button type="submit" class="admin-button">Filter</button><a href="{{ route('admin.templates.index') }}" class="admin-link">Reset</a>
        </form>
        <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Template</th><th>Publication</th><th>Updated</th><th></th></tr></thead><tbody>
            @forelse ($templates as $template)
                <tr><td><a class="font-semibold admin-link" href="{{ route('admin.templates.edit', $template) }}">{{ $template->title_en ?: $template->title_ar ?: 'Untitled template' }}</a><p class="admin-muted mt-1 text-sm">{{ $template->title_en && $template->title_ar ? 'EN + AR' : ($template->title_en ? 'English only' : ($template->title_ar ? 'Arabic only' : 'No title yet')) }}</p></td><td><span class="admin-badge {{ $template->status === 'published' ? 'admin-badge-published' : ($template->status === 'archived' ? 'admin-badge-archived' : '') }}">{{ ucfirst($template->status) }}</span></td><td class="admin-muted">{{ $template->updated_at?->format('Y-m-d H:i') }}</td><td><a class="admin-link" href="{{ route('admin.templates.edit', $template) }}">Edit</a></td></tr>
            @empty
                <tr><td colspan="4" class="admin-muted" style="padding-block:2.5rem;text-align:center;">No templates yet. Add real templates only when their license, delivery and support terms are ready.</td></tr>
            @endforelse
        </tbody></table></div>
        <div class="mt-5">{{ $templates->links() }}</div>
    </section>
@endsection
