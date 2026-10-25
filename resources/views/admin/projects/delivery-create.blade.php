@extends('admin.layouts.app')

@section('title', 'New delivery project')

@section('content')
<header class="mb-7"><a class="admin-link" href="{{ route('admin.clients.show', $client) }}">← {{ $client->name }}</a><h1 class="mt-3 text-3xl font-semibold">New delivery project</h1><p class="admin-muted mt-2">Internal notes are not shared with the client.</p></header>
<section class="admin-card"><form method="POST" action="{{ route('admin.delivery-projects.store', $client) }}">@csrf
<div class="admin-field"><label for="title">Project title *</label><input class="admin-input" id="title" name="title" required value="{{ old('title') }}">@error('title')<span class="admin-error">{{ $message }}</span>@enderror</div>
<div class="admin-field"><label for="description">Description</label><textarea class="admin-textarea" id="description" name="description">{{ old('description') }}</textarea></div>
<div class="grid gap-4 sm:grid-cols-2"><div class="admin-field"><label for="status">Status</label><select class="admin-select" id="status" name="status">@foreach($statuses as $status)<option value="{{ $status }}" @selected(old('status','planning')===$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>@endforeach</select></div><div class="admin-field"><label for="progress_percent">Progress %</label><input class="admin-input" type="number" min="0" max="100" id="progress_percent" name="progress_percent" value="{{ old('progress_percent',0) }}" required></div></div>
<div class="admin-field"><label for="owner_id">Assigned team member</label><select class="admin-select" id="owner_id" name="owner_id"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)old('owner_id')===(string)$user->id)>{{ $user->name }}</option>@endforeach</select></div>
<div class="grid gap-4 sm:grid-cols-2"><div class="admin-field"><label for="start_date">Start date</label><input class="admin-input" type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"></div><div class="admin-field"><label for="target_date">Target date</label><input class="admin-input" type="date" id="target_date" name="target_date" value="{{ old('target_date') }}"></div></div>
<div class="admin-field"><label for="internal_notes">Internal notes</label><textarea class="admin-textarea" id="internal_notes" name="internal_notes">{{ old('internal_notes') }}</textarea></div><button class="admin-button" type="submit">Create project</button></form></section>
@endsection
