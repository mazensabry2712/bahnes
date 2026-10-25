<?php

use Illuminate\Support\Facades\Storage;

it('serves uploaded CMS images from the root-level media disk', function () {
    Storage::fake('bahnes_images');
    Storage::disk('bahnes_images')->put('42/cover.webp', 'fake-image-content');

    $this->get('/media/42/cover.webp')
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Cache-Control', 'public, max-age=31536000, immutable');
});

it('does not expose unsupported files through the media route', function () {
    Storage::fake('bahnes_images');
    Storage::disk('bahnes_images')->put('42/private.php', '<?php echo "no";');

    $this->get('/media/42/private.php')->assertNotFound();
});

it('configures CMS media to use the root-level Bahnes_img directory', function () {
    expect(config('filesystems.disks.bahnes_images.root'))->toBe(base_path('Bahnes_img'))
        ->and(config('media-library.disk_name'))->toBe('bahnes_images');
});
