@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
    <header class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div><p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES IT SOLUTIONS / CATALOG</p><h1 class="text-3xl font-semibold tracking-tight">Products</h1><p class="admin-muted mt-2">Software products and business solutions, separate from client project work.</p></div>
        <div class="flex flex-wrap gap-3"><a class="admin-link" href="{{ route('admin.dashboard') }}">Dashboard</a><a class="admin-link" href="{{ route('admin.templates.index') }}">Website templates</a><a class="admin-button" href="{{ route('admin.products.create') }}">Add product</a></div>
    </header>

    <section class="admin-card">
        <form method="GET" action="{{ route('admin.products.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
            <div class="admin-field" style="flex: 1 1 15rem; margin: 0;"><label for="q" class="font-medium">Search</label><input id="q" name="q" value="{{ $search }}" class="admin-input" placeholder="Product or slug"></div>
            <div class="admin-field" style="flex: 0 1 12rem; margin: 0;"><label for="status" class="font-medium">Status</label><select id="status" name="status" class="admin-select"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected($selectedStatus === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <button type="submit" class="admin-button">Filter</button><a href="{{ route('admin.products.index') }}" class="admin-link">Reset</a>
        </form>
        <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Product</th><th>Publication</th><th>Updated</th><th></th></tr></thead><tbody>
            @forelse ($products as $product)
                <tr><td><a class="font-semibold admin-link" href="{{ route('admin.products.edit', $product) }}">{{ $product->title_en ?: $product->title_ar ?: 'Untitled product' }}</a><p class="admin-muted mt-1 text-sm">{{ $product->title_en && $product->title_ar ? 'EN + AR' : ($product->title_en ? 'English only' : ($product->title_ar ? 'Arabic only' : 'No title yet')) }}</p></td><td><span class="admin-badge {{ $product->status === 'published' ? 'admin-badge-published' : ($product->status === 'archived' ? 'admin-badge-archived' : '') }}">{{ ucfirst($product->status) }}</span></td><td class="admin-muted">{{ $product->updated_at?->format('Y-m-d H:i') }}</td><td><a class="admin-link" href="{{ route('admin.products.edit', $product) }}">Edit</a></td></tr>
            @empty
                <tr><td colspan="4" class="admin-muted" style="padding-block:2.5rem;text-align:center;">No products yet. Add real offers only; pricing and checkout are not assumed.</td></tr>
            @endforelse
        </tbody></table></div>
        <div class="mt-5">{{ $products->links() }}</div>
    </section>
@endsection
