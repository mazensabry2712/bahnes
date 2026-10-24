<?php

use App\Models\Service;
use App\Models\ServiceLine;
use Database\Seeders\ServiceLineSeeder;

it('seeds the two agreed service lines without creating demo accounts', function () {
    $this->seed(ServiceLineSeeder::class);

    expect(ServiceLine::query()->count())->toBe(2)
        ->and(ServiceLine::query()->where('key', 'it-infrastructure-security')->exists())->toBeTrue()
        ->and(ServiceLine::query()->where('key', 'software-digital-solutions')->exists())->toBeTrue();
});

it('returns only published services with a usable translation for the requested locale', function () {
    $line = ServiceLine::query()->create([
        'key' => 'software',
        'name_en' => 'Software & Digital Solutions',
        'name_ar' => 'الحلول البرمجية والرقمية',
    ]);

    $translated = Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Business Website',
        'title_ar' => 'موقع أعمال',
        'slug_en' => 'business-website',
        'slug_ar' => 'business-website-ar',
        'status' => 'published',
    ]);

    $englishOnly = Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'API Integration',
        'slug_en' => 'api-integration',
        'status' => 'published',
    ]);

    $draft = Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Internal Draft',
        'title_ar' => 'مسودة داخلية',
        'slug_en' => 'internal-draft',
        'slug_ar' => 'internal-draft-ar',
        'status' => 'draft',
    ]);

    expect(Service::query()->publishedForLocale('en')->pluck('id')->all())
        ->toContain($translated->id, $englishOnly->id)
        ->not->toContain($draft->id);

    expect(Service::query()->publishedForLocale('ar')->pluck('id')->all())
        ->toContain($translated->id)
        ->not->toContain($englishOnly->id, $draft->id);
});

it('does not return a scheduled service before its publication time', function () {
    $line = ServiceLine::query()->create([
        'key' => 'it',
        'name_en' => 'IT Infrastructure & Security',
        'name_ar' => 'البنية التحتية لتقنية المعلومات وأنظمة الأمن',
    ]);

    $scheduled = Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Network Setup',
        'title_ar' => 'إعداد الشبكات',
        'slug_en' => 'network-setup',
        'slug_ar' => 'network-setup-ar',
        'status' => 'published',
        'published_at' => now()->addDay(),
    ]);

    expect(Service::query()->publishedForLocale('en')->whereKey($scheduled->id)->exists())
        ->toBeFalse();
});

it('does not publish services under an inactive service line', function () {
    $line = ServiceLine::query()->create([
        'key' => 'disabled-line',
        'name_en' => 'Disabled Line',
        'name_ar' => 'خدمة غير نشطة',
        'is_active' => false,
    ]);

    $service = Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Hidden Service',
        'title_ar' => 'خدمة مخفية',
        'slug_en' => 'hidden-service',
        'slug_ar' => 'hidden-service-ar',
        'status' => 'published',
    ]);

    expect(Service::query()->publishedForLocale('en')->whereKey($service->id)->exists())
        ->toBeFalse();
});

it('rejects unsupported locales instead of querying arbitrary column names', function () {
    expect(fn () => Service::query()->publishedForLocale('fr'))
        ->toThrow(InvalidArgumentException::class);
});
