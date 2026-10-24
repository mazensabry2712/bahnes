<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsightCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InsightCategoryController extends Controller
{
    public function index(): View
    {
        $categories = InsightCategory::query()
            ->withCount('insights')
            ->orderBy('sort_order')
            ->orderBy('name_en')
            ->paginate(30);

        return view('admin.insight-categories.index', ['categories' => $categories]);
    }

    public function create(): View
    {
        return view('admin.insight-categories.form', [
            'category' => new InsightCategory(['is_active' => true, 'sort_order' => 0]),
            'formAction' => route('admin.insight-categories.store'),
            'httpMethod' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = InsightCategory::query()->create($this->validatedData($request));
        return redirect()->route('admin.insight-categories.edit', $category)->with('status', 'Category saved successfully.');
    }

    public function edit(InsightCategory $insight_category): View
    {
        return view('admin.insight-categories.form', [
            'category' => $insight_category,
            'formAction' => route('admin.insight-categories.update', $insight_category),
            'httpMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, InsightCategory $insight_category): RedirectResponse
    {
        $insight_category->update($this->validatedData($request, $insight_category));
        return redirect()->route('admin.insight-categories.edit', $insight_category)->with('status', 'Category updated successfully.');
    }

    private function validatedData(Request $request, ?InsightCategory $category = null): array
    {
        $slugEn = Rule::unique('insight_categories', 'slug_en');
        $slugAr = Rule::unique('insight_categories', 'slug_ar');
        if ($category) {
            $slugEn->ignore($category->getKey());
            $slugAr->ignore($category->getKey());
        }

        return $request->validate([
            'name_en' => ['required', 'string', 'max:120'],
            'name_ar' => ['required', 'string', 'max:120'],
            'slug_en' => ['required', 'string', 'max:140', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugEn],
            'slug_ar' => ['required', 'string', 'max:140', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugAr],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
