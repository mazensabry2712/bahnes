<?php

return [
    // Keep originals and generated previews on Laravel's public disk.
    'disk_name' => env('MEDIA_DISK', 'public'),

    // Public-facing image uploads are limited to 5 MiB by validation and the library.
    'max_file_size' => 5 * 1024 * 1024,

    // Image previews are generated synchronously to avoid requiring a queue worker.
    // Revisit queued conversions only when production queue workers are configured.
    'queue_conversions_by_default' => false,

    'image_driver' => env('IMAGE_DRIVER', 'gd'),
    'version_urls' => true,
];
