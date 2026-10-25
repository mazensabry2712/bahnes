<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryProject;
use App\Models\ProjectTask;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectTaskController extends Controller
{
    public function index(DeliveryProject $project): JsonResponse
    {
        return response()->json($project->tasks()
            ->with('assignee:id,name')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(50));
    }

    public function store(Request $request, DeliveryProject $project): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(ProjectTask::STATUSES)],
            'is_milestone' => ['sometimes', 'boolean'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_admin', true)->orWhereNotNull('role_id'))],
            'due_date' => ['nullable', 'date'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:65535'],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $data['is_milestone'] = (bool) ($data['is_milestone'] ?? false);
        $data['sort_order'] ??= 0;
        if ($data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        $task = $project->tasks()->create($data);
        app(ProjectActivityLogger::class)->record(
            $request->user(), $task->is_milestone ? 'project.milestone_created' : 'project.task_created',
            $task->is_milestone ? 'Project milestone created.' : 'Project task created.',
            $project->client, $project, null,
            ['task_id' => $task->getKey(), 'title' => $task->title, 'status' => $task->status, 'due_date' => $data['due_date'] ?? null],
        );

        return back()->with('status', $task->is_milestone ? 'Milestone created.' : 'Task created.');
    }

    public function update(Request $request, ProjectTask $task): RedirectResponse
    {
        abort_unless($task->project, 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(ProjectTask::STATUSES)],
            'is_milestone' => ['sometimes', 'boolean'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_admin', true)->orWhereNotNull('role_id'))],
            'due_date' => ['nullable', 'date'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $oldValues = $task->only(array_keys($data));
        if ($data['status'] === 'completed') {
            $data['completed_at'] = $task->completed_at ?? now();
        } else {
            $data['completed_at'] = null;
        }

        $task->update($data);
        $project = $task->project;
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'project.task_updated',
            $task->is_milestone ? 'Project milestone updated.' : 'Project task updated.',
            $project?->client, $project, $oldValues,
            collect($data)->all(),
        );

        return back()->with('status', $task->is_milestone ? 'Milestone updated.' : 'Task updated.');
    }

    public function archive(Request $request, ProjectTask $task): RedirectResponse
    {
        abort_unless($task->project, 404);
        $project = $task->project;
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'project.task_archived',
            $task->is_milestone ? 'Project milestone archived.' : 'Project task archived.',
            $project?->client, $project, ['title' => $task->title, 'status' => $task->status], null,
        );
        $task->delete();

        return back()->with('status', $task->is_milestone ? 'Milestone archived.' : 'Task archived.');
    }
}
