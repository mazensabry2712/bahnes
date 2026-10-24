<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasContentMedia;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class Service extends Model implements HasMedia
{
    use HasFactory, HasContentMedia;

    public const STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = [
        'service_line_id',
        'title_en',
        'title_ar',
        'slug_en',
        'slug_ar',
        'summary_en',
        'summary_ar',
        'body_en',
        'body_ar',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'status',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function serviceLine(): BelongsTo
    {
        return $this->belongsTo(ServiceLine::class);
    }

    public function scopePublishedForLocale(Builder $query, string $locale): Builder
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            throw new InvalidArgumentException('Unsupported website locale.');
        }

        $titleColumn = "title_{$locale}";
        $slugColumn = "slug_{$locale}";

        return $query
            ->where('status', 'published')
            ->whereNotNull($titleColumn)
            ->where($titleColumn, '<>', '')
            ->whereNotNull($slugColumn)
            ->where($slugColumn, '<>', '')
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('serviceLine', function (Builder $query): void {
                $query->where('is_active', true);
            });
    }

    public function titleFor(string $locale): ?string
    {
        return $this->localizedValue('title', $locale);
    }

    public function slugFor(string $locale): ?string
    {
        return $this->localizedValue('slug', $locale);
    }

    protected function localizedValue(string $field, string $locale): ?string
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            throw new InvalidArgumentException('Unsupported website locale.');
        }

        return $this->getAttribute("{$field}_{$locale}");
    }
}
