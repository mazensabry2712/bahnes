@extends('admin.layouts.app')

@section('title', 'Clients')

@section('content')
<header class="mb-7 flex flex-wrap items-start justify-between gap-4">
    <div><p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES / DELIVERY</p><h1 class="text-3xl font-semibold tracking-tight">Clients</h1><p class="admin-muted mt-2">Private client records and delivery projects.</p></div>
    <div class="flex gap-3"><a class="admin-link" href="{{ route('admin.dashboard') }}">Dashboard</a><a class="admin-button" href="{{ route('admin.clients.create') }}">Add client</a></div>
</header>
@if (session('status'))<p class="admin-status mb-5" role="status">{{ session('status') }}</p>@endif
<section class="admin-card">
<form method="GET" class="mb-5 flex flex-wrap items-end gap-3"><div class="admin-field" style="flex:1 1 18rem;margin:0"><label for="q">Search clients</label><input class="admin-input" id="q" name="q" value="{{ $search }}" placeholder="Name, company, email"></div><button class="admin-button" type="submit">Search</button></form>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Client</th><th>Company</th><th>Email</th><th>Projects</th><th></th></tr></thead><tbody>
@forelse ($clients as $client)
<tr><td>{{ $client->name }}</td><td>{{ $client->company ?: '—' }}</td><td>{{ $client->email }}</td><td>{{ $client->projects_count }}</td><td><a class="admin-link" href="{{ route('admin.clients.show', $client) }}">Open record</a></td></tr>
@empty<tr><td colspan="5" class="admin-muted">No client records yet.</td></tr>@endforelse
</tbody></table></div><div class="mt-5">{{ $clients->links() }}</div>
</section>
@endsection
