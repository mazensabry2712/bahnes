<?php

use Illuminate\Support\Facades\Storage;

it('serves uploaded CMS images from the root-level media disk', function () {
    Storage::fake('bahnes_images');
    Storage::disk('bahnes_images')->put('42/cover.webp', 'fake-image-content');

    $this->get('/media/42/cover.webp')
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');
});

it('does not expose unsupported files through the media route', function () {
    Storage::fake('bahnes_images');
    Storage::disk('bahnes_images')->put('42/private.php', '<?php echo "no";');

    $this->get('/media/42/private.php')->assertNotFound();
});

it('does not serve a valid image path when the file is missing', function () {
    Storage::fake('bahnes_images');

    $this->get('/media/42/missing.jpg')->assertNotFound();
});

it('rejects nested paths outside the media-library layout', function () {
    Storage::fake('bahnes_images');
    Storage::disk('bahnes_images')->put('42/nested/cover.webp', 'fake-image-content');

    $this->get('/media/42/nested/cover.webp')->assertNotFound();
});

it('configures CMS media to use the root-level Bahnes_img directory', function () {
    expect(config('filesystems.disks.bahnes_images.root'))->toBe(base_path('Bahnes_img'))
        ->and(config('media-library.disk_name'))->toBe('bahnes_images');
});
