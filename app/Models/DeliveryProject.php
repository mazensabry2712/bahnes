<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryProject extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = [
        'planning', 'in_progress', 'blocked', 'review', 'completed', 'on_hold', 'cancelled',
    ];

    public const STATUS_TRANSITIONS = [
        'planning' => ['in_progress', 'on_hold', 'cancelled', 'completed'],
        'in_progress' => ['blocked', 'review', 'completed', 'on_hold', 'cancelled'],
        'blocked' => ['in_progress', 'on_hold', 'cancelled'],
        'review' => ['in_progress', 'completed', 'on_hold', 'cancelled'],
        'completed' => ['in_progress'],
        'on_hold' => ['planning', 'in_progress', 'blocked', 'cancelled'],
        'cancelled' => ['planning'],
    ];

    protected $fillable = [
        'client_id', 'owner_id', 'title', 'description', 'status',
        'progress_percent', 'start_date', 'target_date', 'completed_at', 'internal_notes',
        'reference', 'blocker', 'delivery_notes',
    ];

    protected function casts(): array
    {
        return [
            'progress_percent' => 'integer',
            'start_date' => 'date',
            'target_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function canTransitionTo(string $nextStatus): bool
    {
        return $nextStatus === $this->status
            || in_array($nextStatus, self::STATUS_TRANSITIONS[$this->status] ?? [], true);
    }

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** @return BelongsTo<User, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** @return HasMany<ProjectTask, $this> */
    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'delivery_project_id');
    }

    /** @return HasMany<ProjectActivity, $this> */
    public function activities(): HasMany
    {
        return $this->hasMany(ProjectActivity::class, 'delivery_project_id');
    }

    /** @return HasMany<ProjectUpdate, $this> */
    public function updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class);
    }
}
