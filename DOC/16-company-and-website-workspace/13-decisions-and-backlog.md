# 13 — Decisions & Backlog

This document separates the established planning direction from proposals and details that must still be confirmed. “Planned” does not mean implemented.

## Working direction to preserve

| Topic | Direction | Status |
|---|---|---|
| Company presentation | One BAHNES IT SOLUTIONS brand with two clear service lines | Agreed planning direction |
| Service line A | IT Infrastructure & Security: cameras, networking, business IT, support | Portfolio direction; capability needs validation |
| Service line B | Software & Digital Solutions: websites, systems, apps, integration | Portfolio direction; capability needs validation |
| App framework | Existing Laravel scaffold | Exists; must be audited |
| Public rendering | Laravel Blade / server-rendered | Planned baseline |
| Backend/runtime | PHP `^8.4` + Laravel `^13.17` | PHP 8.4 is required by the selected sitemap dependency and aligned with the CI baseline; target hosting still needs verification |
| Database | MySQL with Eloquent/migrations | Preferred plan; confirm hosting/environment |
| CSS/build | Tailwind CSS `^4.1` + Vite `^8.0` + Laravel Vite plugin | Selected CSS/build constraints; lockfile and Vite build are verified by CI after manifest changes |
| Client-side behavior | TypeScript modules for small browser interactions | Implemented in the asset pipeline; `tsc --noEmit` is part of the build |
| PHP quality gates | Pest + Laravel Pint + Larastan | Latest CI: 67 tests / 311 assertions, static analysis and security audits pass, TypeScript and Vite build pass |
| Admin UI | Custom Laravel Blade admin and public site | Existing controllers, views, authentication and server-side authorization remain the baseline; improve through shared Blade components and tested features |
| Inertia / React / Vue SPA | Not selected for initial website | Revisit only if requirements change |
| Motion libraries | CSS/native features first; GSAP available for advanced sequences | Use only where approved motion requires timelines/scroll triggers; respect reduced-motion |
| Separate SPA | Not required by current plan | Out of initial scope |
| Languages | Arabic and English | Planned requirement |
| Themes | Proper light and dark themes with user preference | Planned requirement |
| Public service pages | Both service lines, with detailed pages for approved offers | Planned scope |
| Work portfolio | Projects/case studies separate from products | Initial admin CRUD implemented; publication requires permissions for real work and excludes confidential cases |
| Digital catalog | Products and website templates are separate types | Separate admin catalogs implemented and CI-tested; public catalog, final assets/terms and checkout remain pending |
| Editorial | Insights/articles | Category and bilingual article admin implemented/CI-tested; public article pages implemented; final article assets and editorial content remain pending |
| CMS | Manage core content, media, locales, SEO, and inquiries | Services, Projects/Case Studies, Products, Website Templates, Insights/categories and inquiry inbox implemented; media, public forms and notification delivery pending |
| Sales flow | General contact plus guided project brief and simple pipeline | Contact submission/inbox/status/notes implemented; public contact page now shares the public layout and localized SEO; email recipient and guided brief remain pending |
| Company profile PDF | Bilingual, after company facts and identity are approved | Later deliverable |
| Interactive system showcase | Lightweight, clearly illustrative | Optional after core site |
| Client portal/full CRM | Defer until need is demonstrated | Deferred |
| Automated checkout/subscriptions | Defer until commercial flow is defined and tested | Deferred |
| Logo/colors/fonts/tagline | Proposed identity direction | Needs review/approval |

## Recent implementation notes — 2026-10-25

