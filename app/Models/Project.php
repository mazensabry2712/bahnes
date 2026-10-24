<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasContentMedia;
use Spatie\MediaLibrary\HasMedia;
use InvalidArgumentException;

class Project extends Model implements HasMedia
{
    use HasFactory, HasContentMedia;

    public const STATUSES = ['draft', 'published', 'archived'];
    public const PROJECT_STATUSES = ['completed', 'in_progress', 'concept', 'confidential'];

    protected $fillable = [
        'title_en', 'title_ar', 'slug_en', 'slug_ar',
        'summary_en', 'summary_ar',
        'challenge_en', 'challenge_ar', 'approach_en', 'approach_ar',
        'outcome_en', 'outcome_ar', 'deliverables_en', 'deliverables_ar',
        'client_name', 'project_url', 'project_status',
        'display_permission_confirmed', 'status', 'sort_order', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'display_permission_confirmed' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublishedForLocale(Builder $query, string $locale): Builder
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            throw new InvalidArgumentException('Unsupported website locale.');
        }

        $title = "title_{$locale}";
        $slug = "slug_{$locale}";

        return $query
            ->where('status', 'published')
            ->where('project_status', '<>', 'confidential')
            ->whereNotNull($title)->where($title, '<>', '')
            ->whereNotNull($slug)->where($slug, '<>', '')
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->where('project_status', 'concept')
                    ->orWhere('display_permission_confirmed', true);
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
