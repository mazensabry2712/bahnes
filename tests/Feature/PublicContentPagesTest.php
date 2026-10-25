<?php

use App\Models\Insight;
use App\Models\InsightCategory;
use App\Models\Project;
use App\Models\Product;
use App\Models\WebsiteTemplate;
use App\Models\Service;
use App\Models\ServiceLine;

function publicWebsiteServiceLine(): ServiceLine
{
    return ServiceLine::query()->create([
        'key' => 'software-digital-solutions',
        'name_en' => 'Software & Digital Solutions',
        'name_ar' => 'الحلول البرمجية والرقمية',
        'summary_en' => 'Software and digital work.',
        'summary_ar' => 'الحلول البرمجية والرقمية.',
        'is_active' => true,
        'sort_order' => 1,
    ]);
}

it('renders the bilingual homepage with localized navigation and no fabricated proof points', function () {
    $this->get(route('localized.home', ['locale' => 'en']))
        ->assertOk()
        ->assertSee('Technology that works for your business.')
        ->assertSee('Home')
        ->assertSee(route('localized.home', ['locale' => 'en']), false)
        ->assertSee('Services')
        ->assertSee('SYSTEMS / PEOPLE / OUTCOMES')
        ->assertDontSee("30° 02' N / 31° 14' E")
        ->assertDontSee('Trusted by 500+ companies');

    $this->get(route('localized.home', ['locale' => 'ar']))
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false)
        ->assertSee(route('localized.home', ['locale' => 'ar']), false)
        ->assertSee('الرئيسية')
        ->assertSee('تقنية تعمل لصالح أعمالك.')
        ->assertSee('الأنظمة / الأشخاص / النتائج')
        ->assertDontSee("30° 02' N / 31° 14' E");
});

it('renders the contact page inside the shared bilingual public layout', function () {
    $this->get(route('localized.contact.show', ['locale' => 'en']))
        ->assertOk()
        ->assertSee('Talk to BAHNES')
        ->assertSee('Services')
        ->assertSee(route('public.content.index', ['locale' => 'en', 'section' => 'services']), false)
        ->assertDontSee('robots.txt')
        ->assertSee('name="message"', false)
        ->assertSee('We use your details to respond to this inquiry.')
        ->assertDontSee('Review company privacy information before launch.')
        ->assertSee('hreflang="ar-EG"', false)
        ->assertSee('© ' . now()->year . ' BAHNES IT SOLUTIONS');

    $this->get(route('localized.contact.show', ['locale' => 'ar']))
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false)
        ->assertSee('تواصل مع BAHNES')
        ->assertSee('نستخدم بياناتك للرد على هذا الاستفسار.')
        ->assertSee('lang="en"', false)
        ->assertSee('© ' . now()->year . ' BAHNES IT SOLUTIONS');
});

it('lists only published services with a complete translation and active service line', function () {
    $line = publicWebsiteServiceLine();

    Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Managed Network Support',
        'slug_en' => 'managed-network-support',
        'summary_en' => 'Support for business networks.',
        'status' => 'published',
        'sort_order' => 1,
    ]);
    Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Hidden Draft',
        'slug_en' => 'hidden-draft',
        'status' => 'draft',
        'sort_order' => 2,
    ]);
    $inactiveLine = ServiceLine::query()->create([
        'key' => 'inactive-public-test',
        'name_en' => 'Inactive service line',
        'name_ar' => 'مجال خدمة غير نشط',
        'is_active' => false,
        'sort_order' => 2,
    ]);

    Service::query()->create([
        'service_line_id' => $inactiveLine->id,
        'title_en' => 'Inactive Service',
        'slug_en' => 'inactive-service',
        'status' => 'published',
        'sort_order' => 3,
    ]);

    $this->get(route('public.content.index', ['locale' => 'en', 'section' => 'services']))
        ->assertOk()
        ->assertSee('Home <span aria-hidden="true">/</span>', false)
        ->assertSee('Managed Network Support')
        ->assertDontSee('Hidden Draft')
        ->assertDontSee('Inactive Service');

    $this->get(route('public.content.index', ['locale' => 'ar', 'section' => 'services']))
        ->assertOk()
        ->assertSee('الرئيسية <span aria-hidden="true">/</span>', false)
        ->assertDontSee('Managed Network Support');
});

