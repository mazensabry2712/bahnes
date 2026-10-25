<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_project_id', 'author_id', 'summary', 'completed_work',
        'current_work', 'next_step', 'target_date', 'client_visible',
        'email_status', 'email_attempts', 'email_sent_at', 'email_error',
    ];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
            'client_visible' => 'boolean',
            'email_attempts' => 'integer',
            'email_sent_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<DeliveryProject, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(DeliveryProject::class, 'delivery_project_id');
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
