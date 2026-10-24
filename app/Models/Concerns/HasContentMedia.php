<?php

namespace App\Models\Concerns;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;

trait HasContentMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 960, 720)
            ->nonQueued();
    }

    public function coverImageUrl(): ?string
    {
        $media = $this->getFirstMedia('cover');

        if (! $media) {
            return null;
        }

        return $media->hasGeneratedConversion('preview')
            ? $media->getUrl('preview')
            : $media->getUrl();
    }

    public function coverImageAlt(string $locale = 'en'): string
    {
        $locale = in_array($locale, ['en', 'ar'], true) ? $locale : 'en';
        $media = $this->getFirstMedia('cover');

        return (string) (
            $media?->getCustomProperty("alt_{$locale}")
            ?: $this->getAttribute("title_{$locale}")
            ?: $media?->name
            ?: ''
        );
    }
}
