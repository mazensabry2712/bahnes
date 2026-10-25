<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\DeliveryProject;
use App\Models\ProjectActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectActivityController extends Controller
{
    public function security(Request $request): JsonResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        return response()->json(ProjectActivity::query()
            ->whereNull('client_id')
            ->whereNull('delivery_project_id')
            ->with('actor:id,name')
            ->latest()
            ->paginate(50));
    }

    public function client(Request $request, Client $client): JsonResponse
    {
        $query = $client->activities()->getQuery()->with('actor:id,name');
        $this->applyVisibilityRules($query, $request);

        return response()->json($query->latest()->paginate(50));
    }

    public function project(Request $request, DeliveryProject $project): JsonResponse
    {
        $query = $project->activities()->getQuery()->with('actor:id,name');
        $this->applyVisibilityRules($query, $request);

        return response()->json($query->latest()->paginate(50));
    }

    /**
     * Activity feeds must not bypass the granular permissions on the resources
     * that generated those events.
     *
     * @param Builder<ProjectActivity> $query
     */
    private function applyVisibilityRules(Builder $query, Request $request): void
    {
        $user = $request->user();

        if (! $user?->hasPermissionTo('projects.view')) {
            $query->whereNull('delivery_project_id');
        } elseif (! $user->hasPermissionTo('project_tasks.view')) {
            $query->where('event_key', 'not like', 'project.task_%')
                ->where('event_key', 'not like', 'project.milestone_%');
        }

        if (! $user?->hasPermissionTo('project_updates.create')
            && ! $user?->hasPermissionTo('project_updates.send')) {
            $query->where('event_key', 'not like', 'project_update.%');
        }

        if (! $user?->hasPermissionTo('clients.documents.view')
            && ! $user?->hasPermissionTo('clients.documents.manage')) {
            $query->where('event_key', 'not like', 'client.document_%');
        }
    }
}
