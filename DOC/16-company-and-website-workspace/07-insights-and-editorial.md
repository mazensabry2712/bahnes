# 07 — Insights & Editorial

## Implementation status

- **Implemented and CI-verified:** bilingual category management and Insights article list/search/filter/create/edit in the Blade admin.
- Articles store localized title, URL slug, excerpt, body and SEO metadata, with category relation, order, Draft/Published/Archived status and optional publication scheduling.
- **Publication guard:** an article requires an active category and a complete title/slug/excerpt/body in at least one locale before publication. The public query returns only published, scheduled-ready articles with a full translation for the selected locale and an active category.
- Initial seed categories are neutral starting taxonomy only; no sample articles or claims are seeded.
- CI verification: [run 40099826855](https://github.com/mazensabry2712/bahnes/actions/runs/40099826855) passed 51 tests (193 assertions), TypeScript checking and Vite production build.
- **Still pending:** media uploads/cover images, public Insights list/detail pages, reviewed article copy and related-article UI.

## Purpose

Use **Insights** as a practical knowledge area broader than a generic blog. It should earn trust by answering real customer questions and explaining work BAHNES can credibly deliver.

## Editorial pillars

- CCTV, networking, and small-business IT planning.
- Choosing, securing, and maintaining business devices.
- Inventory, sales, booking, CRM/ERP/POS, and workflow automation.
- Website planning, accessibility, SEO, performance, and e-commerce.
- Software architecture, integrations, maintenance, and security.
- Product/template walkthroughs clearly labelled as demonstrations.
- Approved case studies and delivery lessons.

## Article model

Title, slug, summary, body, cover and alt text, category, optional tags, English and Arabic versions, status, publication/update date, author/editor where appropriate, optional reading-time estimate, related articles, locale SEO, and social preview.

A lightweight Draft/Published/Archived workflow is enough initially. Add scheduled publishing only if there's a real operational need.

## Quality bar

Answer a clear question, verify version-sensitive technical claims, separate facts from opinions, cite claims where appropriate, respect licensing, and avoid keyword stuffing or copied vendor marketing. Publish screenshots/diagrams only when owned, licensed, anonymized, or authorized. A service CTA should help the reader rather than interrupt the article.

Measure useful engagement, inquiries assisted by content, and freshness—not simply the number of published posts or page views.
