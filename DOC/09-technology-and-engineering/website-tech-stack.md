# BAHNES Website — Technology Stack & Frontend Architecture

**Status:** Approved stack and package policy. The custom Laravel Blade admin remains the chosen approach. Media/contact/SEO integration, self-hosted fonts, Lucide and dependency lockfiles all pass CI ([feature CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104580639); [dependency-lock CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104175812)); local and production environments remain unverified.
**Scope:** Public BAHNES IT SOLUTIONS company website and its Laravel-based admin.

## 1. Selected stack

| Layer | Decision | Notes |
|---|---|---|
| Runtime | PHP `^8.4` | Required by the selected sitemap package and aligned with the PHP test/tooling baseline; verify CLI/web PHP and image extensions. |
| Backend | Laravel `^13.17` | Declared in `composer.json`; do not change without a verified compatibility reason. |
| Rendering | Laravel Blade, server-rendered HTML | Public website and admin stay in one Laravel application; no separate SPA required. |
| Admin CMS | Custom Laravel Blade controllers and views | Keep the existing admin, reuse Blade layouts/components, and apply Laravel-native validation and authorization. Do not add a third-party admin panel. |
| Database | MySQL as preferred relational database | Planned choice; verify local/hosting compatibility, configuration, charset and backups during Phase 0. |
| Markup | Semantic HTML | Content, navigation and forms must not depend on a client-side app. |
| CSS | Tailwind CSS `^4.1` plus purposeful custom CSS | Use the existing Tailwind 4.1+ and Vite integration; keep the manifest and lockfile in sync. |
| Client-side language | TypeScript / ES modules | Frontend-only, for small browser interactions; the backend remains Laravel/PHP. |
| Asset build | Vite `^8.0` + `laravel-vite-plugin ^3.1` | Declared in `package.json`; actual installation/build still needs verification. |
| PHP tests | Pest `^5.3` | Existing tests remain the acceptance gate for every module. |
| PHP formatting | Laravel Pint (declared `^1.27`) | Verify install; run on changed PHP files. |
| Motion | CSS first; GSAP `^3.15` for complex, approved sequences | Use GSAP when timelines or scroll-linked motion justify it; respect reduced motion and avoid heavy animation by default. |
| Fonts | `@fontsource/manrope ^5.3`, `@fontsource/ibm-plex-sans-arabic ^5.3` | Both font families and weights 400/500/600/700 are imported into Vite CSS and built as local assets; typography may be adjusted later if brand review requires it. |

The latest feature CI passed **59 Laravel tests (234 assertions)**, Larastan, Composer/npm audits, TypeScript type-checking and Vite production build ([feature CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104580639)). Dependency lock regeneration and the same quality gates passed independently ([dependency-lock CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104175812)). GitHub CI does not verify the local Herd or production host.

## 2. Package-selection policy

**Package-first is the default for non-trivial, reusable capabilities, except the admin panel, which is deliberately custom Blade.** Before writing a custom implementation, look for a maintained package with a suitable license and direct support for the selected Laravel/PHP/Node/Tailwind versions. Prefer a package when it materially reduces code and maintenance for media uploads/conversions, sitemaps, spam protection, icons, animations and code analysis. Do not replace the existing admin with a packaged panel.

Do not install a package solely because it exists. For each addition, verify:
- official version compatibility with PHP, Laravel, Tailwind/Vite and the target Node version;
- release activity, maintainer health, security advisories and license;
- transitive dependency footprint, frontend bundle size and hosting/runtime constraints;
- whether built-in Laravel behavior or a very small local helper is already sufficient.

Keep the lockfiles committed. Run dependency audits, tests, static analysis and the asset build after dependency changes. Do not manually fabricate lockfile entries. A custom implementation is still appropriate for BAHNES-specific business rules, published-content rules, permissions and design where a package does not provide the requirement.

### Selected package set

| Need | Selected package | Why / boundary |
|---|---|---|
| Admin CMS | Custom Laravel Blade controllers and views | Keep the existing admin, reuse Blade layouts/components, and apply Laravel-native validation and authorization. Do not add a third-party admin panel. |
| Media attached to Eloquent content | `spatie/laravel-medialibrary ^11.23` | Cover uploads are connected to Services, Projects, Products, Website Templates and Insights, with JPG/PNG/WebP validation, a 5 MiB limit, UUID filenames, one cover per record, localized alt text and a generated preview. |

