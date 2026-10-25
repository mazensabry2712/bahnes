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
        // Internal notes are private operational data and must never be copied into the audit API payload.
        if ($oldValues !== null) {
            unset($oldValues['internal_notes']);
        }

        if ($newValues !== null) {
            unset($newValues['internal_notes']);
        }

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
