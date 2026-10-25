<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Concerns\HandlesContentMedia;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use HandlesContentMedia;

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));
        $products = Product::query()
            ->when(in_array($status, Product::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q->where('title_en', 'like', "%{$search}%")->orWhere('title_ar', 'like', "%{$search}%")->orWhere('slug_en', 'like', "%{$search}%")->orWhere('slug_ar', 'like', "%{$search}%")))
            ->orderBy('sort_order')->orderByDesc('updated_at')->paginate(20)->withQueryString();

        return view('admin.products.index', ['products' => $products, 'statuses' => Product::STATUSES, 'selectedStatus' => in_array($status, Product::STATUSES, true) ? $status : '', 'search' => $search]);
    }

    public function create(): View
    {
        $product = new Product(['status' => 'draft', 'sort_order' => 0]);
        return view('admin.catalog.form', $this->formData($product, route('admin.products.store'), 'POST'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $product = Product::query()->create($this->withoutContentMediaFields($data));
        $this->saveCoverImage($request, $product);
        return redirect()->route('admin.products.edit', $product)->with('status', 'Product saved successfully.');
    }

    public function edit(Product $product): View
    {
        return view('admin.catalog.form', $this->formData($product, route('admin.products.update', $product), 'PUT'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedData($request, $product);
        $product->update($this->withoutContentMediaFields($data));
        $this->saveCoverImage($request, $product);
        return redirect()->route('admin.products.edit', $product)->with('status', 'Product updated successfully.');
    }

    private function formData(Product $entity, string $formAction, string $httpMethod): array
    {
        return [
            'entity' => $entity, 'catalogLabel' => 'Product',
            'indexRoute' => 'admin.products.index', 'formAction' => $formAction, 'httpMethod' => $httpMethod,
            'statuses' => Product::STATUSES, 'urlField' => 'demo_url', 'urlLabel' => 'Demo URL (optional)',
            'localizedFields' => [
                ['key' => 'title', 'type' => 'input', 'max' => 180, 'label_en' => 'Product title', 'label_ar' => 'اسم المنتج'],
                ['key' => 'slug', 'type' => 'input', 'max' => 180, 'label_en' => 'URL slug (lowercase ASCII)', 'label_ar' => 'رابط الصفحة (حروف إنجليزية)'],
                ['key' => 'summary', 'type' => 'textarea', 'max' => 2000, 'label_en' => 'Summary', 'label_ar' => 'ملخص'],
                ['key' => 'body', 'type' => 'textarea', 'max' => 30000, 'label_en' => 'Description / features', 'label_ar' => 'الوصف والمميزات'],
                ['key' => 'requirements', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'Prerequisites', 'label_ar' => 'المتطلبات المسبقة'],
                ['key' => 'license_terms', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'License terms (required before publication)', 'label_ar' => 'شروط الترخيص (مطلوبة للنشر)'],
                ['key' => 'delivery_terms', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'Delivery terms (required before publication)', 'label_ar' => 'شروط التسليم (مطلوبة للنشر)'],
                ['key' => 'support_terms', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'Support terms (required before publication)', 'label_ar' => 'شروط الدعم (مطلوبة للنشر)'],
                ['key' => 'update_terms', 'type' => 'textarea', 'max' => 10000, 'label_en' => 'Updates / maintenance terms', 'label_ar' => 'التحديثات والصيانة'],
                ['key' => 'price_note', 'type' => 'input', 'max' => 180, 'label_en' => 'Price note (optional; no assumed price)', 'label_ar' => 'ملاحظة السعر (اختياري)'],
            ],
        ];
    }

    private function validatedData(Request $request, ?Product $product = null): array
    {
        $rules = [
            'demo_url' => ['nullable', 'url:http,https', 'max:2048'],
            'status' => ['required', Rule::in(Product::STATUSES)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'published_at' => ['nullable', 'date'],
        ];
        foreach (['en', 'ar'] as $locale) {
            $unique = Rule::unique('products', "slug_{$locale}");
            if ($product) $unique->ignore($product->getKey());
            $rules["title_{$locale}"] = ['nullable', 'string', 'max:180'];
            $rules["slug_{$locale}"] = ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $unique];
            $rules["summary_{$locale}"] = ['nullable', 'string', 'max:2000'];
            foreach (['body', 'requirements', 'license_terms', 'delivery_terms', 'support_terms', 'update_terms'] as $field) $rules["{$field}_{$locale}"] = ['nullable', 'string', 'max:30000'];
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
