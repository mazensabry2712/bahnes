@extends('admin.layouts.app')

@section('title', 'Review inquiry')

@section('content')
    <header class="mb-7">
        <a class="admin-link text-sm" href="{{ route('admin.inquiries.index') }}">← Back to inquiries</a>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight">Review customer inquiry</h1>
        <p class="admin-muted mt-2">Received {{ $inquiry->created_at?->format('Y-m-d H:i') }} · Source: {{ $inquiry->source }}</p>
    </header>

    @if (session('status'))<p class="admin-status admin-flash" role="status">{{ session('status') }}</p>@endif
    @if ($errors->any())
        <div class="admin-error admin-flash" role="alert"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="admin-card mb-5">
        <h2 class="text-xl font-semibold">{{ $inquiry->subject ?: ucfirst(str_replace('_', ' ', $inquiry->type)) }}</h2>
        <dl class="mt-4 grid gap-3">
            <div><dt class="admin-muted text-sm">Name</dt><dd>{{ $inquiry->name }}</dd></div>
            <div><dt class="admin-muted text-sm">Email</dt><dd><a class="admin-link" href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd></div>
            @if ($inquiry->phone)<div><dt class="admin-muted text-sm">Phone</dt><dd>{{ $inquiry->phone }}</dd></div>@endif
            @if ($inquiry->company)<div><dt class="admin-muted text-sm">Company</dt><dd>{{ $inquiry->company }}</dd></div>@endif
            <div><dt class="admin-muted text-sm">Preferred language</dt><dd>{{ strtoupper($inquiry->preferred_locale) }}</dd></div>
            <div><dt class="admin-muted text-sm">Message</dt><dd class="mt-1 whitespace-pre-line">{{ $inquiry->message }}</dd></div>
        </dl>
    </section>

    <form method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}" class="admin-card">
        @csrf
        @method('PUT')
        <div class="admin-field">
            <label for="status" class="font-medium">Follow-up status</label>
            <select id="status" name="status" class="admin-select" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $inquiry->status) === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            @error('status')<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        <div class="admin-field">
            <label for="admin_notes" class="font-medium">Internal notes (admin-only)</label>
            <textarea id="admin_notes" name="admin_notes" maxlength="10000" class="admin-textarea">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea>
            @error('admin_notes')<p class="admin-error">{{ $message }}</p>@enderror
        </div>
        <div class="mt-5 flex flex-wrap items-center gap-3"><button type="submit" class="admin-button">Save follow-up</button><a href="{{ route('admin.inquiries.index') }}" class="admin-link">Cancel</a></div>
    </form>
@endsection
