@if (session('status'))
    <p class="admin-status admin-flash" role="status">{{ session('status') }}</p>
@endif

@if ($errors->any())
    <div class="admin-error admin-flash" role="alert" aria-live="assertive">
        <p class="font-semibold">Please correct the highlighted fields:</p>
        <ul class="mt-2 list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $formAction }}" class="admin-card">
    @csrf
    @if ($httpMethod !== 'POST')
        @method($httpMethod)
    @endif

    <div class="admin-field">
        <label for="service_line_id" class="font-medium">Service line</label>
        <select id="service_line_id" name="service_line_id" class="admin-select" required>
            <option value="">Choose a service line</option>
            @foreach ($serviceLines as $line)
                <option value="{{ $line->id }}" @selected((string) old('service_line_id', $service->service_line_id) === (string) $line->id)>
                    {{ $line->name_en }} — {{ $line->name_ar }}
                </option>
            @endforeach
        </select>
        @error('service_line_id') <p class="admin-error">{{ $message }}</p> @enderror
    </div>

    <h2 class="admin-section-heading">English content</h2>
    <div class="admin-field">
        <label for="title_en" class="font-medium">English title</label>
        <input id="title_en" name="title_en" maxlength="180" value="{{ old('title_en', $service->title_en) }}" class="admin-input">
        @error('title_en') <p class="admin-error">{{ $message }}</p> @enderror
    </div>
    <div class="admin-field">
        <label for="slug_en" class="font-medium">English URL slug (lowercase ASCII, hyphens)</label>
        <input id="slug_en" name="slug_en" maxlength="180" value="{{ old('slug_en', $service->slug_en) }}" class="admin-input" placeholder="network-installation">
        @error('slug_en') <p class="admin-error">{{ $message }}</p> @enderror
    </div>
    <div class="admin-field">
        <label for="summary_en" class="font-medium">English summary</label>
        <textarea id="summary_en" name="summary_en" maxlength="1000" class="admin-textarea">{{ old('summary_en', $service->summary_en) }}</textarea>
        @error('summary_en') <p class="admin-error">{{ $message }}</p> @enderror
    </div>
    <div class="admin-field">
        <label for="body_en" class="font-medium">English description</label>
        <textarea id="body_en" name="body_en" maxlength="30000" class="admin-textarea" style="min-height: 12rem;">{{ old('body_en', $service->body_en) }}</textarea>
        @error('body_en') <p class="admin-error">{{ $message }}</p> @enderror
    </div>

    <h2 class="admin-section-heading">Arabic content</h2>
    <div class="admin-field">
        <label for="title_ar" class="font-medium">Arabic title</label>
        <input id="title_ar" name="title_ar" maxlength="180" dir="rtl" value="{{ old('title_ar', $service->title_ar) }}" class="admin-input">
        @error('title_ar') <p class="admin-error">{{ $message }}</p> @enderror
    </div>
    <div class="admin-field">
        <label for="slug_ar" class="font-medium">Arabic URL slug (lowercase ASCII, hyphens)</label>
        <input id="slug_ar" name="slug_ar" maxlength="180" dir="ltr" value="{{ old('slug_ar', $service->slug_ar) }}" class="admin-input" placeholder="camera-installation">
        @error('slug_ar') <p class="admin-error">{{ $message }}</p> @enderror
    </div>
    <div class="admin-field">
        <label for="summary_ar" class="font-medium">Arabic summary</label>
        <textarea id="summary_ar" name="summary_ar" maxlength="1000" dir="rtl" class="admin-textarea">{{ old('summary_ar', $service->summary_ar) }}</textarea>
        @error('summary_ar') <p class="admin-error">{{ $message }}</p> @enderror
    </div>
    <div class="admin-field">
        <label for="body_ar" class="font-medium">Arabic description</label>
        <textarea id="body_ar" name="body_ar" maxlength="30000" dir="rtl" class="admin-textarea" style="min-height: 12rem;">{{ old('body_ar', $service->body_ar) }}</textarea>
        @error('body_ar') <p class="admin-error">{{ $message }}</p> @enderror
    </div>

    <h2 class="admin-section-heading">Search engine metadata</h2>
    <div class="admin-grid">
        <div class="admin-field">
            <label for="meta_title_en" class="font-medium">English SEO title</label>
            <input id="meta_title_en" name="meta_title_en" maxlength="180" value="{{ old('meta_title_en', $service->meta_title_en) }}" class="admin-input">
            @error('meta_title_en') <p class="admin-error">{{ $message }}</p> @enderror
        </div>
        <div class="admin-field">
            <label for="meta_title_ar" class="font-medium">Arabic SEO title</label>
            <input id="meta_title_ar" name="meta_title_ar" maxlength="180" dir="rtl" value="{{ old('meta_title_ar', $service->meta_title_ar) }}" class="admin-input">
            @error('meta_title_ar') <p class="admin-error">{{ $message }}</p> @enderror
        </div>
        <div class="admin-field">
            <label for="meta_description_en" class="font-medium">English meta description</label>
            <textarea id="meta_description_en" name="meta_description_en" maxlength="320" class="admin-textarea">{{ old('meta_description_en', $service->meta_description_en) }}</textarea>
            @error('meta_description_en') <p class="admin-error">{{ $message }}</p> @enderror
        </div>
        <div class="admin-field">
            <label for="meta_description_ar" class="font-medium">Arabic meta description</label>
            <textarea id="meta_description_ar" name="meta_description_ar" maxlength="320" dir="rtl" class="admin-textarea">{{ old('meta_description_ar', $service->meta_description_ar) }}</textarea>
            @error('meta_description_ar') <p class="admin-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <h2 class="admin-section-heading">Publication</h2>
    <div class="admin-grid">
        <div class="admin-field">
            <label for="status" class="font-medium">Status</label>
            <select id="status" name="status" class="admin-select" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $service->status ?: 'draft') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @error('status') <p class="admin-error">{{ $message }}</p> @enderror
        </div>
        <div class="admin-field">
            <label for="sort_order" class="font-medium">Display order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" max="65535" value="{{ old('sort_order', $service->sort_order ?? 0) }}" class="admin-input" required>
            @error('sort_order') <p class="admin-error">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="admin-field">
        <label for="published_at" class="font-medium">Publish at (optional, server timezone)</label>
        <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $service->published_at?->format('Y-m-d\TH:i')) }}" class="admin-input">
        <p class="admin-muted text-sm">Leave blank to make a fully translated service available immediately when its status is Published.</p>
        @error('published_at') <p class="admin-error">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6 flex flex-wrap items-center gap-3">
        <button type="submit" class="admin-button">Save service</button>
        <a href="{{ route('admin.services.index') }}" class="admin-link">Cancel</a>
    </div>
</form>
