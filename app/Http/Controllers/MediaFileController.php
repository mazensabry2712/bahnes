<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaFileController extends Controller
{
    /**
     * Serve validated image files from the root-level Bahnes_img disk.
     *
     * The storage directory is outside public/, so files are only exposed
     * through this allow-listed route rather than a public symlink.
     */
    public function show(string $path): StreamedResponse
    {
        abort_unless(
            preg_match('/\A[0-9]+\/(?:conversions\/)?[A-Za-z0-9][A-Za-z0-9._-]*\z/D', $path) === 1,
            404,
        );

        abort_unless(
            in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true),
            404,
        );

        $disk = Storage::disk('bahnes_images');

        abort_unless($disk->exists($path), 404);

        return $disk->response($path, basename($path), [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ], 'inline');
    }
}
