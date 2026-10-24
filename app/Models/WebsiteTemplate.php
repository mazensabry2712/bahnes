<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class WebsiteTemplate extends Model
{
    use HasFactory;

    public const STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = [
        'title_en', 'title_ar', 'slug_en', 'slug_ar',
        'summary_en', 'summary_ar', 'description_en', 'description_ar',
        'included_pages_en', 'included_pages_ar',
        'license_terms_en', 'license_terms_ar',
        'delivery_terms_en', 'delivery_terms_ar',
        'support_terms_en', 'support_terms_ar',
        'customization_en', 'customization_ar',
        'price_note_en', 'price_note_ar',
        'preview_url', 'demo_url', 'status', 'sort_order', 'published_at',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'published_at' => 'datetime'];
    }

    public function scopePublishedForLocale(Builder $query, string $locale): Builder
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            throw new InvalidArgumentException('Unsupported website locale.');
        }

        foreach (['title', 'slug', 'summary', 'license_terms', 'delivery_terms', 'support_terms'] as $field) {
            $column = "{$field}_{$locale}";
            $query->whereNotNull($column)->where($column, '<>', '');
        }

        return $query->where('status', 'published')
            ->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
