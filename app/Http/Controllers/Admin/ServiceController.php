<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Concerns\HandlesContentMedia;
use App\Models\Service;
use App\Models\ServiceLine;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    use HandlesContentMedia;

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));

        $services = Service::query()
            ->with('serviceLine')
            ->when(in_array($status, Service::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title_en', 'like', "%{$search}%")
                        ->orWhere('title_ar', 'like', "%{$search}%")
                        ->orWhere('slug_en', 'like', "%{$search}%")
                        ->orWhere('slug_ar', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.services.index', [
            'services' => $services,
            'statuses' => Service::STATUSES,
            'selectedStatus' => in_array($status, Service::STATUSES, true) ? $status : '',
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.services.create', [
            'service' => new Service(['status' => 'draft', 'sort_order' => 0]),
            'serviceLines' => ServiceLine::query()->orderBy('sort_order')->get(),
            'statuses' => Service::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $service = Service::query()->create($this->withoutContentMediaFields($data));
        $this->saveCoverImage($request, $service);

        return redirect()->route('admin.services.edit', $service)
            ->with('status', 'Service saved successfully.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', [
            'service' => $service->load('serviceLine'),
            'serviceLines' => ServiceLine::query()->orderBy('sort_order')->get(),
            'statuses' => Service::STATUSES,
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $this->validatedData($request, $service);
        $service->update($this->withoutContentMediaFields($data));
        $this->saveCoverImage($request, $service);

        return redirect()->route('admin.services.edit', $service)
            ->with('status', 'Service updated successfully.');
    }

    /**
     * A published service needs a complete title/slug pair in at least one locale.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Service $service = null): array
    {
        $slugEn = Rule::unique('services', 'slug_en');
        $slugAr = Rule::unique('services', 'slug_ar');

        if ($service) {
            $slugEn->ignore($service->getKey());
            $slugAr->ignore($service->getKey());
        }

        $rules = [
            'service_line_id' => ['required', 'integer', Rule::exists('service_lines', 'id')],
            'title_en' => ['nullable', 'string', 'max:180'],
            'title_ar' => ['nullable', 'string', 'max:180'],
            'slug_en' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugEn],
            'slug_ar' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugAr],
            'summary_en' => ['nullable', 'string', 'max:1000'],
            'summary_ar' => ['nullable', 'string', 'max:1000'],
            'body_en' => ['nullable', 'string', 'max:30000'],
            'body_ar' => ['nullable', 'string', 'max:30000'],
            'meta_title_en' => ['nullable', 'string', 'max:180'],
            'meta_title_ar' => ['nullable', 'string', 'max:180'],
            'meta_description_en' => ['nullable', 'string', 'max:320'],
            'meta_description_ar' => ['nullable', 'string', 'max:320'],
            'status' => ['required', Rule::in(Service::STATUSES)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'published_at' => ['nullable', 'date'],
        ];

        $validator = Validator::make($request->all(), array_merge($rules, $this->contentMediaValidationRules()));

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

            if ($request->input('status') === 'published' && $completeLocales === 0) {
                $validator->errors()->add('status', 'To publish a service, complete its title and URL slug in at least one language.');
            }
        });

        return $validator->validate();
    }
}
