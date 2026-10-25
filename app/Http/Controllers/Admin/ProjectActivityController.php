<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\DeliveryProject;
use App\Models\ProjectActivity;
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

    public function client(Client $client): JsonResponse
    {
        return response()->json($client->activities()
            ->with('actor:id,name')
            ->latest()
            ->paginate(50));
    }

    public function project(DeliveryProject $project): JsonResponse
    {
        return response()->json($project->activities()
            ->with('actor:id,name')
            ->latest()
            ->paginate(50));
    }
}
