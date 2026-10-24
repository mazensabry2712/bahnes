    @php($coverMedia = $entity->getFirstMedia('cover'))
    @php($coverPreview = $coverMedia ? ($coverMedia->hasGeneratedConversion('preview') ? $coverMedia->getUrl('preview') : $coverMedia->getUrl()) : null)
    @php($fallbackCoverAlt = $entity->getAttribute('title_en') ?: $entity->getAttribute('title_ar') ?: 'Content cover')
    <section class="admin-field" aria-labelledby="cover-image-heading">
        <h2 id="cover-image-heading" class="admin-section-heading">Cover image</h2>
        <p class="admin-muted text-sm">Optional JPG, PNG, or WebP image. Maximum 5 MB. Files are stored through the media library with a generated filename.</p>
        @if ($coverMedia && $coverPreview)
            <figure class="mb-4">
                <img src="{{ $coverPreview }}" alt="{{ $coverMedia->getCustomProperty('alt_en') ?: $fallbackCoverAlt }}" width="480" height="360" loading="lazy" style="display:block;width:min(100%,30rem);height:auto;aspect-ratio:4/3;object-fit:contain;background:#f2f4f7;border:1px solid #e2e6ed;border-radius:.75rem;">
                <figcaption class="admin-muted mt-2 text-sm">Current cover image</figcaption>
            </figure>
        @endif
        <label for="cover_image" class="font-medium">Upload / replace cover image</label>
        <input id="cover_image" name="cover_image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="admin-input">
        @error('cover_image') <p class="admin-error">{{ $message }}</p> @enderror
        <div class="admin-grid">
            <div class="admin-field">
                <label for="cover_alt_en" class="font-medium">English image alt text</label>
                <input id="cover_alt_en" name="cover_alt_en" maxlength="180" value="{{ old('cover_alt_en', $coverMedia?->getCustomProperty('alt_en') ?? '') }}" class="admin-input">
                @error('cover_alt_en') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
            <div class="admin-field">
                <label for="cover_alt_ar" class="font-medium">Arabic image alt text</label>
                <input id="cover_alt_ar" name="cover_alt_ar" maxlength="180" dir="rtl" value="{{ old('cover_alt_ar', $coverMedia?->getCustomProperty('alt_ar') ?? '') }}" class="admin-input">
                @error('cover_alt_ar') <p class="admin-error">{{ $message }}</p> @enderror
            </div>
        </div>
        @if ($coverMedia)
            <label class="flex items-start gap-2 font-medium">
                <input type="checkbox" name="remove_cover_image" value="1" @checked(old('remove_cover_image'))>
                <span>Remove the current cover image</span>
            </label>
            @error('remove_cover_image') <p class="admin-error">{{ $message }}</p> @enderror
        @endif
    </section>
