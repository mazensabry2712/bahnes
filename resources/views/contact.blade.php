<!doctype html>
@php
    $isArabic = $locale === 'ar';
    $labels = $isArabic ? [
        'title' => 'تواصل مع BAHNES',
        'intro' => 'احكِ لنا عن احتياجك، وسنساعدك في تحديد الخطوة المناسبة.',
        'home' => 'الرئيسية',
        'name' => 'الاسم بالكامل',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الهاتف (اختياري)',
        'company' => 'اسم الشركة (اختياري)',
        'type' => 'نوع الاستفسار',
        'subject' => 'الموضوع (اختياري)',
        'message' => 'تفاصيل طلبك',
        'send' => 'إرسال الرسالة',
        'sent' => 'تم استلام رسالتك بنجاح.',
        'reference' => 'رقم المتابعة',
        'choose' => 'اختر نوع الاستفسار',
        'types' => [
            'general' => 'استفسار عام',
            'it_infrastructure' => 'البنية التحتية وأمن المعلومات',
            'software' => 'الحلول البرمجية والرقمية',
            'project_brief' => 'مناقشة مشروع جديد',
            'product_demo' => 'طلب عرض منتج',
            'template_customization' => 'تخصيص قالب موقع',
            'support' => 'الدعم الفني',
        ],
    ] : [
        'title' => 'Talk to BAHNES',
        'intro' => 'Tell us what you need. We will help you define the right next step.',
        'home' => 'Home',
        'name' => 'Full name',
        'email' => 'Email address',
        'phone' => 'Phone (optional)',
        'company' => 'Company (optional)',
        'type' => 'Inquiry type',
        'subject' => 'Subject (optional)',
        'message' => 'How can we help?',
        'send' => 'Send inquiry',
        'sent' => 'Your message has been received.',
        'reference' => 'Reference',
        'choose' => 'Choose an inquiry type',
        'types' => [
            'general' => 'General inquiry',
            'it_infrastructure' => 'IT infrastructure & security',
            'software' => 'Software & digital solutions',
            'project_brief' => 'Discuss a project',
            'product_demo' => 'Product demo',
            'template_customization' => 'Website template customization',
            'support' => 'Support',
        ],
    ];
