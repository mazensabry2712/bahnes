<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTask extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = ['todo', 'in_progress', 'blocked', 'completed', 'cancelled'];

    protected $fillable = [
        'delivery_project_id', 'assigned_to', 'title', 'description', 'status',
        'is_milestone', 'due_date', 'completed_at', 'sort_order', 'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'is_milestone' => 'boolean',
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    /** @return BelongsTo<DeliveryProject, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(DeliveryProject::class, 'delivery_project_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
