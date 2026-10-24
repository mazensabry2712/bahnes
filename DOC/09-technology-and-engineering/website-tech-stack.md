# BAHNES Website — Technology Stack & Frontend Architecture

**Status:** Approved stack and package policy; selected dependencies are being added and lockfiles/CI are being verified. Local and production environments remain unverified.
**Scope:** Public BAHNES IT SOLUTIONS company website and its Laravel-based admin.

## 1. Selected stack

| Layer | Decision | Notes |
|---|---|---|
| Runtime | PHP `^8.4` | Required by the selected sitemap package and aligned with the PHP test/tooling baseline; verify CLI/web PHP and image extensions. |
| Backend | Laravel `^13.17` | Declared in `composer.json`; do not change without a verified compatibility reason. |
| Rendering | Laravel Blade, server-rendered HTML | Public website and admin stay in one Laravel application; no separate SPA required. |
| Admin CMS | Filament 5 panel builder | Prefer package-provided tables, forms, resources and editor over handwritten CRUD. Existing custom admin screens are transitional and will be migrated module by module. |
| Database | MySQL as preferred relational database | Planned choice; verify local/hosting compatibility, configuration, charset and backups during Phase 0. |
| Markup | Semantic HTML | Content, navigation and forms must not depend on a client-side app. |
| CSS | Tailwind CSS `^4.1` plus purposeful custom CSS | Filament 5 requires Tailwind 4.1+; keep the direct constraints compatible. |
| Client-side language | TypeScript / ES modules | Frontend-only, for small browser interactions; the backend remains Laravel/PHP. |
| Asset build | Vite `^8.0` + `laravel-vite-plugin ^3.1` | Declared in `package.json`; actual installation/build still needs verification. |
| PHP tests | Pest `^5.3` | Existing tests remain the acceptance gate for every module. |
| PHP formatting | Laravel Pint (declared `^1.27`) | Verify install; run on changed PHP files. |
| Motion | CSS first; GSAP `^3.15` for complex, approved sequences | Use GSAP when timelines or scroll-linked motion justify it; respect reduced motion and avoid heavy animation by default. |
| Fonts | Self-hostable Fontsource packages for Manrope and IBM Plex Sans Arabic | Packages are being added as candidates; final font, weights and use remain subject to design approval and license review. |

Manifest constraints do not prove the runtime, installed dependencies, test suite or hosting compatibility have been verified.

## 2. Package-selection policy

**Package-first is the default for non-trivial, reusable capabilities.** Before writing a custom implementation, first look for a maintained package with a suitable license and direct support for the selected Laravel/PHP/Node/Tailwind versions. Prefer the package when it materially reduces code and maintenance, especially for admin CRUD, rich editing, media uploads/conversions, sitemaps, spam protection, icons, animations and code analysis.

Do not install a package solely because it exists. For each addition, verify:
- official version compatibility with PHP, Laravel, Tailwind/Vite and the target Node version;
- release activity, maintainer health, security advisories and license;
- transitive dependency footprint, frontend bundle size and hosting/runtime constraints;
- whether built-in Laravel behavior or a very small local helper is already sufficient.

Keep the lockfiles committed. Run dependency audits, tests, static analysis and the asset build after dependency changes. Do not manually fabricate lockfile entries. A custom implementation is still appropriate for BAHNES-specific business rules, published-content rules, permissions and design where a package does not provide the requirement.

### Selected package set

