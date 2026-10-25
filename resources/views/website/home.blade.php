@extends('layouts.website')

@section('content')
    <section class="site-hero">
        <div class="site-container site-hero-grid">
            <div class="site-hero-copy">
                <p class="site-eyebrow"><span class="site-eyebrow-line"></span>{{ $copy['eyebrow'] }}</p>
                <h1>{{ $copy['title'] }}</h1>
                <p class="site-hero-lead">{{ $copy['intro'] }}</p>
                <div class="site-hero-actions">
                    <a class="site-button" href="{{ route('localized.contact.show', ['locale' => $locale]) }}">{{ $copy['primaryCta'] }} <span aria-hidden="true">↗</span></a>
                    <a class="site-text-link" href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'services']) }}">{{ $copy['secondaryCta'] }} <span aria-hidden="true">→</span></a>
                </div>
                <div class="site-hero-footnote">
                    <span class="site-status-dot"></span>
                    <span>{{ $isArabic ? 'حلول عملية. نطاق واضح. تنفيذ مسؤول.' : 'Practical solutions. Clear scope. Responsible delivery.' }}</span>
                </div>
            </div>
            <div class="site-hero-art" aria-label="{{ $isArabic ? 'مجالات BAHNES التقنية' : 'BAHNES technology disciplines' }}">
                <div class="site-art-orbit site-art-orbit-one"></div>
                <div class="site-art-orbit site-art-orbit-two"></div>
                <div class="site-art-core"><span>B</span><small>BAHNES / 01</small></div>
                <div class="site-art-label site-art-label-top"><span>01</span>{{ $isArabic ? 'البنية التحتية' : 'INFRASTRUCTURE' }}</div>
                <div class="site-art-label site-art-label-right"><span>02</span>{{ $isArabic ? 'الأمن' : 'SECURITY' }}</div>
                <div class="site-art-label site-art-label-bottom"><span>03</span>{{ $isArabic ? 'البرمجيات' : 'SOFTWARE' }}</div>
                <div class="site-art-coordinate">{{ $isArabic ? 'الأنظمة / الأشخاص / النتائج' : 'SYSTEMS / PEOPLE / OUTCOMES' }}</div>
            </div>
        </div>
    </section>

    <section class="site-section site-section-light">
        <div class="site-container">
            <div class="site-section-heading">
                <div>
                    <p class="site-eyebrow">{{ $copy['capabilities'] }}</p>
                    <h2>{{ $isArabic ? 'مساران واضحان. هدف واحد: تقنية أفضل.' : 'Two clear disciplines. One practical outcome.' }}</h2>
                </div>
                <p>{{ $isArabic ? 'اختيار الخدمة المناسبة يبدأ بفهم المشكلة وسياق العمل، وليس باقتراح أدوات بلا داعٍ.' : 'The right solution starts by understanding the problem and business context, not by prescribing tools without a reason.' }}</p>
            </div>
            <div class="site-service-line-grid">
                @foreach ($serviceLines as $index => $line)
                    <article class="site-discipline-card">
                        <div class="site-card-topline"><span>0{{ $index + 1 }}</span><span class="site-card-arrow" aria-hidden="true">↗</span></div>
                        <h3>{{ $line->nameFor($locale) }}</h3>
                        @if ($line->getAttribute('summary_'.$locale))
                            <p>{{ $line->getAttribute('summary_'.$locale) }}</p>
                        @endif
                        <a class="site-text-link" href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'services']) }}">{{ $copy['serviceLineServices'] }} <span aria-hidden="true">→</span></a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @if ($services->isNotEmpty())
        <section class="site-section">
            <div class="site-container">
                <div class="site-section-heading">
                    <div><p class="site-eyebrow">{{ $copy['services'] }}</p><h2>{{ $isArabic ? 'خدمات منشورة وجاهزة للاستكشاف.' : 'Services, clearly scoped.' }}</h2></div>
                    <a class="site-text-link" href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'services']) }}">{{ $copy['viewAll'] }} <span aria-hidden="true">→</span></a>
                </div>
                <div class="site-content-grid">
                    @foreach ($services as $service)
                        <article class="site-content-card">
                            @if ($service->coverImageUrl())
                                <a class="site-card-image" href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'services', 'slug' => $service->getAttribute('slug_'.$locale)]) }}">
                                    <img src="{{ $service->coverImageUrl() }}" alt="{{ $service->coverImageAlt($locale) }}" loading="lazy" width="960" height="720">
                                </a>
                            @endif
                            <div class="site-content-card-body">
                                <p class="site-card-kicker">{{ $service->serviceLine?->nameFor($locale) }}</p>
                                <h3><a href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'services', 'slug' => $service->getAttribute('slug_'.$locale)]) }}">{{ $service->getAttribute('title_'.$locale) }}</a></h3>
                                <p>{{ $service->getAttribute('summary_'.$locale) }}</p>
                                <a class="site-text-link" href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'services', 'slug' => $service->getAttribute('slug_'.$locale)]) }}">{{ $copy['learnMore'] }} <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($projects->isNotEmpty())
        <section class="site-section site-section-dark">
            <div class="site-container">
                <div class="site-section-heading site-section-heading-dark">
                    <div><p class="site-eyebrow">{{ $copy['projects'] }}</p><h2>{{ $isArabic ? 'أعمال نعرضها بشفافية.' : 'Work we can stand behind.' }}</h2></div>
                    <a class="site-text-link site-text-link-light" href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'projects']) }}">{{ $copy['viewAll'] }} <span aria-hidden="true">→</span></a>
                </div>
                <div class="site-content-grid">
                    @foreach ($projects as $project)
                        <article class="site-content-card site-content-card-dark">
                            @if ($project->coverImageUrl())
                                <a class="site-card-image" href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'projects', 'slug' => $project->getAttribute('slug_'.$locale)]) }}"><img src="{{ $project->coverImageUrl() }}" alt="{{ $project->coverImageAlt($locale) }}" loading="lazy" width="960" height="720"></a>
                            @endif
                            <div class="site-content-card-body">
                                <p class="site-card-kicker">{{ $project->project_status === 'concept' ? ($isArabic ? 'تصور / Concept' : 'Concept') : ($isArabic ? 'مشروع منشور' : 'Published project') }}</p>
                                <h3><a href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'projects', 'slug' => $project->getAttribute('slug_'.$locale)]) }}">{{ $project->getAttribute('title_'.$locale) }}</a></h3>
                                <p>{{ $project->getAttribute('summary_'.$locale) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($products->isNotEmpty() || $templates->isNotEmpty())
        <section class="site-section">
            <div class="site-container">
                <div class="site-section-heading">
                    <div><p class="site-eyebrow">{{ $copy['catalog'] }}</p><h2>{{ $isArabic ? 'أدوات ومنتجات بشروط واضحة.' : 'Useful tools, defined terms.' }}</h2></div>
                </div>
                <div class="site-content-grid">
                    @foreach ($products as $product)
                        <article class="site-content-card">
                            @if ($product->coverImageUrl()) <a class="site-card-image" href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'products', 'slug' => $product->getAttribute('slug_'.$locale)]) }}"><img src="{{ $product->coverImageUrl() }}" alt="{{ $product->coverImageAlt($locale) }}" loading="lazy" width="960" height="720"></a> @endif
                            <div class="site-content-card-body"><p class="site-card-kicker">{{ $isArabic ? 'منتج برمجي' : 'Software product' }}</p><h3><a href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'products', 'slug' => $product->getAttribute('slug_'.$locale)]) }}">{{ $product->getAttribute('title_'.$locale) }}</a></h3><p>{{ $product->getAttribute('summary_'.$locale) }}</p><a class="site-text-link" href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'products']) }}">{{ $copy['viewAll'] }} <span aria-hidden="true">→</span></a></div>
                        </article>
                    @endforeach
                    @foreach ($templates as $template)
                        <article class="site-content-card">
                            @if ($template->coverImageUrl()) <a class="site-card-image" href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'templates', 'slug' => $template->getAttribute('slug_'.$locale)]) }}"><img src="{{ $template->coverImageUrl() }}" alt="{{ $template->coverImageAlt($locale) }}" loading="lazy" width="960" height="720"></a> @endif
                            <div class="site-content-card-body"><p class="site-card-kicker">{{ $isArabic ? 'قالب موقع' : 'Website template' }}</p><h3><a href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'templates', 'slug' => $template->getAttribute('slug_'.$locale)]) }}">{{ $template->getAttribute('title_'.$locale) }}</a></h3><p>{{ $template->getAttribute('summary_'.$locale) }}</p><a class="site-text-link" href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'templates']) }}">{{ $copy['viewAll'] }} <span aria-hidden="true">→</span></a></div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($insights->isNotEmpty())
        <section class="site-section site-section-light">
            <div class="site-container">
                <div class="site-section-heading"><div><p class="site-eyebrow">{{ $copy['insights'] }}</p><h2>{{ $isArabic ? 'معرفة عملية، دون ضجيج.' : 'Practical knowledge, no noise.' }}</h2></div><a class="site-text-link" href="{{ route('public.content.index', ['locale' => $locale, 'section' => 'insights']) }}">{{ $copy['viewAll'] }} <span aria-hidden="true">→</span></a></div>
                <div class="site-content-grid">
                    @foreach ($insights as $insight)
                        <article class="site-content-card">
                            @if ($insight->coverImageUrl()) <a class="site-card-image" href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'insights', 'slug' => $insight->getAttribute('slug_'.$locale)]) }}"><img src="{{ $insight->coverImageUrl() }}" alt="{{ $insight->coverImageAlt($locale) }}" loading="lazy" width="960" height="720"></a> @endif
                            <div class="site-content-card-body"><p class="site-card-kicker">{{ $insight->category?->nameFor($locale) }}</p><h3><a href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'insights', 'slug' => $insight->getAttribute('slug_'.$locale)]) }}">{{ $insight->getAttribute('title_'.$locale) }}</a></h3><p>{{ $insight->getAttribute('excerpt_'.$locale) }}</p><a class="site-text-link" href="{{ route('public.content.show', ['locale' => $locale, 'section' => 'insights', 'slug' => $insight->getAttribute('slug_'.$locale)]) }}">{{ $copy['learnMore'] }} <span aria-hidden="true">→</span></a></div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="site-cta-section">
        <div class="site-container site-cta-inner">
            <div><p class="site-eyebrow">{{ $isArabic ? 'الخطوة التالية' : 'THE NEXT STEP' }}</p><h2>{{ $isArabic ? 'عندك تحدٍ تقني؟ لنبدأ من احتياجك.' : 'Have a technical challenge? Start with the need.' }}</h2><p>{{ $isArabic ? 'شاركنا السياق والنتيجة المطلوبة، وسنناقش معك نطاقًا مناسبًا.' : 'Share the context and outcome you need. We will discuss a scope that fits.' }}</p></div>
            <a class="site-button site-button-light" href="{{ route('localized.contact.show', ['locale' => $locale]) }}">{{ $copy['contact'] }} <span aria-hidden="true">↗</span></a>
        </div>
    </section>
@endsection