- The public contact page now uses the shared Blade website layout, including the common navigation, language switch, footer, canonical URL, and Arabic/English alternate metadata.
- Contact-page labels and privacy/validation copy are provided by the locale-aware public controller instead of being duplicated in a standalone HTML document.
- Replaced an internal launch reminder and an unverified retention-policy claim with accurate bilingual contact-form copy.
- Contact validation errors now use Arabic or English copy based on the route locale or the legacy form's selected language.
- Public content pagination now uses a custom bilingual Blade view with RTL-aware navigation labels and accessible current/disabled states.
- The public footer now prioritizes useful Services and Contact links instead of exposing the technical robots.txt endpoint as visitor navigation.
- The shared public header now includes an explicit localized Home link alongside the logo, with feature-test coverage for English and Arabic navigation.
- Removed unverified geographic coordinates from the homepage illustration and replaced them with neutral, localized brand messaging; tests guard against reintroducing the unsupported location claim.
- Replaced the mixed Arabic/English project-concept label with fully Arabic copy on both listing cards and detail pages, with a bilingual regression test.
- Added the existing Insights category-management route to the admin dashboard, and extended the authenticated dashboard test to assert links for every current CMS module.
- Added bilingual contact-layout/privacy-copy, Arabic validation-error, pagination, and footer navigation coverage, plus explicit home breadcrumb assertions for localized content indexes.
- GitHub Actions passed on `main` commit `dd4073a3dde7667bc50ab19cf45c752c98fbff70`: 67 tests / 311 assertions, Larastan, dependency audits, TypeScript and frontend production build. This verifies CI only; it does not confirm a live deployment.

## Package-first rule

When a maintained package is a strong fit for a non-trivial requirement, prefer it over a custom replacement. The admin is an explicit exception: keep it in custom Blade because it already exists and should stay simple. Use Spatie packages for media, sitemaps and anti-spam; Larastan for static analysis; GSAP/Lucide/Fontsource for the frontend layer. Check compatibility, maintenance, security advisories, license, bundle size and long-term cost before adoption.

## Technology decision and review rule

The canonical choice is [Website Tech Stack & Frontend Architecture](../09-technology-and-engineering/website-tech-stack.md): one Laravel app with PHP backend, custom Blade admin and public pages, Tailwind CSS 4.1+, TypeScript for browser-side code and Vite; MySQL is preferred. Do not add Inertia, a SPA, Livewire, Alpine.js or a packaged admin panel. Use GSAP only for motion that materially benefits from it.

Historical baseline: the initial feature and dependency-lock runs passed **59 Pest tests (234 assertions)**, Composer/npm security audits, Larastan, TypeScript and the Vite production build ([initial feature CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104580639); [initial dependency-lock CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104175812)). The current verified baseline is listed in the implementation notes above. CI does not verify local Herd or the deployment host.

## Decisions required before backend implementation

1. Confirm the service categories that can be delivered directly versus via suppliers/partners.
2. Confirm the initial contact destination/owner and required inquiry types.
3. [x] Audit the scaffold, lockfiles, routes, migrations and sample tests; establish a green GitHub Actions baseline on PHP 8.4 / Node 24. Local/hosting runtime and storage/database settings still need environment-specific confirmation.
4. [x] Choose the initial translation storage: explicit `_en` and `_ar` columns for the two launch locales, with locale-aware queries and SEO fields; review before adding another locale.
5. [x] Define and implement admin provisioning/recovery with CLI commands and server-side authorization.
6. [x] Establish initial service-line/service schema and admin CRUD with per-locale publication rules.
7. Confirm how inquiries are notified and stored, plus privacy/retention assumptions.
8. Select the next CMS sections/models from the documented content types; defer anything not needed for launch.

## Decisions that can wait until public launch or catalog release

- Final logo and all production brand assets.
- Public domain, final company contacts, and approved company profile copy.
- Final prices and detailed service packages.
- Actual product/template inventory, licenses, prices, delivery/update/support policy.
- Portfolio assets and client permissions.
- Exact privacy/legal copy reviewed by qualified advisers.
- Production hosting, mail provider, backups, analytics, monitoring, and DNS details.

A visual concept can be drafted before the final logo, but launch must use approved assets and accurate company information.

## Prioritization

Prioritize correctness and security, core business flows, accessibility/usability, content truthfulness, performance, then optional polish. Every feature should have a user/business reason, scope, acceptance criteria, dependencies, and a test.

The interactive showcase is optional. A reliable contact flow, clear service pages, proper content management, and correct metadata are more important for the first release.
