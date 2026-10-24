<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $projectStatus = $request->query('project_status');
        $search = trim((string) $request->query('q', ''));

        $projects = Project::query()
            ->when(in_array($status, Project::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when(in_array($projectStatus, Project::PROJECT_STATUSES, true), fn ($query) => $query->where('project_status', $projectStatus))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title_en', 'like', "%{$search}%")
                        ->orWhere('title_ar', 'like', "%{$search}%")
                        ->orWhere('client_name', 'like', "%{$search}%")
                        ->orWhere('slug_en', 'like', "%{$search}%")
                        ->orWhere('slug_ar', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'statuses' => Project::STATUSES,
            'projectStatuses' => Project::PROJECT_STATUSES,
            'selectedStatus' => in_array($status, Project::STATUSES, true) ? $status : '',
            'selectedProjectStatus' => in_array($projectStatus, Project::PROJECT_STATUSES, true) ? $projectStatus : '',
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.form', [
            'project' => new Project(['status' => 'draft', 'project_status' => 'concept', 'sort_order' => 0]),
            'statuses' => Project::STATUSES,
            'projectStatuses' => Project::PROJECT_STATUSES,
            'formAction' => route('admin.projects.store'),
            'httpMethod' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $project = Project::query()->create($this->validatedData($request));

        return redirect()->route('admin.projects.edit', $project)
            ->with('status', 'Project saved successfully.');
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.form', [
            'project' => $project,
            'statuses' => Project::STATUSES,
            'projectStatuses' => Project::PROJECT_STATUSES,
            'formAction' => route('admin.projects.update', $project),
            'httpMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $project->update($this->validatedData($request, $project));

        return redirect()->route('admin.projects.edit', $project)
            ->with('status', 'Project updated successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Project $project = null): array
    {
        $slugEn = Rule::unique('projects', 'slug_en');
        $slugAr = Rule::unique('projects', 'slug_ar');

        if ($project) {
            $slugEn->ignore($project->getKey());
            $slugAr->ignore($project->getKey());
        }

        $validator = Validator::make($request->all(), [
            'title_en' => ['nullable', 'string', 'max:180'],
            'title_ar' => ['nullable', 'string', 'max:180'],
            'slug_en' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugEn],
            'slug_ar' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugAr],
            'summary_en' => ['nullable', 'string', 'max:2000'],
            'summary_ar' => ['nullable', 'string', 'max:2000'],
            'challenge_en' => ['nullable', 'string', 'max:30000'],
            'challenge_ar' => ['nullable', 'string', 'max:30000'],
            'approach_en' => ['nullable', 'string', 'max:30000'],
            'approach_ar' => ['nullable', 'string', 'max:30000'],
            'outcome_en' => ['nullable', 'string', 'max:30000'],
            'outcome_ar' => ['nullable', 'string', 'max:30000'],
            'deliverables_en' => ['nullable', 'string', 'max:30000'],
            'deliverables_ar' => ['nullable', 'string', 'max:30000'],
            'client_name' => ['nullable', 'string', 'max:180'],
            'project_url' => ['nullable', 'url', 'max:2048'],
            'project_status' => ['required', Rule::in(Project::PROJECT_STATUSES)],
            'display_permission_confirmed' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(Project::STATUSES)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            $completeLocales = 0;

            foreach (['en', 'ar'] as $locale) {
                $title = trim((string) $request->input("title_{$locale}", ''));
                $slug = trim((string) $request->input("slug_{$locale}", ''));

                if ($title !== '' && $slug === '') {
                    $validator->errors()->add("slug_{$locale}", 'Provide a URL slug when a title is entered.');
                } elseif ($title === '' && $slug !== '') {
                    $validator->errors()->add("title_{$locale}", 'Provide a title when a URL slug is entered.');
                }

                if ($title !== '' && $slug !== '') {
                    $completeLocales++;
                }
            }

            $status = $request->input('status');
            $projectStatus = $request->input('project_status');

            if ($status === 'published' && $completeLocales === 0) {
                $validator->errors()->add('status', 'To publish a project, complete its title and URL slug in at least one language.');
            }

            if ($status === 'published' && $projectStatus === 'confidential') {
                $validator->errors()->add('project_status', 'Confidential projects cannot be published publicly.');
            }

            if ($status === 'published' && $projectStatus !== 'concept' && ! $request->boolean('display_permission_confirmed')) {
                $validator->errors()->add('display_permission_confirmed', 'Confirm permission to display real project work before publishing it.');
            }

            if ($projectStatus === 'concept' && filled($request->input('client_name'))) {
                $validator->errors()->add('client_name', 'Concept projects must not be presented as work for a named client.');
            }
        });

        $data = $validator->validate();
        $data['display_permission_confirmed'] = $request->boolean('display_permission_confirmed');

        if ($data['project_status'] === 'concept') {
            $data['client_name'] = null;
        }

        return $data;
    }
}
