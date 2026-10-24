<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceLine;
use App\Models\WebsiteTemplate;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class PublicWebsiteController extends Controller
{
    public function root(): RedirectResponse
    {
        return redirect()->route('localized.home', ['locale' => 'en']);
    }

    public function home(string $locale): View
    {
        $this->assertLocale($locale);

        $copy = $this->homeCopy($locale);
        $alternates = [
            'en' => route('localized.home', ['locale' => 'en']),
            'ar' => route('localized.home', ['locale' => 'ar']),
        ];

        return view('website.home', array_merge($this->layoutData(
            $locale,
            $copy['title'],
            $copy['description'],
            route('localized.home', ['locale' => $locale]),
            $alternates,
        ), [
            'copy' => $copy,
            'serviceLines' => ServiceLine::query()->active()->orderBy('sort_order')->get(),
            'services' => Service::query()->publishedForLocale($locale)
                ->with(['serviceLine', 'media'])
                ->orderBy('sort_order')->orderByDesc('published_at')->limit(3)->get(),
            'projects' => Project::query()->publishedForLocale($locale)
                ->with('media')
                ->orderBy('sort_order')->orderByDesc('published_at')->limit(3)->get(),
            'products' => Product::query()->publishedForLocale($locale)
                ->with('media')
                ->orderBy('sort_order')->orderByDesc('published_at')->limit(3)->get(),
            'templates' => WebsiteTemplate::query()->publishedForLocale($locale)
                ->with('media')
                ->orderBy('sort_order')->orderByDesc('published_at')->limit(3)->get(),
            'insights' => Insight::query()->publishedForLocale($locale)
                ->with(['category', 'media'])
                ->orderBy('sort_order')->orderByDesc('published_at')->limit(3)->get(),
        ]));
    }

    public function index(string $locale, string $section): View
    {
        $this->assertLocale($locale);
        $config = $this->sectionCopy($section, $locale);

        $items = $this->contentQuery($section, $locale)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $alternates = [
            'en' => route('public.content.index', ['locale' => 'en', 'section' => $section]),
            'ar' => route('public.content.index', ['locale' => 'ar', 'section' => $section]),
        ];

        return view('website.content.index', array_merge($this->layoutData(
            $locale,
            $config['title'],
            $config['description'],
            route('public.content.index', ['locale' => $locale, 'section' => $section]),
            $alternates,
        ), [
            'section' => $section,
            'config' => $config,
            'items' => $items,
            'languageSwitchUrl' => $alternates[$locale === 'en' ? 'ar' : 'en'],
        ]));
    }

    public function show(string $locale, string $section, string $slug): View
    {
        $this->assertLocale($locale);
        $item = $this->contentQuery($section, $locale)
            ->where("slug_{$locale}", $slug)
            ->firstOrFail();

        $config = $this->sectionCopy($section, $locale);
        $title = (string) $item->getAttribute("title_{$locale}");
        $summaryField = $section === 'insights' ? 'excerpt' : 'summary';
        $summary = (string) ($item->getAttribute("{$summaryField}_{$locale}") ?? '');
        $metaTitle = $item->getAttribute("meta_title_{$locale}") ?: $title;
        $metaDescription = $item->getAttribute("meta_description_{$locale}") ?: $summary;

        $alternates = [];
        $languageSwitchUrl = route('public.content.index', [
            'locale' => $locale === 'en' ? 'ar' : 'en',
            'section' => $section,
        ]);

        foreach (['en', 'ar'] as $alternateLocale) {
            if ($this->contentQuery($section, $alternateLocale)->whereKey($item->getKey())->exists()) {
                $alternateSlug = (string) $item->getAttribute("slug_{$alternateLocale}");
                $alternates[$alternateLocale] = route('public.content.show', [
                    'locale' => $alternateLocale,
                    'section' => $section,
                    'slug' => $alternateSlug,
                ]);
            }
        }

        if (isset($alternates[$locale === 'en' ? 'ar' : 'en'])) {
            $languageSwitchUrl = $alternates[$locale === 'en' ? 'ar' : 'en'];
        }

        return view('website.content.show', array_merge($this->layoutData(
            $locale,
            (string) $metaTitle,
            (string) $metaDescription,
            route('public.content.show', ['locale' => $locale, 'section' => $section, 'slug' => $slug]),
            $alternates,
            $item->coverImageUrl(),
        ), [
            'section' => $section,
            'config' => $config,
            'item' => $item,
            'itemTitle' => $title,
            'summary' => $summary,
            'sections' => $this->detailSections($item, $section, $locale),
            'coverUrl' => $item->coverImageUrl(),
            'coverAlt' => $item->coverImageAlt($locale),
            'languageSwitchUrl' => $languageSwitchUrl,
        ]));
    }

    /** @return Builder<Service>|Builder<Project>|Builder<Product>|Builder<WebsiteTemplate>|Builder<Insight> */
    private function contentQuery(string $section, string $locale): Builder
    {
        $this->assertLocale($locale);

        return match ($section) {
            'services' => Service::query()->publishedForLocale($locale)->with(['serviceLine', 'media']),
            'projects' => Project::query()->publishedForLocale($locale)->with('media'),
            'products' => Product::query()->publishedForLocale($locale)->with('media'),
            'templates' => WebsiteTemplate::query()->publishedForLocale($locale)->with('media'),
            'insights' => Insight::query()->publishedForLocale($locale)->with(['category', 'media']),
            default => throw new InvalidArgumentException('Unsupported public content section.'),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function layoutData(
        string $locale,
        string $pageTitle,
        string $description,
        string $canonical,
        array $alternates = [],
        ?string $imageUrl = null,
    ): array {
        $isArabic = $locale === 'ar';

        return [
            'locale' => $locale,
            'isArabic' => $isArabic,
            'pageTitle' => $pageTitle,
            'metaDescription' => $description,
            'canonicalUrl' => $canonical,
            'alternateUrls' => $alternates,
            'ogImageUrl' => $imageUrl,
            'languageSwitchUrl' => $alternates[$isArabic ? 'en' : 'ar'] ?? route('localized.home', ['locale' => $isArabic ? 'en' : 'ar']),
            'navLabels' => $isArabic ? [
                'home' => 'الرئيسية',
                'services' => 'الخدمات',
                'projects' => 'المشروعات',
                'products' => 'المنتجات',
                'templates' => 'قوالب المواقع',
                'insights' => 'المقالات',
                'contact' => 'تواصل معنا',
                'language' => 'EN',
            ] : [
                'home' => 'Home',
                'services' => 'Services',
                'projects' => 'Projects',
                'products' => 'Products',
                'templates' => 'Website templates',
                'insights' => 'Insights',
                'contact' => 'Contact',
                'language' => 'عربي',
            ],
        ];
    }

    private function assertLocale(string $locale): void
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            throw new InvalidArgumentException('Unsupported website locale.');
        }
    }

    /**
     * @return array<string, string>
     */
    private function homeCopy(string $locale): array
    {
        return $locale === 'ar' ? [
            'eyebrow' => 'البنية التحتية · الأمن · الحلول البرمجية',
            'title' => 'تقنية تعمل لصالح أعمالك.',
            'description' => 'حلول تقنية وأمنية وبرمجية عملية، تبدأ بفهم احتياجك وتنتهي بنظام يمكنك الاعتماد عليه.',
            'intro' => 'نجمع بين البنية التحتية لتقنية المعلومات وأنظمة الأمن من جهة، والحلول البرمجية والرقمية من جهة أخرى، ضمن تجربة واضحة ومتكاملة.',
            'primaryCta' => 'ناقش مشروعك',
            'secondaryCta' => 'استكشف خدماتنا',
            'capabilities' => 'مجالات عملنا',
            'services' => 'خدمات مختارة',
            'projects' => 'مشروعات وأعمال منشورة',
            'catalog' => 'المنتجات الرقمية',
            'templates' => 'قوالب المواقع',
            'insights' => 'أدلة ورؤى',
            'viewAll' => 'عرض الكل',
            'learnMore' => 'اعرف المزيد',
            'contact' => 'تحدث معنا',
            'empty' => 'نعمل على إعداد محتوى هذا القسم. عُد قريبًا للاطلاع على التحديثات.',
            'serviceLineServices' => 'استكشف الخدمات',
        ] : [
            'eyebrow' => 'IT INFRASTRUCTURE · SECURITY · SOFTWARE',
            'title' => 'Technology that works for your business.',
            'description' => 'Practical IT, security and software solutions that start with your needs and end with systems your team can rely on.',
            'intro' => 'We bring IT infrastructure and security together with software and digital solutions in one clear, integrated experience.',
            'primaryCta' => 'Discuss a project',
            'secondaryCta' => 'Explore services',
            'capabilities' => 'What we do',
            'services' => 'Selected services',
            'projects' => 'Published projects',
            'catalog' => 'Digital products',
            'templates' => 'Website templates',
            'insights' => 'Guides & insights',
            'viewAll' => 'View all',
            'learnMore' => 'Learn more',
            'contact' => 'Talk to us',
            'empty' => 'This section is being prepared. Check back for updates.',
            'serviceLineServices' => 'Explore services',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function sectionCopy(string $section, string $locale): array
    {
        $copy = [
            'en' => [
                'services' => ['title' => 'Services', 'description' => 'IT infrastructure, security and software services shaped around real business needs.', 'eyebrow' => 'WHAT WE DO', 'intro' => 'Choose the area closest to your need. Scope and delivery are agreed clearly before work begins.', 'singular' => 'Service', 'emptyTitle' => 'Services are being prepared', 'emptyText' => 'Our service catalog is being prepared. Contact us to discuss your requirements.'],
                'projects' => ['title' => 'Projects & case studies', 'description' => 'Published work and concepts from BAHNES IT SOLUTIONS.', 'eyebrow' => 'OUR WORK', 'intro' => 'Explore work that is approved for public display. Concepts are labeled clearly; confidential or unapproved client work is never shown.', 'singular' => 'Project', 'emptyTitle' => 'No projects published yet', 'emptyText' => 'Published work will appear here when it is ready to share.'],
                'products' => ['title' => 'Software products', 'description' => 'Software products with clear scope, licensing, delivery and support terms.', 'eyebrow' => 'DIGITAL PRODUCTS', 'intro' => 'Explore products only when their offer and commercial terms are ready. No price or checkout is assumed.', 'singular' => 'Product', 'emptyTitle' => 'No products published yet', 'emptyText' => 'Products will appear here after their scope and terms are finalized.'],
                'templates' => ['title' => 'Website templates', 'description' => 'Website templates with defined deliverables, license terms and support boundaries.', 'eyebrow' => 'WEBSITE TEMPLATES', 'intro' => 'Browse reusable website starting points. Each published template explains what is included and how delivery and support work.', 'singular' => 'Template', 'emptyTitle' => 'No templates published yet', 'emptyText' => 'Templates will appear here after their previews and terms are ready.'],
                'insights' => ['title' => 'Insights & guides', 'description' => 'Practical IT, security and software guides from BAHNES IT SOLUTIONS.', 'eyebrow' => 'INSIGHTS', 'intro' => 'Useful ideas and practical guides to help teams plan their technology with more confidence.', 'singular' => 'Article', 'emptyTitle' => 'No articles published yet', 'emptyText' => 'Reviewed articles will appear here when ready.'],
            ],
            'ar' => [
                'services' => ['title' => 'الخدمات', 'description' => 'خدمات البنية التحتية لتقنية المعلومات والأمن والحلول البرمجية وفق احتياجات العمل.', 'eyebrow' => 'ماذا نقدم', 'intro' => 'اختر المجال الأقرب لاحتياجك. نتفق بوضوح على نطاق العمل وطريقة التنفيذ قبل البدء.', 'singular' => 'خدمة', 'emptyTitle' => 'يجري تجهيز الخدمات', 'emptyText' => 'نعمل على تجهيز كتالوج الخدمات. تواصل معنا لمناقشة احتياجك.'],
                'projects' => ['title' => 'المشروعات ودراسات الحالة', 'description' => 'الأعمال والتصورات المنشورة والمعتمدة للعرض العام من BAHNES IT SOLUTIONS.', 'eyebrow' => 'أعمالنا', 'intro' => 'استكشف الأعمال المسموح بعرضها علنًا. نوضح أن التصورات مجرد مفاهيم، ولا نعرض أعمال العملاء السرية أو غير المعتمدة.', 'singular' => 'مشروع', 'emptyTitle' => 'لا توجد مشروعات منشورة بعد', 'emptyText' => 'ستظهر الأعمال هنا عندما تصبح جاهزة للمشاركة.'],
                'products' => ['title' => 'المنتجات البرمجية', 'description' => 'منتجات برمجية بنطاق وترخيص وتسليم ودعم واضح.', 'eyebrow' => 'منتجات رقمية', 'intro' => 'استكشف المنتجات بعد اعتماد العرض والشروط التجارية، دون افتراض أسعار أو تفعيل شراء إلكتروني.', 'singular' => 'منتج', 'emptyTitle' => 'لا توجد منتجات منشورة بعد', 'emptyText' => 'ستظهر المنتجات بعد استكمال نطاقها وشروطها.'],
                'templates' => ['title' => 'قوالب المواقع', 'description' => 'قوالب مواقع بمخرجات وترخيص وتسليم وحدود دعم واضحة.', 'eyebrow' => 'قوالب المواقع', 'intro' => 'استكشف قوالب قابلة لإعادة الاستخدام، مع توضيح الملفات المشمولة وشروط التسليم والدعم.', 'singular' => 'قالب', 'emptyTitle' => 'لا توجد قوالب منشورة بعد', 'emptyText' => 'ستظهر القوالب عندما تصبح المعاينات والشروط جاهزة.'],
                'insights' => ['title' => 'المقالات والأدلة', 'description' => 'أدلة عملية في تقنية المعلومات والأمن والحلول البرمجية من BAHNES IT SOLUTIONS.', 'eyebrow' => 'المعرفة التقنية', 'intro' => 'أفكار وأدلة عملية تساعد الفرق على تخطيط التقنية واتخاذ قرارات أوضح.', 'singular' => 'مقال', 'emptyTitle' => 'لا توجد مقالات منشورة بعد', 'emptyText' => 'ستظهر المقالات بعد مراجعتها وتجهيزها للنشر.'],
            ],
        ];

        if (! isset($copy[$locale][$section])) {
            throw new InvalidArgumentException('Unsupported public content section.');
        }

        return $copy[$locale][$section];
    }

    /**
     * @return array<int, array{title: string, body: string}>
     */
    private function detailSections(object $item, string $section, string $locale): array
    {
        $definitions = match ($section) {
            'services' => [['body', $locale === 'ar' ? 'تفاصيل الخدمة' : 'Service details']],
            'projects' => [
                ['challenge', $locale === 'ar' ? 'التحدي' : 'The challenge'],
                ['approach', $locale === 'ar' ? 'طريقة التنفيذ' : 'Our approach'],
                ['outcome', $locale === 'ar' ? 'النتيجة' : 'Outcome'],
                ['deliverables', $locale === 'ar' ? 'المخرجات' : 'Deliverables'],
            ],
            'products' => [
                ['body', $locale === 'ar' ? 'الوصف والمميزات' : 'Description & features'],
                ['requirements', $locale === 'ar' ? 'المتطلبات' : 'Requirements'],
                ['license_terms', $locale === 'ar' ? 'شروط الترخيص' : 'License terms'],
                ['delivery_terms', $locale === 'ar' ? 'التسليم' : 'Delivery'],
                ['support_terms', $locale === 'ar' ? 'الدعم' : 'Support'],
                ['update_terms', $locale === 'ar' ? 'التحديثات والصيانة' : 'Updates & maintenance'],
                ['price_note', $locale === 'ar' ? 'ملاحظة السعر' : 'Pricing note'],
            ],
            'templates' => [
                ['description', $locale === 'ar' ? 'الوصف' : 'Description'],
                ['included_pages', $locale === 'ar' ? 'الصفحات والملفات المشمولة' : 'Included pages & files'],
                ['license_terms', $locale === 'ar' ? 'شروط الترخيص' : 'License terms'],
                ['delivery_terms', $locale === 'ar' ? 'التسليم' : 'Delivery'],
                ['support_terms', $locale === 'ar' ? 'الدعم' : 'Support'],
                ['customization', $locale === 'ar' ? 'التخصيص' : 'Customization'],
                ['price_note', $locale === 'ar' ? 'ملاحظة السعر' : 'Pricing note'],
            ],
            'insights' => [['body', $locale === 'ar' ? 'المقال' : 'Article']],
            default => [],
        };

        $sections = [];
        foreach ($definitions as [$field, $title]) {
            $body = trim((string) $item->getAttribute("{$field}_{$locale}"));
            if ($body !== '') {
                $sections[] = ['title' => $title, 'body' => $body];
            }
        }

        return $sections;
    }
}