@endphp
<html lang="{{ $locale }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    <meta name="description" content="{{ $isArabic ? 'تواصل مع BAHNES IT SOLUTIONS لمناقشة احتياجاتك التقنية والبرمجية.' : 'Contact BAHNES IT SOLUTIONS to discuss your IT, security and software needs.' }}">
    <title>{{ $labels['title'] }} · BAHNES IT SOLUTIONS</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @endif
    <style>
        :root{font-family:'Manrope','IBM Plex Sans Arabic',ui-sans-serif,system-ui,sans-serif;color:#101c35;background:#f5f4ef}
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh}
        a{color:inherit}
        .contact-shell{width:min(100% - 2rem,70rem);margin-inline:auto;padding-block:1.5rem 4rem}
        .contact-header{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding-block:1rem 2rem}
        .contact-brand{font-weight:800;letter-spacing:.13em;text-decoration:none}
        .contact-nav{color:#667085;text-underline-offset:.22em}
        .contact-layout{display:grid;grid-template-columns:minmax(0,.85fr) minmax(0,1.15fr);gap:clamp(1.5rem,5vw,4rem);align-items:start}
        .contact-intro{padding-block:clamp(1rem,5vw,4rem)}
        .contact-kicker{color:#0d8f78;font-size:.8rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
        h1{font-size:clamp(2.5rem,5vw,4.5rem);line-height:1.04;letter-spacing:-.045em;margin:.8rem 0 1.25rem}
        .contact-lead{font-size:1.1rem;line-height:1.8;color:#667085;max-width:34rem}
        .contact-card{background:#fff;border:1px solid #e5e6e1;border-radius:1.25rem;padding:clamp(1.25rem,3vw,2rem);box-shadow:0 22px 65px #101c3510}
        .contact-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}
        .contact-field{display:grid;gap:.45rem;margin-bottom:1rem}
        .contact-field.full{grid-column:1/-1}
        .contact-field label{font-weight:650;font-size:.9rem}
        .contact-field input,.contact-field select,.contact-field textarea{width:100%;border:1px solid #d4d8d5;border-radius:.7rem;background:#fff;padding:.9rem;font:inherit;color:#101c35}
        .contact-field textarea{min-height:10rem;resize:vertical}
        .contact-field input:focus-visible,.contact-field select:focus-visible,.contact-field textarea:focus-visible,.contact-button:focus-visible{outline:3px solid #55d6be;outline-offset:2px}
        .contact-button{display:flex;align-items:center;justify-content:center;gap:.65rem;border:0;border-radius:.7rem;background:#101c35;color:#fff;font-weight:750;padding:1rem 1.2rem;min-height:3.2rem;cursor:pointer;width:100%}
        .contact-button .ui-icon{width:1.1rem;height:1.1rem;flex:none}
        .contact-muted{color:#667085;font-size:.88rem;line-height:1.7}
        .contact-flash{border-radius:.7rem;padding:.9rem 1rem;margin-bottom:1rem;background:#e8f8f2;border:1px solid #a5dfcc;color:#075e4f}
        .contact-error{color:#b42318;font-size:.875rem}
        @media(max-width:760px){.contact-layout{grid-template-columns:1fr}.contact-intro{padding-block:1rem}.contact-header{padding-bottom:1.25rem}h1{font-size:clamp(2.5rem,12vw,4rem)}}
        @media(max-width:480px){.contact-fields{grid-template-columns:1fr}.contact-field.full{grid-column:auto}}
    </style>
</head>
<body>
    <div class="contact-shell">
        <header class="contact-header">
            <a class="contact-brand" href="{{ route('home') }}" aria-label="BAHNES IT SOLUTIONS">BAHNES<span style="color:#0d8f78">.</span></a>
            <a class="contact-nav" href="{{ route('home') }}">{{ $labels['home'] }}</a>
        </header>

        <main class="contact-layout">
            <section class="contact-intro">
                <p class="contact-kicker">IT INFRASTRUCTURE · SOFTWARE · SECURITY</p>
                <h1>{{ $labels['title'] }}</h1>
                <p class="contact-lead">{{ $labels['intro'] }}</p>
                <p class="contact-muted">{{ $isArabic ? 'وضح لنا نوع الخدمة والسياق والنتيجة التي تريد الوصول إليها. لا ترسل كلمات مرور أو بيانات سرية في النموذج.' : 'Share the service you need and the outcome you are aiming for. Do not include passwords or confidential credentials in this form.' }}</p>
            </section>

            <section class="contact-card" aria-label="{{ $labels['title'] }}">
                @if (session('status'))
                    <p class="contact-flash" role="status">{{ session('status') }}</p>
                    @if (session('inquiry_reference'))
                        <p class="contact-muted">{{ $labels['reference'] }}: <strong>{{ session('inquiry_reference') }}</strong></p>
                    @endif
                @endif
                @if ($errors->any())
                    <div class="contact-flash" role="alert" style="background:#fff1f0;border-color:#f2b8b5;color:#912018">
                        <p>{{ $isArabic ? 'راجع البيانات التالية:' : 'Please check the following fields:' }}</p>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('localized.contact.store', ['locale' => $locale]) }}">
                    @csrf
                    <x-honeypot />
                    <input type="hidden" name="preferred_locale" value="{{ $locale }}">
                    <div class="contact-fields">
                        <div class="contact-field">
                            <label for="name">{{ $labels['name'] }}</label>
                            <input id="name" name="name" autocomplete="name" maxlength="120" required value="{{ old('name') }}">
                            @error('name') <span class="contact-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="contact-field">
                            <label for="email">{{ $labels['email'] }}</label>
                            <input id="email" name="email" type="email" autocomplete="email" maxlength="255" required value="{{ old('email') }}">
                            @error('email') <span class="contact-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="contact-field">
                            <label for="phone">{{ $labels['phone'] }}</label>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="40" value="{{ old('phone') }}">
                            @error('phone') <span class="contact-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="contact-field">
                            <label for="company">{{ $labels['company'] }}</label>
                            <input id="company" name="company" autocomplete="organization" maxlength="180" value="{{ old('company') }}">
                            @error('company') <span class="contact-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="contact-field full">
                            <label for="type">{{ $labels['type'] }}</label>
                            <select id="type" name="type" required>
                                <option value="">{{ $labels['choose'] }}</option>
                                @foreach ($labels['types'] as $type => $typeLabel)
                                    <option value="{{ $type }}" @selected(old('type') === $type)>{{ $typeLabel }}</option>
                                @endforeach
                            </select>
                            @error('type') <span class="contact-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="contact-field full">
                            <label for="subject">{{ $labels['subject'] }}</label>
                            <input id="subject" name="subject" maxlength="200" value="{{ old('subject') }}">
                            @error('subject') <span class="contact-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="contact-field full">
                            <label for="message">{{ $labels['message'] }}</label>
                            <textarea id="message" name="message" minlength="20" maxlength="5000" required>{{ old('message') }}</textarea>
                            @error('message') <span class="contact-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <button class="contact-button" type="submit"><span>{{ $labels['send'] }}</span><i data-lucide="send" aria-hidden="true"></i></button>
                    <p class="contact-muted" style="margin-top:.8rem">{{ $isArabic ? 'تُستخدم البيانات للرد على استفسارك فقط وفق إعدادات الاحتفاظ المعتمدة.' : 'Your details are used to follow up on this inquiry. Review company privacy information before launch.' }}</p>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
