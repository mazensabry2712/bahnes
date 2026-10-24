@extends('layouts.website')

@section('content')
    <section class="site-page-hero">
        <div class="site-container site-page-hero-inner">
            <a class="site-breadcrumb" href="{{ route('localized.home', ['locale' => $locale]) }}">{{ $navLabels['home'] }} <span aria-hidden="true">/</span></a>
            <p class="site-eyebrow">{{ $config['eyebrow'] }}</p>
            <h1>{{ $config['title'] }}</h1>
            <p class="site-page-intro">{{ $config['intro'] }}</p>
        </div>
    </section>

    <section class="site-section">
        <div class="site-container">
            @if ($items->isNotEmpty())
                <div class="site-content-grid">
                    @foreach ($items as $item)
                        @php($itemTitle = (string) $item->getAttribute('title_'.$locale))
                        @php($itemSlug = (string) $item->getAttribute('slug_'.$locale))
                        @php($summaryField = $section === 'insights' ? 'excerpt' : 'summary')
                        @php($itemSummary = (string) ($item->getAttribute($summaryField.'_'.$locale) ?? ''))
                        <article class="site-content-card">
                            @if ($item->coverImageUrl())
                                <a class="site-card-image" href="{{ route('public.content.show', ['locale' => $locale, 'section' => $section, 'slug' => $itemSlug]) }}">
                                    <img src="{{ $item->coverImageUrl() }}" alt="{{ $item->coverImageAlt($locale) }}" loading="lazy" width="960" height="720">
                                </a>
                            @endif
                            <div class="site-content-card-body">
                                @if ($section === 'services' && $item->serviceLine)
                                    <p class="site-card-kicker">{{ $item->serviceLine->nameFor($locale) }}</p>
                                @elseif ($section === 'insights' && $item->category)
                                    <p class="site-card-kicker">{{ $item->category->nameFor($locale) }}</p>
                                @elseif ($section === 'projects')
                                    <p class="site-card-kicker">{{ $item->project_status === 'concept' ? ($isArabic ? 'تصور / Concept' : 'Concept') : ($isArabic ? 'مشروع منشور' : 'Published project') }}</p>
                                @else
                                    <p class="site-card-kicker">{{ $config['singular'] }}</p>
                                @endif
                                <h2><a href="{{ route('public.content.show', ['locale' => $locale, 'section' => $section, 'slug' => $itemSlug]) }}">{{ $itemTitle }}</a></h2>
                                @if ($itemSummary !== '') <p>{{ $itemSummary }}</p> @endif
                                <a class="site-text-link" href="{{ route('public.content.show', ['locale' => $locale, 'section' => $section, 'slug' => $itemSlug]) }}">{{ $isArabic ? 'عرض التفاصيل' : 'View details' }} <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="site-pagination">{{ $items->links() }}</div>
            @else
                <div class="site-empty-state">
                    <span class="site-empty-mark" aria-hidden="true">B.</span>
                    <p class="site-eyebrow">{{ $isArabic ? 'تحديث المحتوى' : 'CONTENT UPDATE' }}</p>
                    <h2>{{ $config['emptyTitle'] }}</h2>
                    <p>{{ $config['emptyText'] }}</p>
                    <a class="site-button" href="{{ route('localized.contact.show', ['locale' => $locale]) }}">{{ $navLabels['contact'] }} <span aria-hidden="true">↗</span></a>
                </div>
            @endif
        </div>
    </section>
@endsection
