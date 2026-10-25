<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\DeliveryProject;
use Illuminate\Http\JsonResponse;

class ProjectActivityController extends Controller
{
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
