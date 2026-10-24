@extends('admin.layouts.app')

@section('title', $project->exists ? 'Edit project' : 'Add project')

@section('content')
    <header class="mb-7">
        <a class="admin-link text-sm" href="{{ route('admin.projects.index') }}">← Back to projects</a>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ $project->exists ? 'Edit project / case study' : 'Add project / case study' }}</h1>
        <p class="admin-muted mt-2">Keep concepts labeled as concepts and confirm permission before publishing real client work.</p>
    </header>

    @if (session('status')) <p class="admin-status admin-flash" role="status">{{ session('status') }}</p> @endif
    @if ($errors->any())
        <div class="admin-error admin-flash" role="alert" aria-live="assertive">
            <p class="font-semibold">Please correct the following fields:</p>
            <ul class="mt-2 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $formAction }}" class="admin-card">
        @csrf
        @if ($httpMethod !== 'POST') @method($httpMethod) @endif

        <h2 class="admin-section-heading">English content</h2>
        <div class="admin-field"><label for="title_en" class="font-medium">English title</label><input id="title_en" name="title_en" maxlength="180" value="{{ old('title_en', $project->title_en) }}" class="admin-input">@error('title_en')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="slug_en" class="font-medium">English URL slug</label><input id="slug_en" name="slug_en" maxlength="180" value="{{ old('slug_en', $project->slug_en) }}" class="admin-input" placeholder="network-modernization">@error('slug_en')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="summary_en" class="font-medium">English summary</label><textarea id="summary_en" name="summary_en" maxlength="2000" class="admin-textarea">{{ old('summary_en', $project->summary_en) }}</textarea>@error('summary_en')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="challenge_en" class="font-medium">Challenge / context</label><textarea id="challenge_en" name="challenge_en" maxlength="30000" class="admin-textarea">{{ old('challenge_en', $project->challenge_en) }}</textarea>@error('challenge_en')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="approach_en" class="font-medium">Approach / work performed</label><textarea id="approach_en" name="approach_en" maxlength="30000" class="admin-textarea">{{ old('approach_en', $project->approach_en) }}</textarea>@error('approach_en')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="outcome_en" class="font-medium">Outcome (verified results only)</label><textarea id="outcome_en" name="outcome_en" maxlength="30000" class="admin-textarea">{{ old('outcome_en', $project->outcome_en) }}</textarea>@error('outcome_en')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="deliverables_en" class="font-medium">Deliverables</label><textarea id="deliverables_en" name="deliverables_en" maxlength="30000" class="admin-textarea">{{ old('deliverables_en', $project->deliverables_en) }}</textarea>@error('deliverables_en')<p class="admin-error">{{ $message }}</p>@enderror</div>

        <h2 class="admin-section-heading">Arabic content</h2>
        <div class="admin-field"><label for="title_ar" class="font-medium">Arabic title</label><input id="title_ar" name="title_ar" maxlength="180" dir="rtl" value="{{ old('title_ar', $project->title_ar) }}" class="admin-input">@error('title_ar')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="slug_ar" class="font-medium">Arabic URL slug (ASCII)</label><input id="slug_ar" name="slug_ar" maxlength="180" dir="ltr" value="{{ old('slug_ar', $project->slug_ar) }}" class="admin-input" placeholder="tahdeeth-shabakat">@error('slug_ar')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="summary_ar" class="font-medium">Arabic summary</label><textarea id="summary_ar" name="summary_ar" maxlength="2000" dir="rtl" class="admin-textarea">{{ old('summary_ar', $project->summary_ar) }}</textarea>@error('summary_ar')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="challenge_ar" class="font-medium">التحدي / سياق المشروع</label><textarea id="challenge_ar" name="challenge_ar" maxlength="30000" dir="rtl" class="admin-textarea">{{ old('challenge_ar', $project->challenge_ar) }}</textarea>@error('challenge_ar')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="approach_ar" class="font-medium">طريقة التنفيذ / العمل المنجز</label><textarea id="approach_ar" name="approach_ar" maxlength="30000" dir="rtl" class="admin-textarea">{{ old('approach_ar', $project->approach_ar) }}</textarea>@error('approach_ar')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="outcome_ar" class="font-medium">النتيجة (نتائج تم التحقق منها فقط)</label><textarea id="outcome_ar" name="outcome_ar" maxlength="30000" dir="rtl" class="admin-textarea">{{ old('outcome_ar', $project->outcome_ar) }}</textarea>@error('outcome_ar')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="deliverables_ar" class="font-medium">المخرجات</label><textarea id="deliverables_ar" name="deliverables_ar" maxlength="30000" dir="rtl" class="admin-textarea">{{ old('deliverables_ar', $project->deliverables_ar) }}</textarea>@error('deliverables_ar')<p class="admin-error">{{ $message }}</p>@enderror</div>

        <h2 class="admin-section-heading">Client and permission</h2>
        <div class="admin-field"><label for="client_name" class="font-medium">Client name (only if approved for disclosure)</label><input id="client_name" name="client_name" maxlength="180" value="{{ old('client_name', $project->client_name) }}" class="admin-input">@error('client_name')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field"><label for="project_url" class="font-medium">Public project URL (optional)</label><input id="project_url" name="project_url" type="url" maxlength="2048" value="{{ old('project_url', $project->project_url) }}" class="admin-input" placeholder="https://example.com">@error('project_url')<p class="admin-error">{{ $message }}</p>@enderror</div>
        <div class="admin-field">
            <label class="flex items-start gap-2 font-medium">
                <input type="checkbox" name="display_permission_confirmed" value="1" @checked(old('display_permission_confirmed', $project->display_permission_confirmed))>
                <span>Permission to publicly display this real project and its details has been confirmed.</span>
            </label>
            @error('display_permission_confirmed')<p class="admin-error">{{ $message }}</p>@enderror
        </div>

        <h2 class="admin-section-heading">Publication</h2>
        <div class="admin-grid">
            <div class="admin-field"><label for="project_status" class="font-medium">Project stage</label><select id="project_status" name="project_status" class="admin-select" required>@foreach ($projectStatuses as $projectStatus)<option value="{{ $projectStatus }}" @selected(old('project_status', $project->project_status ?: 'concept') === $projectStatus)>{{ ucfirst(str_replace('_', ' ', $projectStatus)) }}</option>@endforeach</select>@error('project_status')<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="status" class="font-medium">Publication status</label><select id="status" name="status" class="admin-select" required>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(old('status', $project->status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>@error('status')<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="sort_order" class="font-medium">Display order</label><input id="sort_order" name="sort_order" type="number" min="0" max="65535" value="{{ old('sort_order', $project->sort_order ?? 0) }}" class="admin-input" required>@error('sort_order')<p class="admin-error">{{ $message }}</p>@enderror</div>
            <div class="admin-field"><label for="published_at" class="font-medium">Publish at (optional, server timezone)</label><input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $project->published_at?->format('Y-m-d\TH:i')) }}" class="admin-input">@error('published_at')<p class="admin-error">{{ $message }}</p>@enderror</div>
        </div>
        <p class="admin-muted text-sm">Concepts may be published with a clear Concept label. Real Completed/In Progress projects need confirmed permission. Confidential projects never appear publicly.</p>
        <div class="mt-6 flex flex-wrap items-center gap-3"><button type="submit" class="admin-button">Save project</button><a href="{{ route('admin.projects.index') }}" class="admin-link">Cancel</a></div>
    </form>
@endsection
