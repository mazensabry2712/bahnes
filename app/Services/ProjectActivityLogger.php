<?php

namespace App\Services;

use App\Models\Client;
use App\Models\DeliveryProject;
use App\Models\ProjectActivity;
use App\Models\User;

class ProjectActivityLogger
{
    /**
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     */
    public function record(
        ?User $actor,
        string $eventKey,
        string $summary,
        ?Client $client = null,
        ?DeliveryProject $project = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): ProjectActivity {
        return ProjectActivity::query()->create([
            'actor_id' => $actor?->getKey(),
            'client_id' => $client?->getKey() ?? $project?->client_id,
            'delivery_project_id' => $project?->getKey(),
            'event_key' => $eventKey,
            'summary' => $summary,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