it('localizes pagination controls and direction for both public locales', function () {
    $line = publicWebsiteServiceLine();

    foreach (range(1, 13) as $number) {
        Service::query()->create([
            'service_line_id' => $line->id,
            'title_en' => "Public Service {$number}",
            'slug_en' => "public-service-{$number}",
            'summary_en' => 'A published service for pagination coverage.',
            'title_ar' => "خدمة منشورة {$number}",
            'slug_ar' => "khidma-manshura-{$number}",
            'summary_ar' => 'خدمة منشورة لاختبار تقسيم الصفحات.',
            'status' => 'published',
            'sort_order' => $number,
        ]);
    }

    $this->get(route('public.content.index', ['locale' => 'ar', 'section' => 'services']))
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('التنقل بين الصفحات')
        ->assertSee('السابق')
        ->assertSee('التالي');

    $this->get(route('public.content.index', ['locale' => 'en', 'section' => 'services']))
        ->assertOk()
        ->assertSee('dir="ltr"', false)
        ->assertSee('Pagination Navigation')
        ->assertSee('Previous')
        ->assertSee('Next');
});

it('serves only public project details and clearly labels concepts', function () {
    Project::query()->create([
        'title_en' => 'Network Improvement Concept',
        'slug_en' => 'network-improvement-concept',
        'title_ar' => 'تصور تطوير الشبكة',
        'slug_ar' => 'tasawor-tatweer-alshabaka',
        'summary_en' => 'A proposed improvement direction.',
        'summary_ar' => 'تصور مبدئي لتحسين الشبكة.',
        'challenge_en' => 'A growing business needs a clear network plan.',
        'approach_en' => 'Review site layout, capacity and support needs.',
        'project_status' => 'concept',
        'status' => 'published',
        'sort_order' => 0,
    ]);

    Project::query()->create([
        'title_en' => 'Confidential Project',
        'slug_en' => 'confidential-project',
        'project_status' => 'confidential',
        'display_permission_confirmed' => true,
        'status' => 'published',
        'sort_order' => 1,
    ]);

    Project::query()->create([
        'title_en' => 'Draft Project',
        'slug_en' => 'draft-project',
        'project_status' => 'concept',
        'status' => 'draft',
        'sort_order' => 2,
    ]);

    $this->get(route('public.content.show', [
        'locale' => 'en',
        'section' => 'projects',
        'slug' => 'network-improvement-concept',
    ]))
        ->assertOk()
        ->assertSee('Network Improvement Concept')
        ->assertSee('This is a concept, not a delivered client project.')
        ->assertSee('A growing business needs a clear network plan.');

    $this->get(route('public.content.show', [
        'locale' => 'ar',
        'section' => 'projects',
        'slug' => 'tasawor-tatweer-alshabaka',
    ]))
        ->assertOk()
        ->assertSee('تصور تطوير الشبكة')
        ->assertSee('تصور توضيحي')
        ->assertDontSee('تصور / Concept');

    $this->get(route('public.content.show', [
        'locale' => 'en',
        'section' => 'projects',
        'slug' => 'confidential-project',
    ]))->assertNotFound();

    $this->get(route('public.content.show', [
        'locale' => 'en',
        'section' => 'projects',
        'slug' => 'draft-project',
    ]))->assertNotFound();
});

