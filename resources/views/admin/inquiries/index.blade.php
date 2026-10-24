@extends('admin.layouts.app')

@section('title', 'Customer inquiries')

@section('content')
    <header class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES IT SOLUTIONS / SALES</p>
            <h1 class="text-3xl font-semibold tracking-tight">Customer inquiries</h1>
            <p class="admin-muted mt-2">Track requests and next steps. Internal notes are visible only to admins.</p>
        </div>
        <a class="admin-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
    </header>

    @if (session('status')) <p class="admin-status admin-flash" role="status">{{ session('status') }}</p> @endif

    <section class="admin-card">
        <form method="GET" action="{{ route('admin.inquiries.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
            <div class="admin-field" style="flex: 1 1 15rem; margin: 0;">
                <label for="q" class="font-medium">Search</label>
                <input id="q" name="q" value="{{ $search }}" class="admin-input" placeholder="Name, email, company, subject">
            </div>
            <div class="admin-field" style="flex: 0 1 12rem; margin: 0;">
                <label for="status" class="font-medium">Status</label>
                <select id="status" name="status" class="admin-select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-field" style="flex: 0 1 14rem; margin: 0;">
                <label for="type" class="font-medium">Inquiry type</label>
                <select id="type" name="type" class="admin-select">
                    <option value="">All types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected($selectedType === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="admin-button">Filter</button>
            <a href="{{ route('admin.inquiries.index') }}" class="admin-link">Reset</a>
        </form>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Contact</th><th>Type</th><th>Status</th><th>Received</th><th></th></tr></thead>
                <tbody>
                    @forelse ($inquiries as $inquiry)
                        <tr>
                            <td>
                                <a class="font-semibold admin-link" href="{{ route('admin.inquiries.edit', $inquiry) }}">{{ $inquiry->name }}</a>
                                <p class="admin-muted mt-1 text-sm">{{ $inquiry->email }}</p>
                                @if ($inquiry->company)<p class="admin-muted text-sm">{{ $inquiry->company }}</p>@endif
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $inquiry->type)) }}</td>
                            <td><span class="admin-badge {{ $inquiry->status === 'new' ? 'admin-badge-published' : ($inquiry->status === 'closed' || $inquiry->status === 'spam' ? 'admin-badge-archived' : '') }}">{{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}</span></td>
                            <td class="admin-muted">{{ $inquiry->created_at?->format('Y-m-d H:i') }}</td>
                            <td><a class="admin-link" href="{{ route('admin.inquiries.edit', $inquiry) }}">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="admin-muted" style="padding-block: 2.5rem; text-align: center;">No customer inquiries received yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $inquiries->links() }}</div>
    </section>
@endsection
