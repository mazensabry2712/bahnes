# 09 — Architecture & Data Model

## Baseline

- Laravel with server-rendered Blade views.
- Relational database, Laravel migrations, and Eloquent where appropriate.
- Existing scaffold is the starting point and must be audited before changing dependencies.
- No SPA or public mobile API is required by the current website plan.
- Separate validation, authorization, business rules, and presentation appropriately without unnecessary layers.

The repository declares PHP ^8.3 and Laravel ^13.17. Verify actual local runtime, installed dependencies, and target hosting compatibility before implementation/deployment.

## Technology implementation baseline

Use the canonical [Website Tech Stack & Frontend Architecture decision](../09-technology-and-engineering/website-tech-stack.md).

- **Application:** PHP `^8.3` and Laravel `^13.17` are declared by Composer; verify runtime and lockfiles in Phase 0.
- **Rendering:** server-rendered Blade for public pages and custom admin in one Laravel application.
- **Frontend:** semantic HTML, Tailwind CSS `^4.0`, limited custom CSS, TypeScript browser modules and Vite `^8.0` through Laravel Vite. Run `tsc --noEmit` separately because Vite transpiles but does not type-check.
- **Database:** MySQL preferred; confirm local/hosting compatibility, then use Eloquent and migrations.
- **Quality tools:** Pest and Laravel Pint are declared dependencies; baseline tests/format/build checks remain pending.
- **Not selected:** Inertia, React/Vue SPA, Livewire, Alpine.js, required GSAP, packaged admin panel, separate public API, microservices or universal page builder. TypeScript is selected only for browser-side code.

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

## Current implementation slice

- **Implemented and CI-verified:** secure administrator sign-in/provisioning and the first `ServiceLine` / `Service` models and migrations.
- **Implemented and CI-verified:** per-locale publication scope for services, scheduled publication filtering, active-line filtering, and the two agreed default service-line records.
- **Service CRUD:** initial Blade admin list/search/status filter/create/edit/archive workflows are CI-verified in [run 40098468204](https://github.com/mazensabry2712/bahnes/actions/runs/40098468204) (23 tests, 83 assertions).
- **Projects / Case Studies CRUD:** admin list/search/filter/create/edit/archive status and separate project stage, bilingual content, permission-aware publication and confidential-project exclusion are CI-verified in [run 40098999954](https://github.com/mazensabry2712/bahnes/actions/runs/40098999954).
- **Not yet implemented:** media upload UI, product/template/article content types, inquiry workflow, and public service/project pages.

## Implementation sequence

1. Audit routes, models, dependencies, tests, config, views, and assets.
2. Choose the smallest schema that supports the requirements.
3. Add migrations, authorization, validation, and tests.
4. Implement admin workflows.
5. Build shared Blade layouts/section views.
6. Add public pages and catalogs incrementally.
7. Test changes before deployment.

This is a proposed architecture, not a claim that these models or features already exist.
