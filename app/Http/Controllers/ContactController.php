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
        $locale = $request->route('locale');
        if (! in_array($locale, ['en', 'ar'], true)) {
            $preferredLocale = $request->input('preferred_locale');
            $locale = in_array($preferredLocale, ['en', 'ar'], true) ? $preferredLocale : 'en';
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:180'],
            'type' => ['required', Rule::in(Inquiry::TYPES)],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'preferred_locale' => ['nullable', Rule::in(['en', 'ar'])],
        ], $this->validationMessages($locale), $this->validationAttributes($locale));

        $inquiry = Inquiry::query()->create([
            'name' => $data['name'],
            'email' => mb_strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'type' => $data['type'],
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'preferred_locale' => $request->route('locale')
                ?? $data['preferred_locale']
                ?? (in_array(app()->getLocale(), ['en', 'ar'], true) ? app()->getLocale() : 'en'),
            'source' => 'website_contact',
            'status' => 'new',
        ]);

        // Email notification is deferred until a real recipient and mail settings are agreed.
        $message = $inquiry->preferred_locale === 'ar'
            ? 'شكرًا لك، تم استلام رسالتك.'
            : 'Thank you. Your message has been received.';

        return redirect()->back()
            ->with('status', $message)
            ->with('inquiry_reference', 'BR-' . str_pad((string) $inquiry->getKey(), 6, '0', STR_PAD_LEFT));
    }

    /**
     * @return array<string, string>
     */
    private function validationMessages(string $locale): array
    {
        if ($locale === 'ar') {
            return [
                'required' => 'حقل :attribute مطلوب.',
                'string' => 'يجب أن يكون :attribute نصًا.',
                'email' => 'يرجى إدخال عنوان بريد إلكتروني صحيح في حقل :attribute.',
                'max.string' => 'يجب ألا يزيد :attribute عن :max حرفًا.',
                'min.string' => 'يجب أن يحتوي حقل :attribute على :min حرفًا على الأقل.',
                'in' => 'القيمة المحددة لحقل :attribute غير صالحة.',
            ];
        }

        return [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute field must be text.',
            'email' => 'Enter a valid email address for :attribute.',
            'max.string' => 'The :attribute field may not be greater than :max characters.',
            'min.string' => 'The :attribute field must be at least :min characters.',
            'in' => 'The selected :attribute is invalid.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(string $locale): array
    {
        return $locale === 'ar' ? [
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الهاتف',
            'company' => 'اسم الشركة',
            'type' => 'نوع الاستفسار',
            'subject' => 'الموضوع',
            'message' => 'الرسالة',
            'preferred_locale' => 'اللغة المفضلة',
        ] : [
            'name' => 'name',
            'email' => 'email address',
            'phone' => 'phone number',
            'company' => 'company name',
            'type' => 'inquiry type',
            'subject' => 'subject',
            'message' => 'message',
            'preferred_locale' => 'preferred language',
        ];
    }
}
