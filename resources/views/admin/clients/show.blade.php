@extends('admin.layouts.app')

@section('title', $client->name)

@section('content')
<header class="mb-7 flex flex-wrap items-start justify-between gap-4"><div><a class="admin-link" href="{{ route('admin.clients.index') }}">← Clients</a><h1 class="mt-3 text-3xl font-semibold">{{ $client->name }}</h1><p class="admin-muted mt-2">{{ $client->company ?: 'Individual client' }} · {{ $client->email }}</p></div><a class="admin-button" href="{{ route('admin.delivery-projects.create', $client) }}">Add delivery project</a></header>
@if (session('status'))<p class="admin-status mb-5" role="status">{{ session('status') }}</p>@endif
<section class="admin-card mb-6"><h2 class="text-xl font-semibold">Client record</h2><dl class="mt-4 grid gap-4 sm:grid-cols-2"><div><dt class="admin-muted text-sm">Phone</dt><dd>{{ $client->phone ?: '—' }}</dd></div><div><dt class="admin-muted text-sm">Account manager</dt><dd>{{ $client->accountManager?->name ?: 'Unassigned' }}</dd></div></dl><div class="admin-field"><span class="font-medium">Internal notes — staff only</span><p class="whitespace-pre-line">{{ $client->internal_notes ?: 'No internal notes.' }}</p></div></section>
<section class="admin-card"><h2 class="text-xl font-semibold">Delivery projects</h2><div class="admin-table-wrap mt-4"><table class="admin-table"><thead><tr><th>Project</th><th>Status</th><th>Progress</th><th>Target date</th><th></th></tr></thead><tbody>
@forelse ($client->projects as $project)<tr><td>{{ $project->title }}</td><td>{{ str_replace('_',' ',ucfirst($project->status)) }}</td><td>{{ $project->progress_percent }}%</td><td>{{ $project->target_date?->format('M j, Y') ?: '—' }}</td><td><a class="admin-link" href="{{ route('admin.delivery-projects.show', $project) }}">Manage</a></td></tr>
@empty<tr><td colspan="5" class="admin-muted">No delivery projects recorded for this client.</td></tr>@endforelse
</tbody></table></div></section>
@endsection
