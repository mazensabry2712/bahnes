<!doctype html>
<html lang="{{ $locale }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="theme-color" content="#101c35">
    <title>{{ $pageTitle }} · BAHNES IT SOLUTIONS</title>
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @foreach ($alternateUrls as $alternateLocale => $alternateUrl)
        <link rel="alternate" hreflang="{{ $alternateLocale === 'ar' ? 'ar-EG' : 'en' }}" href="{{ $alternateUrl }}">
    @endforeach
    <meta property="og:type" content="@yield('ogType', 'website')">
    <meta property="og:site_name" content="BAHNES IT SOLUTIONS">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if (!empty($ogImageUrl))
        <meta property="og:image" content="{{ $ogImageUrl }}">
    @endif
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @endif
    @stack('head')
</head>
<body class="site-body">
    <a class="skip-link" href="#main-content">{{ $isArabic ? 'انتقل إلى المحتوى' : 'Skip to content' }}</a>
    <header class="site-header">
        <div class="site-nav-wrap">
            <a class="site-wordmark" href="{{ route('localized.home', ['locale' => $locale]) }}" aria-label="BAHNES IT SOLUTIONS">
                <span class="site-mark" aria-hidden="true">B</span>
                <span>BAHNES<span class="site-wordmark-dot">.</span><small>IT SOLUTIONS</small></span>
            </a>
            <nav class="site-nav" aria-label="{{ $isArabic ? 'التنقل الرئيسي' : 'Main navigation' }}">
                <a href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'services']) }}">{{ $navLabels['services'] }}</a>
                <a href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'projects']) }}">{{ $navLabels['projects'] }}</a>
                <a href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'products']) }}">{{ $navLabels['products'] }}</a>
                <a href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'templates']) }}">{{ $navLabels['templates'] }}</a>
                <a href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'insights']) }}">{{ $navLabels['insights'] }}</a>
            </nav>
            <div class="site-nav-actions">
                <a class="site-language" href="{{ $languageSwitchUrl }}" lang="{{ $isArabic ? 'en' : 'ar' }}">{{ $navLabels['language'] }}</a>
                <a class="site-button site-button-small" href="{{ route('localized.contact.show', ['locale' => $locale]) }}">{{ $navLabels['contact'] }} <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="site-footer-inner">
            <a class="site-wordmark site-wordmark-footer" href="{{ route('localized.home', ['locale' => $locale]) }}">
                <span class="site-mark" aria-hidden="true">B</span>
                <span>BAHNES<span class="site-wordmark-dot">.</span><small>IT SOLUTIONS</small></span>
            </a>
            <p>{{ $isArabic ? 'البنية التحتية لتقنية المعلومات والأمن والحلول البرمجية.' : 'IT infrastructure, security, and software solutions.' }}</p>
            <div class="site-footer-links">
                <a href="{{ route('localized.contact.show', ['locale' => $locale]) }}">{{ $navLabels['contact'] }}</a>
                <a href="{{ route('robots') }}">robots.txt</a>
            </div>
            <small>© {{ now()->year }} BAHNES IT SOLUTIONS</small>
        </div>
    </footer>
</body>
</html>
