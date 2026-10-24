# 09 — Architecture & Data Model

## Baseline

- Laravel with server-rendered Blade views.
- Relational database, Laravel migrations, and Eloquent where appropriate.
- Existing scaffold is the starting point and must be audited before changing dependencies.
- No SPA or public mobile API is required by the current website plan.
- Separate validation, authorization, business rules, and presentation appropriately without unnecessary layers.

The repository declares PHP ^8.4 and Laravel ^13.17. GitHub Actions has verified the lockfiles and selected packages; actual local and target-host runtimes still need confirmation.

## Technology implementation baseline

Use the canonical [Website Tech Stack & Frontend Architecture decision](../09-technology-and-engineering/website-tech-stack.md).

- **Application:** PHP `^8.4` and Laravel `^13.17`; lockfile resolution and dependency health are CI-verified, while local/hosting PHP and extensions remain to be checked.
- **Rendering:** server-rendered Blade for public pages; Filament is the selected admin package. Existing custom admin screens remain transitional until module-by-module Filament migration.
- **Frontend:** semantic HTML, Tailwind CSS `^4.1`, limited custom CSS, TypeScript browser modules and Vite `^8.0` through Laravel Vite. Run `tsc --noEmit` separately because Vite transpiles but does not type-check.
- **Database:** MySQL preferred; confirm local/hosting compatibility, then use Eloquent and migrations.
- **Quality tools:** Pest, Pint and Larastan are dependencies; the current set passes 51 tests (193 assertions), Larastan, Composer/npm audits and Vite build ([CI run 40101984829](https://github.com/mazensabry2712/bahnes/actions/runs/40101984829)).
- **Not selected:** Inertia/React/Vue SPA for the public website, separate public API, microservices or universal page builder. Filament 5 is now selected for admin CMS resources; its internal Livewire/Alpine runtime stays inside the admin panel. TypeScript is only for browser assets.

## Suggested records

- Page and predefined page-section content.
- Service line and service.
- Project/case study.
- Product.
- Website template.
- Insight article, category, and optional tag.
- Media metadata if required by the chosen storage setup.
- Contact message/inquiry with type, status, source, and next action.
- FAQ if recurring questions justify reusable records.
- Per-locale content and SEO fields in a consistent structure.

Products/templates must stay separate content types; portfolio proof must stay distinct from commercial catalog items. Do not put every type into one giant JSON record simply to reduce table count.

## Admin and translations

Use admin authentication and server-side authorization; safe uploads; consistent publication states; and pagination/filtering where needed. For the two agreed launch languages, the initial content schema uses explicit `_en` and `_ar` columns, with locale-specific slugs and SEO fields. A service is visible in a locale only when it is published, its publication time has arrived, its service line is active, and that locale has a non-empty title and slug. This deliberate two-language approach avoids a generic translation engine; review the schema before adding a third language.

## Media

Use the selected storage driver, safe generated names, content/size validation, and protected storage paths. Prevent executable uploads, optimize public images, and document backups/deletion. Keep private files outside public access.

## Package-first decision

Use maintained packages for non-trivial shared capabilities rather than expanding bespoke infrastructure. Filament 5 is selected for admin resources and forms; Spatie Media Library plus its Filament plugin is selected for uploaded images/conversions; Spatie Sitemap handles XML sitemap generation; Spatie Honeypot complements rate limits on public forms; Larastan runs static analysis. Existing custom admin screens are transitional: migrate each module to Filament only after equivalent permissions, validation and tests exist. Do not create duplicate parallel CMS workflows in the final application.

The public site remains custom server-rendered Blade with Tailwind and TypeScript. GSAP/Lucide/Fontsource are available for the public visual layer, with package bundles limited to the actual features used.

## Current implementation slice

- **Implemented and CI-verified:** secure administrator sign-in/provisioning and the first `ServiceLine` / `Service` models and migrations.
- **Implemented and CI-verified:** per-locale publication scope for services, scheduled publication filtering, active-line filtering, and the two agreed default service-line records.
- **Service CRUD:** initial Blade admin list/search/status filter/create/edit/archive workflows are CI-verified in [run 40098468204](https://github.com/mazensabry2712/bahnes/actions/runs/40098468204) (23 tests, 83 assertions).
- **Projects / Case Studies CRUD:** admin list/search/filter/create/edit/archive status and separate project stage, bilingual content, permission-aware publication and confidential-project exclusion are CI-verified in [run 40098999954](https://github.com/mazensabry2712/bahnes/actions/runs/40098999954).
- **Inquiry intake and inbox:** `POST /contact` with validation/rate limiting, protected admin inbox, status changes and admin-only notes are CI-verified in [run 40099285127](https://github.com/mazensabry2712/bahnes/actions/runs/40099285127).
- **Products / Website Templates:** separate migrations, models, localized admin forms/lists and publish requirements are CI-verified in [run 40099542157](https://github.com/mazensabry2712/bahnes/actions/runs/40099542157).
- **Insights / categories:** bilingual category and article schema plus admin CRUD with locale-aware publication filtering passed [CI run 40099826855](https://github.com/mazensabry2712/bahnes/actions/runs/40099826855): 51 tests / 193 assertions.
- **Not yet implemented:** media upload UI, public contact page/form, email notification delivery, and public service/project/catalog/editorial pages.

## Implementation sequence

1. Audit routes, models, dependencies, tests, config, views, and assets.
2. Choose the smallest schema that supports the requirements.
3. Add migrations, authorization, validation, and tests.
4. Implement admin workflows.
5. Build shared Blade layouts/section views.
6. Add public pages and catalogs incrementally.
7. Test changes before deployment.

This is a proposed architecture, not a claim that these models or features already exist.
