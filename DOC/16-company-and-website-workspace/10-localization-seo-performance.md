# 10 — Localization, SEO & Performance

## Rendering baseline

Use Laravel Blade server-rendered HTML. Do not introduce SPA routing for the current content website. Use Laravel routes and the Arabic/English locale strategy; see the [canonical stack decision](../09-technology-and-engineering/website-tech-stack.md).

## Languages and URLs

Arabic and English are implemented at launch with consistent locale URLs, for example /en/services and /ar/services. The root route redirects to the English homepage. Language switching preserves the equivalent content page when a published translation exists; otherwise it falls back to the localized section index. Navigation, forms, breadcrumbs, and pagination have locale-aware labels; pagination also switches direction for RTL. Mixed technical terms should retain readable direction and spacing.

Each locale requires reviewed copy, slug policy, title/description, canonical, social preview, and translation state. Never declare a nonexistent translation as available.

## SEO foundations

- Semantic HTML and logical headings.
- Unique titles and useful descriptions for indexable pages.
- Stable URLs, canonical handling, and redirects for changed slugs.
- Reciprocal hreflang links for existing language variants.
- **Implemented and CI-tested:** dynamic `/sitemap.xml` via Spatie Sitemap lists both localized homepages, contact pages, all five section indexes per locale, and published service/project/product/template/Insight detail URLs only when the locale-specific translation is eligible for publication. Drafts, archived records, confidential projects, and missing translations must remain excluded.
- **Implemented and CI-tested:** dynamic `/robots.txt` allows public crawling, disallows `/admin-panel/`, and points to the absolute sitemap URL.
- Open Graph previews with the approved logo/brand asset and relevant page image.
- Accurate structured data only where appropriate.
- Useful internal links, descriptive link text, useful alt text, and clean 404 handling.
- Correct handling of drafts and unpublished content.

## Performance

Use responsive optimized images, proper dimensions and lazy-loading for non-critical media; prioritize primary content; keep JavaScript and third-party scripts minimal; cache safe public content without leaking admin/private data; avoid heavy 3D/autoplay; and measure mobile and desktop behavior. Social preview images must point to the intended asset, not an unrelated placeholder.

## Measurement

Track qualified inquiries, service-line interest, demo/template requests, useful landing pages, and performance with privacy-conscious analytics. Collect no more personal data than needed.

## Review gate

Test locales, RTL/LTR, canonical, hreflang, sitemap contents, robots exclusions, social previews, redirects, forms, and mobile performance before launch.
