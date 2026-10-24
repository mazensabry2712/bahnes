@extends('admin.layouts.app')

@section('title', 'Projects & Case Studies')

@section('content')
    <header class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="admin-muted mb-2 text-sm font-semibold uppercase tracking-wider">BAHNES IT SOLUTIONS / CMS</p>
            <h1 class="text-3xl font-semibold tracking-tight">Projects & Case Studies</h1>
            <p class="admin-muted mt-2">Publish only authorized work. Keep concepts clearly labeled and confidential projects private.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a class="admin-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="admin-button" href="{{ route('admin.projects.create') }}">Add project</a>
        </div>
    </header>

    @if (session('status')) <p class="admin-status admin-flash" role="status">{{ session('status') }}</p> @endif

    <section class="admin-card">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
            <div class="admin-field" style="flex: 1 1 15rem; margin: 0;">
                <label for="q" class="font-medium">Search</label>
                <input id="q" name="q" value="{{ $search }}" class="admin-input" placeholder="Project, slug, client">
            </div>
            <div class="admin-field" style="flex: 0 1 11rem; margin: 0;">
                <label for="status" class="font-medium">Publication</label>
                <select id="status" name="status" class="admin-select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-field" style="flex: 0 1 12rem; margin: 0;">
                <label for="project_status" class="font-medium">Project stage</label>
                <select id="project_status" name="project_status" class="admin-select">
                    <option value="">All stages</option>
                    @foreach ($projectStatuses as $projectStatus)
                        <option value="{{ $projectStatus }}" @selected($selectedProjectStatus === $projectStatus)>{{ ucfirst(str_replace('_', ' ', $projectStatus)) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="admin-button">Filter</button>
            <a href="{{ route('admin.projects.index') }}" class="admin-link">Reset</a>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Project</th><th>Stage</th><th>Publication</th><th>Permission</th><th></th></tr></thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr>
                            <td>
                                <a class="font-semibold admin-link" href="{{ route('admin.projects.edit', $project) }}">{{ $project->title_en ?: $project->title_ar ?: 'Untitled project' }}</a>
                                <p class="admin-muted mt-1 text-sm">{{ $project->client_name ?: 'No client name' }}</p>
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $project->project_status)) }}</td>
                            <td><span class="admin-badge {{ $project->status === 'published' ? 'admin-badge-published' : ($project->status === 'archived' ? 'admin-badge-archived' : '') }}">{{ ucfirst($project->status) }}</span></td>
                            <td>{{ $project->display_permission_confirmed ? 'Confirmed' : 'Not confirmed' }}</td>
                            <td><a class="admin-link" href="{{ route('admin.projects.edit', $project) }}">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="admin-muted" style="padding-block: 2.5rem; text-align: center;">No projects or case studies yet. Do not create placeholder client work.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $projects->links() }}</div>
    </section>
@endsection
