<?php

use App\Models\Service;
use App\Models\ServiceLine;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function createMediaTestServiceLine(): ServiceLine
{
    return ServiceLine::query()->create([
        'key' => 'software-digital-solutions',
        'name_en' => 'Software & Digital Solutions',
        'name_ar' => 'الحلول البرمجية والرقمية',
        'summary_en' => 'Software services',
        'summary_ar' => 'خدمات برمجية',
        'sort_order' => 1,
        'is_active' => true,
    ]);
}

beforeEach(function (): void {
    Storage::fake('public');
    config()->set('media-library.disk_name', 'public');
    config()->set('media-library.queue_conversions_by_default', false);
});

it('uploads a cover image with a generated filename and localized alt text', function () {
    $serviceLine = createMediaTestServiceLine();
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->post(route('admin.services.store'), [
            'service_line_id' => $serviceLine->id,
            'title_en' => 'Network installation',
            'slug_en' => 'network-installation',
            'summary_en' => 'Reliable business networking.',
            'status' => 'draft',
            'sort_order' => 0,
            'cover_image' => UploadedFile::fake()->image('cover.png', 640, 480),
            'cover_alt_en' => 'Network equipment in a rack',
            'cover_alt_ar' => 'معدات الشبكات داخل خزانة',
        ])
        ->assertRedirect();

    $service = Service::query()->firstOrFail();
    $media = $service->getFirstMedia('cover');

    expect($media)->not->toBeNull();
    expect($media->file_name)->not->toBe('cover.png');
    expect($media->getCustomProperty('alt_en'))->toBe('Network equipment in a rack');
    expect($media->getCustomProperty('alt_ar'))->toBe('معدات الشبكات داخل خزانة');
    expect($service->coverImageUrl())->not->toBeNull();
    expect(Storage::disk('public')->allFiles())->not->toBeEmpty();
});

it('can remove a previously uploaded cover image', function () {
    $serviceLine = createMediaTestServiceLine();
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->post(route('admin.services.store'), [
            'service_line_id' => $serviceLine->id,
            'title_en' => 'Camera installation',
            'slug_en' => 'camera-installation',
            'status' => 'draft',
            'sort_order' => 0,
            'cover_image' => UploadedFile::fake()->image('camera.png', 320, 240),
            'cover_alt_en' => 'Camera installation',
        ])
        ->assertRedirect();

    $service = Service::query()->firstOrFail();
    expect($service->getMedia('cover'))->toHaveCount(1);

    $this->actingAs($admin)
        ->put(route('admin.services.update', $service), [
            'service_line_id' => $serviceLine->id,
            'title_en' => 'Camera installation',
            'slug_en' => 'camera-installation',
            'status' => 'draft',
            'sort_order' => 0,
            'remove_cover_image' => '1',
        ])
        ->assertRedirect(route('admin.services.edit', $service));

    expect($service->fresh()->getMedia('cover'))->toHaveCount(0);
});

it('rejects non-image files before storing content', function () {
    $serviceLine = createMediaTestServiceLine();
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->from(route('admin.services.create'))
        ->post(route('admin.services.store'), [
            'service_line_id' => $serviceLine->id,
            'title_en' => 'Network installation',
            'slug_en' => 'network-installation',
            'status' => 'draft',
            'sort_order' => 0,
            'cover_image' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
        ])
        ->assertRedirect(route('admin.services.create'))
        ->assertSessionHasErrors('cover_image');

    expect(Service::query()->count())->toBe(0);
});
