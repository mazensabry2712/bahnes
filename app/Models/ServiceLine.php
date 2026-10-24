<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class ServiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name_en',
        'name_ar',
        'summary_en',
        'summary_ar',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function nameFor(string $locale): string
    {
        return match ($locale) {
            'en' => $this->name_en,
            'ar' => $this->name_ar,
            default => throw new InvalidArgumentException('Unsupported website locale.'),
        };
    }
}