it('escapes article text and only provides language alternates for a real published translation', function () {
    $category = InsightCategory::query()->create([
        'name_en' => 'Guides',
        'name_ar' => 'أدلة',
        'slug_en' => 'guides-public',
        'slug_ar' => 'adella-public',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $insight = Insight::query()->create([
        'insight_category_id' => $category->id,
        'title_en' => 'Plan a Secure Network',
        'slug_en' => 'plan-secure-network',
        'excerpt_en' => 'Key questions before deployment.',
        'body_en' => '<script>alert("unsafe")</script>Separate trusted and guest access.',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    $this->get(route('public.content.show', [
        'locale' => 'en',
        'section' => 'insights',
        'slug' => $insight->slug_en,
    ]))
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(&quot;unsafe&quot;)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert("unsafe")</script>', false)
        ->assertDontSee('hreflang="ar-EG"', false);

    $insight->update([
        'title_ar' => 'تخطيط شبكة آمنة',
        'slug_ar' => 'takhteet-shabaka-amina',
        'excerpt_ar' => 'أسئلة مهمة قبل التنفيذ.',
        'body_ar' => 'افصل بين وصول الموظفين والضيوف.',
    ]);

    $this->get(route('public.content.show', [
        'locale' => 'en',
        'section' => 'insights',
        'slug' => $insight->slug_en,
    ]))
        ->assertOk()
        ->assertSee(route('public.content.show', [
            'locale' => 'ar',
            'section' => 'insights',
            'slug' => 'takhteet-shabaka-amina',
        ]), false)
        ->assertSee('hreflang="ar-EG"', false);
});

it('adds only published locale variants to the sitemap', function () {
    $line = publicWebsiteServiceLine();

    Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Published Service',
        'slug_en' => 'published-service',
        'title_ar' => 'خدمة منشورة',
        'slug_ar' => 'khidma-manshura',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Draft Sitemap Service',
        'slug_en' => 'draft-sitemap-service',
        'status' => 'draft',
        'sort_order' => 2,
    ]);

    $xml = $this->get(route('sitemap'))->assertOk()->getContent();

    expect($xml)
        ->toContain(route('public.content.show', ['locale' => 'en', 'section' => 'services', 'slug' => 'published-service']))
        ->toContain(route('public.content.show', ['locale' => 'ar', 'section' => 'services', 'slug' => 'khidma-manshura']))
        ->not->toContain('draft-sitemap-service');
});


it('publishes only complete live products and website templates on public pages', function () {
    Product::query()->create([
        'title_en' => 'Published Business Product',
        'slug_en' => 'published-business-product',
        'summary_en' => 'A complete public product summary.',
        'license_terms_en' => 'License terms are available.',
        'delivery_terms_en' => 'Delivery terms are available.',
        'support_terms_en' => 'Support terms are available.',
        'status' => 'published',
        'sort_order' => 1,
    ]);
    Product::query()->create([
        'title_en' => 'Future Business Product',
        'slug_en' => 'future-business-product',
        'summary_en' => 'This product is scheduled for the future.',
        'license_terms_en' => 'License terms.',
        'delivery_terms_en' => 'Delivery terms.',
        'support_terms_en' => 'Support terms.',
        'status' => 'published',
        'published_at' => now()->addDay(),
        'sort_order' => 2,
    ]);
    Product::query()->create([
        'title_en' => 'Draft Business Product',
        'slug_en' => 'draft-business-product',
        'summary_en' => 'This draft must stay private.',
        'license_terms_en' => 'License terms.',
        'delivery_terms_en' => 'Delivery terms.',
        'support_terms_en' => 'Support terms.',
        'status' => 'draft',
        'sort_order' => 3,
    ]);

    WebsiteTemplate::query()->create([
        'title_en' => 'Published Corporate Template',
        'slug_en' => 'published-corporate-template',
        'summary_en' => 'A public template with complete commercial terms.',
        'license_terms_en' => 'Single-site license.',
        'delivery_terms_en' => 'Source package delivery.',
        'support_terms_en' => 'Email support.',
        'status' => 'published',
        'sort_order' => 1,
    ]);
    WebsiteTemplate::query()->create([
        'title_en' => 'Draft Corporate Template',
        'slug_en' => 'draft-corporate-template',
        'summary_en' => 'This draft must stay private.',
        'license_terms_en' => 'License terms.',
        'delivery_terms_en' => 'Delivery terms.',
        'support_terms_en' => 'Support terms.',
        'status' => 'draft',
        'sort_order' => 2,
    ]);

    $this->get(route('public.content.index', ['locale' => 'en', 'section' => 'products']))
        ->assertOk()
        ->assertSee('Published Business Product')
        ->assertDontSee('Future Business Product')
        ->assertDontSee('Draft Business Product');

    $this->get(route('public.content.show', [
        'locale' => 'en',
        'section' => 'products',
        'slug' => 'published-business-product',
    ]))->assertOk()->assertSee('Published Business Product');

    $this->get(route('public.content.index', ['locale' => 'en', 'section' => 'templates']))
        ->assertOk()
        ->assertSee('Published Corporate Template')
        ->assertDontSee('Draft Corporate Template');

    $this->get(route('public.content.show', [
        'locale' => 'en',
        'section' => 'templates',
        'slug' => 'draft-corporate-template',
    ]))->assertNotFound();
});
