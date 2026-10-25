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

    /** @return HasMany<ProjectUpdate, $this> */
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

    public function updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class);
    }
}
