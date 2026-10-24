<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:180'],
            'type' => ['required', Rule::in(Inquiry::TYPES)],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'preferred_locale' => ['nullable', Rule::in(['en', 'ar'])],
            'website_url' => ['nullable', 'string', 'max:0'],
        ]);

        $inquiry = Inquiry::query()->create([
            'name' => $data['name'],
            'email' => mb_strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'type' => $data['type'],
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'preferred_locale' => $data['preferred_locale'] ?? (in_array(app()->getLocale(), ['en', 'ar'], true) ? app()->getLocale() : 'en'),
            'source' => 'website_contact',
            'status' => 'new',
        ]);

        // Email notification is deferred until a real recipient and mail settings are agreed.
        return redirect()->back()
            ->with('status', 'Thank you. Your message has been received.')
            ->with('inquiry_reference', 'BR-' . str_pad((string) $inquiry->getKey(), 6, '0', STR_PAD_LEFT));
    }
}
