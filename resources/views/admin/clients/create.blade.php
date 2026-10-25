@extends('admin.layouts.app')

@section('title', 'Add client')

@section('content')
<header class="mb-7"><a class="admin-link" href="{{ route('admin.clients.index') }}">← Clients</a><h1 class="mt-3 text-3xl font-semibold">Add client</h1><p class="admin-muted mt-2">Internal notes are private and are never included in client emails.</p></header>
<section class="admin-card"><form method="POST" action="{{ route('admin.clients.store') }}">@csrf
@foreach (['name'=>'Client contact name','company'=>'Company','email'=>'Email','phone'=>'Phone'] as $field=>$label)
<div class="admin-field"><label for="{{ $field }}">{{ $label }} @if(in_array($field,['name','email']))* @endif</label><input class="admin-input" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" @if(in_array($field,['name','email']))required @endif @if($field==='email')type="email" @endif>@error($field)<span class="admin-error">{{ $message }}</span>@enderror</div>
@endforeach
<div class="admin-field"><label for="preferred_locale">Email language</label><select class="admin-select" id="preferred_locale" name="preferred_locale"><option value="en" @selected(old('preferred_locale','en')==='en')>English</option><option value="ar" @selected(old('preferred_locale')==='ar')>Arabic</option></select></div>
<div class="admin-field"><label for="internal_notes">Internal notes (private)</label><textarea class="admin-textarea" id="internal_notes" name="internal_notes">{{ old('internal_notes') }}</textarea>@error('internal_notes')<span class="admin-error">{{ $message }}</span>@enderror</div>
<button class="admin-button" type="submit">Create client</button></form></section>
@endsection
