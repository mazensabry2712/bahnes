<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Concerns\HandlesContentMedia;
use App\Models\WebsiteTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WebsiteTemplateController extends Controller
{
    use HandlesContentMedia;

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));
        $templates = WebsiteTemplate::query()
            ->when(in_array($status, WebsiteTemplate::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q->where('title_en', 'like', "%{$search}%")->orWhere('title_ar', 'like', "%{$search}%")->orWhere('slug_en', 'like', "%{$search}%")->orWhere('slug_ar', 'like', "%{$search}%")))
            ->orderBy('sort_order')->orderByDesc('updated_at')->paginate(20)->withQueryString();

        return view('admin.templates.index', ['templates' => $templates, 'statuses' => WebsiteTemplate::STATUSES, 'selectedStatus' => in_array($status, WebsiteTemplate::STATUSES, true) ? $status : '', 'search' => $search]);
    }

    public function create(): View
    {
        $template = new WebsiteTemplate(['status' => 'draft', 'sort_order' => 0]);
        return view('admin.catalog.form', $this->formData($template, route('admin.templates.store'), 'POST'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $template = WebsiteTemplate::query()->create($this->withoutContentMediaFields($data));
        $this->saveCoverImage($request, $template);
        return redirect()->route('admin.templates.edit', $template)->with('status', 'Website template saved successfully.');
    }

    public function edit(WebsiteTemplate $template): View
    {
        return view('admin.catalog.form', $this->formData($template, route('admin.templates.update', $template), 'PUT'));
    }

    public function update(Request $request, WebsiteTemplate $template): RedirectResponse
    {
        $data = $this->validatedData($request, $template);
        $template->update($this->withoutContentMediaFields($data));
        $this->saveCoverImage($request, $template);
        return redirect()->route('admin.templates.edit', $template)->with('status', 'Website template updated successfully.');
    }

    private function formData(WebsiteTemplate $entity, string $formAction, string $httpMethod): array
    {
        return [
            'entity' => $entity, 'catalogLabel' => 'Website template',
            'indexRoute' => 'admin.templates.index', 'formAction' => $formAction, 'httpMethod' => $httpMethod,
            'statuses' => WebsiteTemplate::STATUSES, 'urlField' => 'preview_url', 'urlLabel' => 'Preview URL (optional)',
            'localizedFields' => [
                ['key' => 'title', 'type' => 'input', 'max' => 180, 'label_en' => 'Template title', 'label_ar' => 'اسم القالب'],
                ['key' => 'slug', 'type' => 'input', 'max' => 180, 'label_en' => 'URL slug (lowercase ASCII)', 'label_ar' => 'رابط الصفحة (حروف إنجليزية)'],
                ['key' => 'summary', 'type' => 'textarea', 'max' => 2000, 'label_en' => 'Summary', 'label_ar' => 'ملخص'],
                ['key' => 'description', 'type' => 'textarea', 'max' => 30000, 'label_en' => 'Description', 'label_ar' => 'الوصف'],
                ['key' => 'included_pages', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'Included pages / files', 'label_ar' => 'الصفحات والملفات المضمنة'],
                ['key' => 'license_terms', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'License terms (required before publication)', 'label_ar' => 'شروط الترخيص (مطلوبة للنشر)'],
                ['key' => 'delivery_terms', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'Delivery terms (required before publication)', 'label_ar' => 'شروط التسليم (مطلوبة للنشر)'],
                ['key' => 'support_terms', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'Support terms (required before publication)', 'label_ar' => 'شروط الدعم (مطلوبة للنشر)'],
                ['key' => 'customization', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'Customization options', 'label_ar' => 'خيارات التخصيص'],
                ['key' => 'price_note', 'type' => 'input', 'max' => 180, 'label_en' => 'Price note (optional; no assumed price)', 'label_ar' => 'ملاحظة السعر (اختياري)'],
            ],
        ];
    }

    private function validatedData(Request $request, ?WebsiteTemplate $template = null): array
    {
        $rules = [
            'preview_url' => ['nullable', 'url:http,https', 'max:2048'],
            'demo_url' => ['nullable', 'url', 'max:2048'],
            'status' => ['required', Rule::in(WebsiteTemplate::STATUSES)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'published_at' => ['nullable', 'date'],
        ];
        foreach (['en', 'ar'] as $locale) {
            $unique = Rule::unique('website_templates', "slug_{$locale}");
            if ($template) $unique->ignore($template->getKey());
            $rules["title_{$locale}"] = ['nullable', 'string', 'max:180'];
            $rules["slug_{$locale}"] = ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $unique];
            $rules["summary_{$locale}"] = ['nullable', 'string', 'max:2000'];
            foreach (['description', 'included_pages', 'license_terms', 'delivery_terms', 'support_terms', 'customization'] as $field) $rules["{$field}_{$locale}"] = ['nullable', 'string', 'max:30000'];
            $rules["price_note_{$locale}"] = ['nullable', 'string', 'max:180'];
        }

        $validator = Validator::make($request->all(), array_merge($rules, $this->contentMediaValidationRules()));
        $validator->after(function ($validator) use ($request): void {
            $complete = false;
            foreach (['en', 'ar'] as $locale) {
                $title = trim((string) $request->input("title_{$locale}", ''));
                $slug = trim((string) $request->input("slug_{$locale}", ''));
                if ($title !== '' && $slug === '') $validator->errors()->add("slug_{$locale}", 'Provide a slug when a title is entered.');
                if ($title === '' && $slug !== '') $validator->errors()->add("title_{$locale}", 'Provide a title when a slug is entered.');
                $complete = $complete || ($title !== '' && $slug !== '' &&
                    filled($request->input("summary_{$locale}")) &&
                    filled($request->input("license_terms_{$locale}")) &&
                    filled($request->input("delivery_terms_{$locale}")) &&
                    filled($request->input("support_terms_{$locale}")));
            }
            if ($request->input('status') === 'published' && ! $complete) {
                $validator->errors()->add('status', 'To publish, one language must include a title, slug, summary, license, delivery and support terms.');
            }
        });
        return $validator->validate();
    }
}