| XML sitemap | `spatie/laravel-sitemap ^8.2` | A dynamic `/sitemap.xml` endpoint currently lists only implemented public routes (home and English/Arabic contact). Add content URLs when public detail/list routes exist; never list drafts or confidential work. |
| Contact anti-spam | `spatie/laravel-honeypot ^4.7.3` | The localized Blade contact form already renders `<x-honeypot />`; its POST routes use the package middleware and a five-per-minute rate limit. Email notifications remain separate. |
| PHP static analysis | `larastan/larastan ^3.13` | Catch type and Laravel-specific issues before release; run it in CI. |
| Frontend motion | `gsap ^3.15` | Available for purposeful animation timelines; CSS remains the default for simple transitions. |
| Icons | `lucide ^1.61` | Wired into the TypeScript entry with `createIcons`; only the `Send` icon used by the contact form is imported, preserving tree-shaking. |
| Fonts | `@fontsource/manrope ^5.3`, `@fontsource/ibm-plex-sans-arabic ^5.3` | Both font families and weights 400/500/600/700 are imported into Vite CSS and built as local assets; typography may be adjusted later if brand review requires it. |

Not selected: a separate role/permission package (until multiple admin roles are required), a universal page builder, an SPA, a rich-text editor package, a full CRM/activity log, browser-test tooling before public pages exist, and packages that duplicate Laravel's built-in mail/auth/validation. The custom admin remains Blade-based. Reassess against the actual feature, not by habit.

## 3. Application structure

Keep routes, validation, authorization, business rules, database access, the CMS and public pages inside Laravel.
- Use Eloquent and migrations for relational records/schema changes.
- Keep routes focused; use controllers/actions and Laravel validation/authorization conventions.
- Reuse Blade layouts, components and partials for navigation, footer, forms, cards and predefined sections.
- Keep business logic out of Blade templates. Add abstractions only when they improve maintainability or testability.
- Keep secrets and environment-specific settings outside Git.

CMS content includes pages/predefined homepage sections, services, projects/case studies, products, website templates, Insights/articles/categories, media metadata where needed, inquiries, locale content and SEO fields. Keep different content types separate. Draft and archived content must not appear publicly. A universal drag-and-drop page builder is out of scope.

## 4. Frontend, responsive behavior and themes

- Blade renders public and admin pages. Core navigation and contact flows should work without a JavaScript application.
- Use Tailwind CSS 4 for consistent responsive styling and custom CSS for design tokens, theme variables and exceptions.
- Write browser-side modules in TypeScript for interactions such as mobile navigation, theme toggling and small accordions. Keep the code focused; TypeScript does not imply React, Inertia, a SPA or a large client-side application.
- Include a strict-enough `tsconfig.json` and run `tsc --noEmit` so type errors fail the relevant build/check command. Vite transpiles TypeScript but does not type-check it by itself; a Vite build alone is not sufficient verification.
- Do not add Alpine.js or Livewire to the baseline. Use small TypeScript modules for browser interactions and consider another runtime only if a concrete requirement justifies it.
- Use consistent tokens for color, spacing, typography, radii, shadows and motion.
- Support mobile, tablet and desktop without horizontal overflow.
- Build designed light/dark themes, use system preference initially, persist explicit user choice and prevent theme flash where practical.
- Arabic/English, RTL/LTR, keyboard access, visible focus, color contrast and browser zoom are first-class requirements.
- Optimize images/fonts, reserve image dimensions and lazy-load non-critical media.

### Motion

Prefer short CSS transitions using transform/opacity. Use Intersection Observer sparingly; content must remain available if animation or JavaScript is disabled. Respect `prefers-reduced-motion`; never delay core content, navigation or forms. Add GSAP only if an approved effect cannot be implemented cleanly with CSS/native features. Heavy 3D, scroll hijacking, autoplay media and decorative animation everywhere remain out of scope.

## 5. Localization and SEO

Use Laravel routes and server-rendered locale pages, planned as `/en/...` and `/ar/...`. Locale controls content, direction and metadata. Do not create language alternates for translations that do not exist.

Provide per-language titles/descriptions, canonical URLs, reciprocal hreflang for existing variants, XML sitemap, correct robots rules, clean 404/redirect handling and relevant Open Graph metadata. Implemented now: dynamic `/sitemap.xml` and `/robots.txt` endpoints; the sitemap currently includes only the homepage and existing English/Arabic contact routes. Add content URLs when public detail/list routes are implemented. The default social preview must use the approved BAHNES logo/brand asset, not an unrelated image.

