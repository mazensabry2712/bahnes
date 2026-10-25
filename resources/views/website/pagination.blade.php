@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ $isArabic ? 'التنقل بين الصفحات' : 'Pagination Navigation' }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        @if ($paginator->onFirstPage())
            <span class="site-page-link is-disabled" aria-disabled="true">{{ $isArabic ? 'السابق' : 'Previous' }}</span>
        @else
            <a class="site-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">{{ $isArabic ? 'السابق' : 'Previous' }}</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="site-page-link is-ellipsis" aria-disabled="true">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="site-page-link is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="site-page-link" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="site-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">{{ $isArabic ? 'التالي' : 'Next' }}</a>
        @else
            <span class="site-page-link is-disabled" aria-disabled="true">{{ $isArabic ? 'التالي' : 'Next' }}</span>
        @endif
    </nav>
@endif
