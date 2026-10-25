@extends('admin.layouts.app')

@section('title', $project->title)

@section('content')
<header class="mb-7"><a class="admin-link" href="{{ route('admin.clients.show', $project->client) }}">← {{ $project->client->name }}</a><h1 class="mt-3 text-3xl font-semibold">{{ $project->title }}</h1><p class="admin-muted mt-2">Private delivery tracker · client emails are sent only on explicit action.</p></header>
@if (session('status'))<p class="admin-status mb-5" role="status">{{ session('status') }}</p>@endif
@if ($errors->has('email'))<p class="admin-error mb-5" role="alert">{{ $errors->first('email') }}</p>@endif
@if (auth()->user()->hasPermissionTo('projects.update'))
<section class="admin-card mb-6"><h2 class="text-xl font-semibold">Project details</h2><form class="mt-4" method="POST" action="{{ route('admin.delivery-projects.update', $project) }}">@csrf @method('PUT')
<div class="admin-field"><label for="title">Title</label><input class="admin-input" id="title" name="title" required value="{{ old('title',$project->title) }}"></div><div class="admin-field"><label for="description">Description</label><textarea class="admin-textarea" id="description" name="description">{{ old('description',$project->description) }}</textarea></div>
<div class="grid gap-4 sm:grid-cols-2"><div class="admin-field"><label for="status">Status</label><select class="admin-select" id="status" name="status">@foreach($statuses as $status)<option value="{{ $status }}" @selected(old('status',$project->status)===$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>@endforeach</select></div><div class="admin-field"><label for="progress_percent">Progress %</label><input class="admin-input" type="number" min="0" max="100" id="progress_percent" name="progress_percent" value="{{ old('progress_percent',$project->progress_percent) }}" required></div></div>
<div class="admin-field"><label for="owner_id">Assigned team member</label><select class="admin-select" id="owner_id" name="owner_id"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)old('owner_id',$project->owner_id)===(string)$user->id)>{{ $user->name }}</option>@endforeach</select></div>
<div class="grid gap-4 sm:grid-cols-2"><div class="admin-field"><label for="start_date">Start date</label><input class="admin-input" type="date" id="start_date" name="start_date" value="{{ old('start_date',$project->start_date?->format('Y-m-d')) }}"></div><div class="admin-field"><label for="target_date">Target date</label><input class="admin-input" type="date" id="target_date" name="target_date" value="{{ old('target_date',$project->target_date?->format('Y-m-d')) }}"></div></div>
<div class="admin-field"><label for="internal_notes">Internal notes (never emailed)</label><textarea class="admin-textarea" id="internal_notes" name="internal_notes">{{ old('internal_notes',$project->internal_notes) }}</textarea></div><button class="admin-button" type="submit">Save project</button></form></section>
@endif
@if (auth()->user()->hasPermissionTo('project_updates.create'))
<section class="admin-card mb-6"><h2 class="text-xl font-semibold">Add progress update</h2><p class="admin-muted mt-2">Save updates first. Only client-visible updates can be emailed, and sending is a separate explicit action.</p><form method="POST" action="{{ route('admin.delivery-projects.updates.store',$project) }}">@csrf
<div class="admin-field"><label for="summary">Summary *</label><textarea class="admin-textarea" id="summary" name="summary" required>{{ old('summary') }}</textarea>@error('summary')<span class="admin-error">{{ $message }}</span>@enderror</div>
@foreach(['completed_work'=>'Completed work','current_work'=>'Currently working on','next_step'=>'Next step'] as $field=>$label)<div class="admin-field"><label for="{{ $field }}">{{ $label }}</label><textarea class="admin-textarea" id="{{ $field }}" name="{{ $field }}">{{ old($field) }}</textarea></div>@endforeach
<div class="admin-field"><label for="update_target_date">Target date</label><input class="admin-input" type="date" id="update_target_date" name="target_date" value="{{ old('target_date') }}"></div>
<label class="flex items-center gap-2"><input type="checkbox" name="client_visible" value="1" @checked(old('client_visible'))> Approved to share with client</label><p class="admin-muted mt-2 text-sm">Internal notes and project costs are never included in the email template.</p><button class="admin-button mt-5" type="submit">Save update</button></form></section>
@endif
<section class="admin-card"><h2 class="text-xl font-semibold">Progress history</h2><div class="mt-4 grid gap-4">
@forelse($project->updates->sortByDesc('created_at') as $update)<article class="rounded-lg border p-4"><div class="flex flex-wrap items-start justify-between gap-3"><div><p class="font-semibold">{{ $update->summary }}</p><p class="admin-muted mt-1 text-sm">{{ $update->created_at->format('M j, Y H:i') }} · {{ $update->author?->name ?: 'Former user' }}</p></div><span class="admin-muted text-sm">{{ $update->client_visible ? 'Client-visible' : 'Internal only' }}</span></div>
@if($update->completed_work)<p class="mt-3"><strong>Completed:</strong> {{ $update->completed_work }}</p>@endif @if($update->current_work)<p class="mt-2"><strong>In progress:</strong> {{ $update->current_work }}</p>@endif @if($update->next_step)<p class="mt-2"><strong>Next:</strong> {{ $update->next_step }}</p>@endif
@if($update->email_sent_at)<p class="mt-3 text-sm">Email sent {{ $update->email_sent_at->format('M j, Y H:i') }}</p>@elseif($update->client_visible && auth()->user()->hasPermissionTo('project_updates.send'))<form class="mt-3" method="POST" action="{{ route('admin.project-updates.send',$update) }}">@csrf<button class="admin-button" type="submit">Send update to {{ $project->client->email }}</button></form>@endif
@if($update->email_error)<p class="admin-error mt-2">{{ $update->email_error }}</p>@endif</article>
@empty<p class="admin-muted">No progress updates yet.</p>@endforelse
</div></section>
@endsection
