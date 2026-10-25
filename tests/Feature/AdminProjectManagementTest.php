<?php

use App\Models\Project;
use App\Models\User;

function makeProjectAdmin(): User
{
    return User::factory()->create(['is_admin' => true]);
}

it('requires admin access to view project management', function () {
    $this->get(route('admin.projects.index'))
        ->assertRedirect(route('admin.login'));

    $this->actingAs(User::factory()->create(['is_admin' => false]))
        ->get(route('admin.projects.index'))
        ->assertForbidden();
});

it('saves a concept as a draft and does not expose it publicly', function () {
    $this->actingAs(makeProjectAdmin())
        ->post(route('admin.projects.store'), [
            'title_en' => 'Network Modernization Concept',
            'slug_en' => 'network-modernization-concept',
            'title_ar' => 'تصور تحديث الشبكات',
            'slug_ar' => 'tasawor-tahdeeth-shabakat',
            'summary_en' => 'A proposed concept.',
            'summary_ar' => 'تصور أولي لمشروع.',
            'project_status' => 'concept',
            'status' => 'draft',
            'sort_order' => 0,
        ])
        ->assertRedirect();

    $project = Project::query()->firstOrFail();

    expect($project->status)->toBe('draft')
        ->and(Project::query()->publishedForLocale('en')->whereKey($project->id)->exists())->toBeFalse()
        ->and(Project::query()->publishedForLocale('ar')->whereKey($project->id)->exists())->toBeFalse();
});

it('requires display permission before publishing real work', function () {
    $this->actingAs(makeProjectAdmin())
        ->from(route('admin.projects.create'))
        ->post(route('admin.projects.store'), [
            'title_en' => 'Client Network Project',
            'slug_en' => 'client-network-project',
            'project_status' => 'completed',
            'status' => 'published',
            'sort_order' => 0,
        ])
        ->assertRedirect(route('admin.projects.create'))
        ->assertSessionHasErrors(['display_permission_confirmed']);

    expect(Project::query()->count())->toBe(0);
});

it('does not allow confidential projects to be published', function () {
    $this->actingAs(makeProjectAdmin())
        ->from(route('admin.projects.create'))
        ->post(route('admin.projects.store'), [
            'title_en' => 'Confidential Project',
            'slug_en' => 'confidential-project',
            'project_status' => 'confidential',
            'display_permission_confirmed' => '1',
            'status' => 'published',
            'sort_order' => 0,
        ])
        ->assertRedirect(route('admin.projects.create'))
        ->assertSessionHasErrors(['project_status']);

    expect(Project::query()->count())->toBe(0);
});

it('publishes a concept only in the locale with complete translated fields', function () {
    $this->actingAs(makeProjectAdmin())
        ->post(route('admin.projects.store'), [
            'title_en' => 'Retail Systems Concept',
            'slug_en' => 'retail-systems-concept',
            'title_ar' => '',
            'slug_ar' => '',
            'project_status' => 'concept',
            'status' => 'published',
            'sort_order' => 0,
        ])
        ->assertRedirect();

    $project = Project::query()->firstOrFail();

    expect(Project::query()->publishedForLocale('en')->whereKey($project->id)->exists())->toBeTrue()
        ->and(Project::query()->publishedForLocale('ar')->whereKey($project->id)->exists())->toBeFalse();
});

it('keeps confidential or unapproved real work out of public results', function () {
    $confidential = Project::query()->create([
        'title_en' => 'Private System',
        'slug_en' => 'private-system',
        'project_status' => 'confidential',
        'display_permission_confirmed' => true,
        'status' => 'published',
    ]);

    $unapproved = Project::query()->create([
        'title_en' => 'Unapproved Work',
        'slug_en' => 'unapproved-work',
        'project_status' => 'completed',
        'display_permission_confirmed' => false,
        'status' => 'published',
    ]);

    expect(Project::query()->publishedForLocale('en')->whereKey($confidential->id)->exists())->toBeFalse()
        ->and(Project::query()->publishedForLocale('en')->whereKey($unapproved->id)->exists())->toBeFalse();
});

it('updates a project and can archive it', function () {
    $project = Project::query()->create([
        'title_en' => 'Old Concept',
        'slug_en' => 'old-concept',
        'project_status' => 'concept',
        'status' => 'draft',
    ]);

    $this->actingAs(makeProjectAdmin())
        ->put(route('admin.projects.update', $project), [
            'title_en' => 'Updated Concept',
            'slug_en' => 'updated-concept',
            'project_status' => 'concept',
            'status' => 'archived',
            'sort_order' => 4,
        ])
        ->assertRedirect(route('admin.projects.edit', $project));

    expect($project->fresh()->title_en)->toBe('Updated Concept')
        ->and($project->fresh()->status)->toBe('archived');
});


it('rejects non-web protocols for public project links', function () {
    $this->actingAs(makeProjectAdmin())->from(route('admin.projects.create'))->post(route('admin.projects.store'), [
        'title_en' => 'Unsafe Link Project',
        'slug_en' => 'unsafe-link-project',
        'project_status' => 'concept',
        'status' => 'draft',
        'sort_order' => 0,
        'project_url' => 'ftp://example.com/private',
    ])->assertRedirect(route('admin.projects.create'))->assertSessionHasErrors(['project_url']);

    expect(Project::query()->count())->toBe(0);
});