## 6. Data, files, mail and hosting

- MySQL is the preferred plan; exact local/production configuration and hosting limits must be verified.
- Use reviewed migrations; do not alter a live schema manually without a recovery plan.
- Generate upload filenames; validate content/MIME and size; prevent executable uploads. New CMS media originals and previews use the `bahnes_images` disk rooted at `<project-root>/Bahnes_img`, outside `public/`; Laravel serves only allow-listed JPG/JPEG/PNG/WebP media through `/media/{path}` without a public symlink. The directory is runtime storage and is git-ignored except for `.gitkeep`. Confirm persistent disk/backups and PHP `gd`, `exif`, and `fileinfo` on production. Existing media records retain their previously assigned disk until a controlled migration is performed.
- Media disk, mail provider/recipient, queue strategy, backup destination, analytics and hosting remain environment decisions. Never hard-code secrets.
- Do not add Redis, queues, search infrastructure, public API, microservices or multi-tenancy without a demonstrated requirement.

## 7. Quality gates

Before the first code change, record PHP/Composer/Node/npm versions, PHP extensions, dependency manifests and lockfiles, routes/views/models/migrations/config/storage/tests, baseline test results and asset-build result, and database/hosting compatibility.

For each relevant milestone:
- Run Laravel/Pest tests and add tests for new behavior.
- Run Pint on changed PHP code.
- Run `tsc --noEmit` for TypeScript type-checking and `npm run build` for Vite/Tailwind asset compilation after asset changes; both must pass.
- Browser-check key public/admin flows on desktop/mobile, Arabic/English and light/dark themes.
- Verify RTL/LTR, keyboard/focus, reduced motion, validation, authorization, draft privacy, uploads, SEO and social previews.
- Record actual commands/results. A manifest or commit is not evidence that a check passed.

## 7. Explicitly not selected for the initial site

| Option | Decision |
|---|---|
| Inertia.js | Not selected; Blade meets the current requirement. |
| React/Vue SPA | Not selected; no separate frontend app is needed. |
| TypeScript | Selected for the small frontend asset layer, with explicit `tsc --noEmit` checks. It does not change the Laravel/PHP backend or require an SPA. |
| Livewire/Alpine.js | Excluded from the baseline; browser interactions use focused TypeScript modules. |
| GSAP | Optional after a specific motion review, not a baseline dependency. |
| Packaged admin panel | Not selected; custom Laravel Blade admin is the agreed direction. Revisit only if a measured maintenance/time trade-off justifies a change. |
| Universal page builder, full CRM, client portal, payments, microservices | Deferred; see roadmap and decisions/backlog. |

## 8. Review triggers

Revisit this decision only if a genuine interactive journey, public API/mobile client, hosting incompatibility, materially better maintained admin option, or measured performance/accessibility evidence requires it. Update this document, the architecture page and roadmap before changing dependencies.

## Official references
- [Laravel 13 release and PHP support](https://laravel.com/docs/13.x/releases)
- [Laravel 13 upgrade guide](https://laravel.com/docs/13.x/upgrade)
- [Laravel Blade](https://laravel.com/docs/13.x/blade)
- [Laravel Vite integration](https://laravel.com/docs/13.x/vite)
- [Tailwind CSS with Laravel and Vite](https://tailwindcss.com/docs/installation/framework-guides/laravel/vite)


## Root-level CMS media storage — 2026-10-25

- Added `Bahnes_img/` at the project root, outside `public/`; runtime uploads are ignored by Git and the directory is preserved with `.gitkeep`.
- Added the `bahnes_images` local filesystem disk rooted at `base_path('Bahnes_img')` and changed Spatie Media Library's default disk for new uploads to that disk.
- Added `/media/{path}` delivery through a Laravel controller. It only serves files under numeric media IDs, permits image extensions JPG/JPEG/PNG/WebP, rejects other paths/extensions, and sets `nosniff` plus long-lived cache headers. No symlink into `public/` is created.
- Added feature tests for serving a stored image, rejecting non-image extensions, and checking the storage configuration. These tests are committed but still require a CI run to establish their result.
- Important: changing the default disk affects new uploads. Existing Media Library records retain the disk recorded in their database rows; migrate them deliberately after backup and a tested dry run. Production deployment and storage migration have not been performed.
