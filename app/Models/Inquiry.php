<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Inquiry extends Model
{
    use HasFactory;

    public const TYPES = [
        'general',
        'it_infrastructure',
        'software',
        'project_brief',
        'product_demo',
        'template_customization',
        'support',
    ];

    public const STATUSES = [
        'new',
        'in_progress',
        'waiting_for_client',
        'won',
        'lost',
        'spam',
        'closed',
    ];

    protected $fillable = [
        'name', 'email', 'phone', 'company', 'type', 'subject', 'message',
        'preferred_locale', 'source', 'status', 'admin_notes',
    ];

    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException('Unsupported inquiry status.');
        }

        return $query->where('status', $status);
    }
}
