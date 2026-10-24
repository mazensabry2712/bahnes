<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class Insight extends Model
{
    use HasFactory;

    public const STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = [
        'insight_category_id',
        'title_en', 'title_ar', 'slug_en', 'slug_ar',
        'excerpt_en', 'excerpt_ar', 'body_en', 'body_ar',
        'meta_title_en', 'meta_title_ar',
        'meta_description_en', 'meta_description_ar',
        'status', 'sort_order', 'published_at',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'published_at' => 'datetime'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InsightCategory::class, 'insight_category_id');
    }

    public function scopePublishedForLocale(Builder $query, string $locale): Builder
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            throw new InvalidArgumentException('Unsupported website locale.');
        }

        foreach (['title', 'slug', 'excerpt', 'body'] as $field) {
            $column = "{$field}_{$locale}";
            $query->whereNotNull($column)->where($column, '<>', '');
        }

        return $query
            ->where('status', 'published')
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->whereHas('category', function (Builder $query): void {
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
