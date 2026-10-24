@extends('admin.layouts.app')

@section('title', 'Services')

@section('content')
    <header class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES IT SOLUTIONS / CMS</p>
            <h1 class="text-3xl font-semibold tracking-tight">Services</h1>
            <p class="admin-muted mt-2">Manage offers under the two service lines.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a class="admin-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="admin-link" href="{{ route('admin.projects.index') }}">Projects</a>
            <a class="admin-button" href="{{ route('admin.services.create') }}">Add service</a>
        </div>
    </header>

    @if (session('status'))
        <p class="admin-status admin-flash" role="status">{{ session('status') }}</p>
    @endif

    <section class="admin-card">
        <form method="GET" action="{{ route('admin.services.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
            <div class="admin-field" style="flex: 1 1 16rem; margin: 0;">
                <label for="q" class="font-medium">Search</label>
                <input id="q" name="q" value="{{ $search }}" class="admin-input" placeholder="English or Arabic title / slug">
            </div>
            <div class="admin-field" style="flex: 0 1 12rem; margin: 0;">
                <label for="status" class="font-medium">Publication status</label>
                <select id="status" name="status" class="admin-select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="admin-button">Filter</button>
            <a href="{{ route('admin.services.index') }}" class="admin-link">Reset</a>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Service line</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td>
                                <a class="font-semibold admin-link" href="{{ route('admin.services.edit', $service) }}">
                                    {{ $service->title_en ?: $service->title_ar ?: 'Untitled draft' }}
                                </a>
                                <p class="admin-muted mt-1 text-sm">
                                    @if ($service->title_en && $service->title_ar)
                                        EN + AR
                                    @elseif ($service->title_en)
                                        English only
                                    @elseif ($service->title_ar)
                                        Arabic only
                                    @else
                                        No title yet
                                    @endif
                                </p>
                            </td>
                            <td>{{ $service->serviceLine?->name_en ?? 'Unknown' }}</td>
                            <td>
                                <span class="admin-badge {{ $service->status === 'published' ? 'admin-badge-published' : ($service->status === 'archived' ? 'admin-badge-archived' : '') }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                            <td class="admin-muted">{{ $service->updated_at?->format('Y-m-d H:i') }}</td>
                            <td><a class="admin-link" href="{{ route('admin.services.edit', $service) }}">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="admin-muted" style="padding-block: 2.5rem; text-align: center;">No services yet. Add a draft and publish it when ready.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $services->links() }}</div>
    </section>
@endsection
