<?php

use App\\Models\\Project;
use App\\Models\\User;
use Illuminate\\Http\\UploadedFile;

it('rejects a non-image upload for a project cover before saving the project', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->from(route('admin.projects.create'))
        ->post(route('admin.projects.store'), [
            'title_en' => 'Upload validation check',
            'slug_en' => 'upload-validation-check',
            'project_status' => 'concept',
            'status' => 'draft',
            'sort_order' => 0,
            'cover_image' => UploadedFile::fake()->create('not-an-image.txt', 2, 'text/plain'),
        ])
        ->assertRedirect(route('admin.projects.create'))
        ->assertSessionHasErrors('cover_image');

    $this->assertDatabaseMissing('projects', ['slug_en' => 'upload-validation-check']);
});

it('does not allow a guest to upload a project cover', function () {
    $this->post(route('admin.projects.store'), [
        'title_en' => 'Guest upload attempt',
        'slug_en' => 'guest-upload-attempt',
        'project_status' => 'concept',
        'status' => 'draft',
        'sort_order' => 0,
        'cover_image' => UploadedFile::fake()->create('not-an-image.txt', 2, 'text/plain'),
    ])->assertRedirect(route('admin.login'));

    $this->assertDatabaseMissing('projects', ['slug_en' => 'guest-upload-attempt']);
});
