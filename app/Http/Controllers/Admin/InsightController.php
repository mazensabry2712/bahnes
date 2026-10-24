<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insight;
use App\Models\InsightCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InsightController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $categoryId = $request->query('category');
        $search = trim((string) $request->query('q', ''));

        $insights = Insight::query()
            ->with('category')
            ->when(in_array($status, Insight::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when(is_numeric($categoryId), fn ($query) => $query->where('insight_category_id', (int) $categoryId))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title_en', 'like', "%{$search}%")
                        ->orWhere('title_ar', 'like', "%{$search}%")
                        ->orWhere('slug_en', 'like', "%{$search}%")
                        ->orWhere('slug_ar', 'like', "%{$search}%")
                        ->orWhere('excerpt_en', 'like', "%{$search}%")
                        ->orWhere('excerpt_ar', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.insights.index', [
            'insights' => $insights,
            'statuses' => Insight::STATUSES,
            'categories' => InsightCategory::query()->orderBy('sort_order')->get(),
            'selectedStatus' => in_array($status, Insight::STATUSES, true) ? $status : '',
            'selectedCategory' => is_numeric($categoryId) ? (string) $categoryId : '',
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.insights.form', [
            'insight' => new Insight(['status' => 'draft', 'sort_order' => 0]),
            'categories' => InsightCategory::query()->orderBy('sort_order')->get(),
            'statuses' => Insight::STATUSES,
            'formAction' => route('admin.insights.store'),
            'httpMethod' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $insight = Insight::query()->create($this->validatedData($request));
        return redirect()->route('admin.insights.edit', $insight)->with('status', 'Insight saved successfully.');
    }

    public function edit(Insight $insight): View
    {
        return view('admin.insights.form', [
            'insight' => $insight->load('category'),
            'categories' => InsightCategory::query()->orderBy('sort_order')->get(),
            'statuses' => Insight::STATUSES,
            'formAction' => route('admin.insights.update', $insight),
            'httpMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, Insight $insight): RedirectResponse
    {
        $insight->update($this->validatedData($request, $insight));
        return redirect()->route('admin.insights.edit', $insight)->with('status', 'Insight updated successfully.');
    }

    private function validatedData(Request $request, ?Insight $insight = null): array
    {
        $slugEn = Rule::unique('insights', 'slug_en');
        $slugAr = Rule::unique('insights', 'slug_ar');
        if ($insight) {
            $slugEn->ignore($insight->getKey());
            $slugAr->ignore($insight->getKey());
        }

        $rules = [
            'insight_category_id' => ['required', 'integer', Rule::exists('insight_categories', 'id')],
            'title_en' => ['nullable', 'string', 'max:180'],
            'title_ar' => ['nullable', 'string', 'max:180'],
            'slug_en' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugEn],
            'slug_ar' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugAr],
            'excerpt_en' => ['nullable', 'string', 'max:2000'],
            'excerpt_ar' => ['nullable', 'string', 'max:2000'],
            'body_en' => ['nullable', 'string', 'max:100000'],
            'body_ar' => ['nullable', 'string', 'max:100000'],
            'meta_title_en' => ['nullable', 'string', 'max:180'],
            'meta_title_ar' => ['nullable', 'string', 'max:180'],
            'meta_description_en' => ['nullable', 'string', 'max:320'],
            'meta_description_ar' => ['nullable', 'string', 'max:320'],
            'status' => ['required', Rule::in(Insight::STATUSES)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'published_at' => ['nullable', 'date'],
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($request): void {
            $categoryActive = InsightCategory::query()->whereKey($request->input('insight_category_id'))->where('is_active', true)->exists();
            $hasCompleteLocale = false;

            foreach (['en', 'ar'] as $locale) {
                $title = trim((string) $request->input("title_{$locale}", ''));
                $slug = trim((string) $request->input("slug_{$locale}", ''));
                $excerpt = trim((string) $request->input("excerpt_{$locale}", ''));
                $body = trim((string) $request->input("body_{$locale}", ''));

                if ($title !== '' && $slug === '') $validator->errors()->add("slug_{$locale}", 'Provide a URL slug when a title is entered.');
                if ($title === '' && $slug !== '') $validator->errors()->add("title_{$locale}", 'Provide a title when a slug is entered.');

                if ($title !== '' && $slug !== '' && $excerpt !== '' && $body !== '') $hasCompleteLocale = true;
            }

            if ($request->input('status') === 'published' && ! $categoryActive) {
                $validator->errors()->add('insight_category_id', 'Choose an active category before publishing.');
            }

            if ($request->input('status') === 'published' && ! $hasCompleteLocale) {
                $validator->errors()->add('status', 'To publish an Insight, complete the title, slug, summary and body in at least one language.');
            }
        });

        return $validator->validate();
    }
}
