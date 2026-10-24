<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class InsightCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en', 'name_ar', 'slug_en', 'slug_ar', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_active' => 'boolean'];
    }

    public function insights(): HasMany
    {
        return $this->hasMany(Insight::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function nameFor(string $locale): string
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            throw new InvalidArgumentException('Unsupported website locale.');
        }

        return $this->getAttribute("name_{$locale}");
    }
}