| Need | Selected package | Why / boundary |
|---|---|---|
| Admin CMS | `filament/filament ^5.0` | Reuse resources, tables, forms, authentication and rich editor instead of maintaining duplicate CRUD views. Filament is for the admin panel only; the public site stays Blade-rendered. |
| Media attached to Eloquent content | `spatie/laravel-medialibrary ^11.23` | Store/manage multiple images and conversions for services, projects, products, templates and Insights. Do not accept user-controlled remote URLs for imports. |
| Media fields in Filament | `filament/spatie-laravel-media-library-plugin ^5.0` | Reuse official Filament integration for media uploads/collections. |
| XML sitemap | `spatie/laravel-sitemap ^8.2` | Generate sitemap URLs for public, published content only. Never list drafts, confidential projects or unavailable locale variants. |
| Contact anti-spam | `spatie/laravel-honeypot ^4.7.3` | Add `<x-honeypot />` to the eventual Blade contact/project-brief forms and protect only their submit routes. Keep rate limiting too. |
| PHP static analysis | `larastan/larastan ^3.13` | Catch type and Laravel-specific issues before release; run it in CI. |
| Frontend motion | `gsap ^3.15` | Available for purposeful animation timelines; CSS remains the default for simple transitions. |
| Icons | `lucide ^1.61` | Consistent SVG icon source with tree-shaking; import only required icons. |
| Fonts | `@fontsource/manrope ^5.3`, `@fontsource/ibm-plex-sans-arabic ^5.3` | Self-host font assets rather than depending on a remote font CDN; final font choice still needs approval. |

Not selected yet: a separate role/permission package (until editor/admin role requirements are confirmed), a page builder, an SPA, an extra rich-text library outside Filament's editor, a full CRM/activity log, browser-test tooling before public pages exist, and packages that duplicate Laravel's built-in mail/auth/validation. Reassess against the actual feature, not by habit.

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
- Do not preselect Alpine.js or Livewire; consider them only for a specific interaction with a clear benefit.
- Use consistent tokens for color, spacing, typography, radii, shadows and motion.
- Support mobile, tablet and desktop without horizontal overflow.
- Build designed light/dark themes, use system preference initially, persist explicit user choice and prevent theme flash where practical.
- Arabic/English, RTL/LTR, keyboard access, visible focus, color contrast and browser zoom are first-class requirements.
- Optimize images/fonts, reserve image dimensions and lazy-load non-critical media.

### Motion

Prefer short CSS transitions using transform/opacity. Use Intersection Observer sparingly; content must remain available if animation or JavaScript is disabled. Respect `prefers-reduced-motion`; never delay core content, navigation or forms. Add GSAP only if an approved effect cannot be implemented cleanly with CSS/native features. Heavy 3D, scroll hijacking, autoplay media and decorative animation everywhere remain out of scope.

## 5. Localization and SEO

Use Laravel routes and server-rendered locale pages, planned as `/en/...` and `/ar/...`. Locale controls content, direction and metadata. Do not create language alternates for translations that do not exist.

Provide per-language titles/descriptions, canonical URLs, reciprocal hreflang for existing variants, XML sitemap, correct robots rules, clean 404/redirect handling and relevant Open Graph metadata. The default social preview must use the approved BAHNES logo/brand asset, not an unrelated image. A client-rendered SPA or separate SEO service is not required.

## 6. Data, files, mail and hosting

- MySQL is the preferred plan; exact local/production configuration and hosting limits must be verified.
- Use reviewed migrations; do not alter a live schema manually without a recovery plan.
- Generate upload filenames; validate content/MIME and size; prevent executable uploads; keep private files outside public access.
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
| Livewire/Alpine.js | Not preselected; revisit for a concrete interaction only. |
| GSAP | Optional after a specific motion review, not a baseline dependency. |
| Filament/packaged admin | Not selected at planning time; intended admin is custom Blade. Revisit only if the tested maintenance/time trade-off is clearly better. |
| Universal page builder, full CRM, client portal, payments, microservices | Deferred; see roadmap and decisions/backlog. |

## 8. Review triggers

Revisit this decision only if a genuine interactive journey, public API/mobile client, hosting incompatibility, materially better maintained admin option, or measured performance/accessibility evidence requires it. Update this document, the architecture page and roadmap before changing dependencies.

## Official references
- [Laravel 13 release and PHP support](https://laravel.com/docs/13.x/releases)
- [Laravel 13 upgrade guide](https://laravel.com/docs/13.x/upgrade)
- [Laravel Blade](https://laravel.com/docs/13.x/blade)
- [Laravel Vite integration](https://laravel.com/docs/13.x/vite)
- [Tailwind CSS with Laravel and Vite](https://tailwindcss.com/docs/installation/framework-guides/laravel/vite)
