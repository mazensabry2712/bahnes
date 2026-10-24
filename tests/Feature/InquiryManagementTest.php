<?php

use App\Models\Inquiry;
use App\Models\User;

it('requires admin access to view customer inquiries', function () {
    $this->get(route('admin.inquiries.index'))
        ->assertRedirect(route('admin.login'));

    $this->actingAs(User::factory()->create(['is_admin' => false]))
        ->get(route('admin.inquiries.index'))
        ->assertForbidden();
});

it('stores a valid public contact inquiry as new', function () {
    $this->from('/')
        ->post(route('contact.store'), [
            'name' => 'Mazen Test',
            'email' => 'MAZEN@example.com',
            'phone' => '+201000000000',
            'company' => 'Test Company',
            'type' => 'software',
            'subject' => 'Business website',
            'message' => 'We need a bilingual company website and an admin content system.',
            'preferred_locale' => 'en',
        ])
        ->assertRedirect()
        ->assertSessionHas('status');

    $inquiry = Inquiry::query()->firstOrFail();

    expect($inquiry->name)->toBe('Mazen Test')
        ->and($inquiry->email)->toBe('mazen@example.com')
        ->and($inquiry->type)->toBe('software')
        ->and($inquiry->preferred_locale)->toBe('en')
        ->and($inquiry->source)->toBe('website_contact')
        ->and($inquiry->status)->toBe('new')
        ->and($inquiry->admin_notes)->toBeNull();
});

it('rejects invalid inquiry types and too-short messages', function () {
    $this->from('/')
        ->post(route('contact.store'), [
            'name' => 'Mazen Test',
            'email' => 'mazen@example.com',
            'type' => 'unknown',
            'message' => 'too short',
            'preferred_locale' => 'ar',
        ])
        ->assertRedirect('/')
        ->assertSessionHasErrors(['type', 'message']);

    expect(Inquiry::query()->count())->toBe(0);
});

it('keeps internal notes separate from visitor submitted fields', function () {
    $this->from('/')
        ->post(route('contact.store'), [
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'type' => 'general',
            'subject' => 'Question',
            'message' => 'Please send me more information about your services.',
            'admin_notes' => 'Trying to inject internal notes from a public form.',
        ])
        ->assertRedirect();

    expect(Inquiry::query()->firstOrFail()->admin_notes)->toBeNull();
});

it('allows an administrator to change follow-up status and internal notes', function () {
    $inquiry = Inquiry::query()->create([
        'name' => 'Visitor',
        'email' => 'visitor@example.com',
        'type' => 'general',
        'message' => 'Please send me more information about your services.',
        'preferred_locale' => 'en',
        'source' => 'website_contact',
        'status' => 'new',
    ]);
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->put(route('admin.inquiries.update', $inquiry), [
            'status' => 'in_progress',
            'admin_notes' => 'Replied by email; follow up next week.',
        ])
        ->assertRedirect(route('admin.inquiries.edit', $inquiry));

    expect($inquiry->fresh()->status)->toBe('in_progress')
        ->and($inquiry->fresh()->admin_notes)->toBe('Replied by email; follow up next week.');
});

it('does not allow unsupported follow-up statuses', function () {
    $inquiry = Inquiry::query()->create([
        'name' => 'Visitor',
        'email' => 'visitor@example.com',
        'type' => 'general',
        'message' => 'Please send me more information about your services.',
        'preferred_locale' => 'en',
        'source' => 'website_contact',
        'status' => 'new',
    ]);

    $this->actingAs(User::factory()->create(['is_admin' => true]))
        ->from(route('admin.inquiries.edit', $inquiry))
        ->put(route('admin.inquiries.update', $inquiry), [
            'status' => 'invalid',
            'admin_notes' => '',
        ])
        ->assertRedirect(route('admin.inquiries.edit', $inquiry))
        ->assertSessionHasErrors(['status']);

    expect($inquiry->fresh()->status)->toBe('new');
});
