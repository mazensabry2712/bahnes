<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

trait HandlesContentMedia
{
    /**
     * @return array<string, array<int, mixed>>
     */
    protected function contentMediaValidationRules(): array
    {
        return [
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'cover_alt_en' => ['nullable', 'string', 'max:180'],
            'cover_alt_ar' => ['nullable', 'string', 'max:180'],
            'remove_cover_image' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function withoutContentMediaFields(array $data): array
    {
        return Arr::except($data, [
            'cover_image',
            'cover_alt_en',
            'cover_alt_ar',
            'remove_cover_image',
        ]);
    }

    protected function saveCoverImage(Request $request, HasMedia $model): void
    {
        $file = $request->file('cover_image');

        if ($file !== null) {
            $model->addMedia($file)
                ->usingFileName(Str::uuid().'.'.($file->guessExtension() ?: 'jpg'))
                ->withCustomProperties([
                    'alt_en' => filled($request->input('cover_alt_en')) ? trim((string) $request->input('cover_alt_en')) : null,
                    'alt_ar' => filled($request->input('cover_alt_ar')) ? trim((string) $request->input('cover_alt_ar')) : null,
                ])
                ->toMediaCollection('cover');

            return;
        }

        if ($request->boolean('remove_cover_image')) {
            $model->clearMediaCollection('cover');

            return;
        }

        $media = $model->getFirstMedia('cover');

        if (! $media) {
            return;
        }

        foreach (['en', 'ar'] as $locale) {
            $field = "cover_alt_{$locale}";

            if ($request->exists($field)) {
                $value = filled($request->input($field)) ? trim((string) $request->input($field)) : null;
                $media->setCustomProperty("alt_{$locale}", $value);
            }
        }

        $media->save();
    }
}
