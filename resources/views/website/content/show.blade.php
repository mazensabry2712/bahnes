@extends('layouts.website')

@section('ogType', $section === 'insights' ? 'article' : 'website')

@section('content')
    <article class="site-detail">
        <div class="site-detail-top">
            <div class="site-container">
                <a class="site-breadcrumb" href="{{ route('public.content.index', ['locale' => $locale, 'section' => $section]) }}">{{ $config['title'] }} <span aria-hidden="true">/</span></a>
                @if ($section === 'services' && $item->serviceLine)
                    <p class="site-eyebrow">{{ $item->serviceLine->nameFor($locale) }}</p>
                @elseif ($section === 'insights' && $item->category)
                    <p class="site-eyebrow">{{ $item->category->nameFor($locale) }}</p>
                @elseif ($section === 'projects')
                    <p class="site-eyebrow">{{ $item->project_status === 'concept' ? ($isArabic ? 'تصور توضيحي' : 'Concept') : ($isArabic ? 'مشروع منشور' : 'Published project') }}</p>
                @else
                    <p class="site-eyebrow">{{ $config['singular'] }}</p>
                @endif
                <h1>{{ $itemTitle }}</h1>
                @if ($summary !== '') <p class="site-detail-summary">{{ $summary }}</p> @endif
                @if ($section === 'projects' && $item->client_name && $item->display_permission_confirmed)
                    <p class="site-detail-client">{{ $isArabic ? 'العميل' : 'Client' }}: {{ $item->client_name }}</p>
                @endif
                @if ($section === 'projects' && $item->project_status === 'concept')
                    <p class="site-notice">{{ $isArabic ? 'هذا تصور توضيحي، وليس مشروع عميل منفذًا.' : 'This is a concept, not a delivered client project.' }}</p>
                @endif
            </div>
        </div>

        <div class="site-container site-detail-layout">
            <div class="site-detail-main">
                @if ($coverUrl)
                    <figure class="site-detail-image"><img src="{{ $coverUrl }}" alt="{{ $coverAlt }}" width="960" height="720"></figure>
                @endif

                @foreach ($sections as $contentSection)
                    <section class="site-detail-section">
                        <h2>{{ $contentSection['title'] }}</h2>
                        <div class="site-prose">{{ $contentSection['body'] }}</div>
                    </section>
                @endforeach

                @if ($section === 'templates' && $item->preview_url)
                    <p><a class="site-button site-button-outline" href="{{ $item->preview_url }}" target="_blank" rel="noopener noreferrer">{{ $isArabic ? 'فتح المعاينة' : 'Open preview' }} <span aria-hidden="true">↗</span></a></p>
                @endif
                @if (in_array($section, ['templates', 'products'], true) && $item->demo_url)
                    <p><a class="site-button site-button-outline" href="{{ $item->demo_url }}" target="_blank" rel="noopener noreferrer">{{ $isArabic ? 'فتح العرض التجريبي' : 'Open demo' }} <span aria-hidden="true">↗</span></a></p>
                @endif
                @if ($section === 'projects' && $item->project_url)
                    <p><a class="site-button site-button-outline" href="{{ $item->project_url }}" target="_blank" rel="noopener noreferrer">{{ $isArabic ? 'زيارة المشروع' : 'Visit project' }} <span aria-hidden="true">↗</span></a></p>
                @endif
            </div>

            <aside class="site-detail-aside">
                <p class="site-eyebrow">{{ $isArabic ? 'تحتاج شيئًا مشابهًا؟' : 'NEED SOMETHING SIMILAR?' }}</p>
                <h2>{{ $isArabic ? 'لنناقش احتياجك.' : 'Let’s discuss your needs.' }}</h2>
                <p>{{ $isArabic ? 'أخبرنا بالسياق والهدف، وسنناقش نطاقًا واضحًا للخطوة التالية.' : 'Tell us the context and desired outcome. We will discuss a clear scope for the next step.' }}</p>
                <a class="site-button" href="{{ route('localized.contact.show', ['locale' => $locale]) }}">{{ $navLabels['contact'] }} <span aria-hidden="true">↗</span></a>
                <a class="site-text-link site-aside-back" href="{{ route('public.content.index', ['locale' => $locale, 'section' => $section]) }}">{{ $isArabic ? 'العودة للقائمة' : 'Back to listing' }} <span aria-hidden="true">→</span></a>
            </aside>
        </div>
    </article>
@endsection
