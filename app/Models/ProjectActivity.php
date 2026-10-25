<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectActivity extends Model
{
    protected $fillable = [
        'actor_id', 'client_id', 'delivery_project_id', 'event_key',
        'summary', 'old_values', 'new_values',
    ];

    protected function casts(): array
    {
        return ['old_values' => 'array', 'new_values' => 'array'];
    }

    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /** @return BelongsTo<Client, $this> */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /** @return BelongsTo<DeliveryProject, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(DeliveryProject::class, 'delivery_project_id');
    }
}
