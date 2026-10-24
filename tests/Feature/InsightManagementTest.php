<?php

use App\Models\Insight;
use App\Models\InsightCategory;
use App\Models\User;
use Database\Seeders\InsightCategorySeeder;

function makeInsightAdmin(): User
{
    return User::factory()->create(['is_admin' => true]);
}

function makeInsightCategory(): InsightCategory
{
    return InsightCategory::query()->create([
        'name_en' => 'Guides & Insights',
        'name_ar' => 'أدلة ورؤى تقنية',
        'slug_en' => 'guides-insights-test',
        'slug_ar' => 'adella-test',
        'sort_order' => 10,
        'is_active' => true,
    ]);
}

it('requires administrator access for Insight and category management', function () {
    $this->get(route('admin.insights.index'))->assertRedirect(route('admin.login'));
    $this->get(route('admin.insight-categories.index'))->assertRedirect(route('admin.login'));

    $this->actingAs(User::factory()->create(['is_admin' => false]))
        ->get(route('admin.insights.index'))
        ->assertForbidden();
});

it('seeds the initial bilingual editorial categories without sample articles', function () {
    $this->seed(InsightCategorySeeder::class);

    expect(InsightCategory::query()->count())->toBe(4)
        ->and(Insight::query()->count())->toBe(0);
});

it('saves a bilingual article draft but keeps it out of the published scope', function () {
    $category = makeInsightCategory();

    $this->actingAs(makeInsightAdmin())
        ->post(route('admin.insights.store'), [
            'insight_category_id' => $category->id,
            'title_en' => 'Preparing a Business Network',
            'slug_en' => 'preparing-business-network',
            'excerpt_en' => 'A practical planning guide.',
            'body_en' => 'Review coverage, capacity, business risks, and handover requirements before installation.',
            'title_ar' => 'تجهيز شبكة أعمال',
            'slug_ar' => 'tajheez-shabakat-aamal',
            'excerpt_ar' => 'دليل عملي لتخطيط الشبكات.',
            'body_ar' => 'راجع التغطية والسعة ومخاطر العمل ومتطلبات التسليم قبل التركيب.',
            'status' => 'draft',
            'sort_order' => 0,
        ])->assertRedirect();

    $insight = Insight::query()->firstOrFail();

    expect($insight->status)->toBe('draft')
        ->and(Insight::query()->publishedForLocale('en')->whereKey($insight->id)->exists())->toBeFalse()
        ->and(Insight::query()->publishedForLocale('ar')->whereKey($insight->id)->exists())->toBeFalse();
});

it('requires a complete language and active category before publishing', function () {
    $category = makeInsightCategory();

    $this->actingAs(makeInsightAdmin())
        ->from(route('admin.insights.create'))
        ->post(route('admin.insights.store'), [
            'insight_category_id' => $category->id,
            'title_en' => 'Business Network Planning',
            'slug_en' => 'business-network-planning',
            'excerpt_en' => '',
            'body_en' => '',
            'status' => 'published',
            'sort_order' => 0,
        ])->assertRedirect(route('admin.insights.create'))
          ->assertSessionHasErrors(['status']);

    expect(Insight::query()->count())->toBe(0);
});

it('publishes an article only for the locale with a complete translation', function () {
    $category = makeInsightCategory();

    $this->actingAs(makeInsightAdmin())
        ->post(route('admin.insights.store'), [
            'insight_category_id' => $category->id,
            'title_en' => 'Business Network Planning',
            'slug_en' => 'business-network-planning',
            'excerpt_en' => 'What to plan before installation.',
            'body_en' => 'Plan coverage, capacity, security and maintenance before deploying a business network.',
            'status' => 'published',
            'sort_order' => 0,
        ])->assertRedirect();

    $insight = Insight::query()->firstOrFail();

    expect(Insight::query()->publishedForLocale('en')->whereKey($insight->id)->exists())->toBeTrue()
        ->and(Insight::query()->publishedForLocale('ar')->whereKey($insight->id)->exists())->toBeFalse();
});

it('hides published articles when their category becomes inactive', function () {
    $category = makeInsightCategory();
    $insight = Insight::query()->create([
        'insight_category_id' => $category->id,
        'title_en' => 'IT Guide',
        'slug_en' => 'it-guide-test',
        'excerpt_en' => 'A practical guide.',
        'body_en' => 'Full article body in English.',
        'status' => 'published',
    ]);

    $category->update(['is_active' => false]);

    expect(Insight::query()->publishedForLocale('en')->whereKey($insight->id)->exists())->toBeFalse();
});

it('supports editing an article and archiving it', function () {
    $category = makeInsightCategory();
    $insight = Insight::query()->create([
        'insight_category_id' => $category->id,
        'title_en' => 'Draft Article',
        'slug_en' => 'draft-article-test',
        'excerpt_en' => 'Draft excerpt.',
        'body_en' => 'Draft body.',
        'status' => 'draft',
    ]);

    $this->actingAs(makeInsightAdmin())
        ->put(route('admin.insights.update', $insight), [
            'insight_category_id' => $category->id,
            'title_en' => 'Updated Article',
            'slug_en' => 'updated-article-test',
            'excerpt_en' => 'Updated excerpt.',
            'body_en' => 'Updated body.',
            'status' => 'archived',
            'sort_order' => 1,
        ])->assertRedirect(route('admin.insights.edit', $insight));

    expect($insight->fresh()->title_en)->toBe('Updated Article')
        ->and($insight->fresh()->status)->toBe('archived');
});

it('allows an administrator to manage bilingual category state and slugs', function () {
    $admin = makeInsightAdmin();

    $this->actingAs($admin)
        ->post(route('admin.insight-categories.store'), [
            'name_en' => 'Network Guides',
            'name_ar' => 'أدلة الشبكات',
            'slug_en' => 'network-guides-test',
            'slug_ar' => 'adella-shabakat-test',
            'sort_order' => 5,
            'is_active' => '1',
        ])->assertRedirect();

    $category = InsightCategory::query()->where('slug_en', 'network-guides-test')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('admin.insight-categories.update', $category), [
            'name_en' => 'Network Guides Updated',
            'name_ar' => 'أدلة الشبكات المحدثة',
            'slug_en' => 'network-guides-updated-test',
            'slug_ar' => 'adella-shabakat-updated-test',
            'sort_order' => 5,
        ])->assertRedirect();

    expect($category->fresh()->name_en)->toBe('Network Guides Updated')
        ->and($category->fresh()->is_active)->toBeFalse();
});
