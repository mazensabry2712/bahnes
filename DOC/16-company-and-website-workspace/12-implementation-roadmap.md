# 12 — Implementation Roadmap

## Principle

Finish and test the backend foundation before building the public frontend. Then implement the frontend section by section. Work in small, testable milestones and do not turn every future idea into a launch requirement.

## Package-first CMS

Keep the custom Blade admin and avoid adding a third-party admin panel. Reuse Laravel validation/authorization and shared Blade components rather than cloning similar forms. Use Spatie Media Library for media, Sitemap for public URL discovery, Honeypot plus rate limits for contact forms, and Larastan in CI.

## Phase 0 — Planning handoff and scaffold audit

- Audit the existing Laravel scaffold, Composer dependencies/lockfile, PHP/Node runtimes, routes, views, assets, configuration, storage assumptions, and tests.
- Verify `composer.json`, `composer.lock`, `package.json`, any npm lockfile, PHP extensions, Vite/Tailwind integration and declared Laravel/Pest/Pint versions in the actual environment.
- Follow the canonical [Website Tech Stack & Frontend Architecture decision](../09-technology-and-engineering/website-tech-stack.md); do not add a SPA or additional frontend/animation/admin frameworks without a documented trigger; use TypeScript for browser-side code and add explicit type-checking.
- Run the existing test suite and record the baseline before making changes.
- Confirm how the first administrator will be provisioned and how inquiries will be handled.
- Document unresolved assumptions for locale routing, storage, mail, and initial content.
- Keep service capability, supplier, legal, identity, and privacy items visible as separate content/launch gates.

**Exit criteria:** scaffold and package versions are reviewed; baseline CI passed on PHP 8.4 and Node 24. Local/hosting runtime, DB and storage settings remain separate environment checks. The initial runtime/build baseline is recorded in [GitHub Actions](https://github.com/mazensabry2712/bahnes/actions/runs/40097226745).

## Phase 1 — Backend and CMS foundation

Build the backend capabilities needed to support the agreed website content types before starting the public frontend:

- [x] Secure admin access, protected dashboard, CLI provisioning (`admin:create`) and password recovery (`admin:password-reset`); no public admin registration. Tested in CI.
- [x] First service-line/service migrations and Eloquent models with English/Arabic fields, locale-aware publish filtering, and tests.
- [x] Initial Blade admin service list, search/status filters, create/edit, and draft/published/archived workflows with validation and feature tests. Verified in [CI run 40098468204](https://github.com/mazensabry2712/bahnes/actions/runs/40098468204): 23 tests passed (83 assertions), TypeScript check and Vite build passed.
- Migrations and maintainable content models for pages/predefined homepage sections, service lines/services, projects/case studies, products, website templates, Insights/articles and categories, media metadata as needed, inquiries, locale content, and SEO fields.
- Admin create/edit/preview/publish/archive workflows for the agreed content types.
- Predefined homepage section ordering/visibility rather than a universal drag-and-drop page builder.
- Separate Projects, Products, and Website Templates in the data model and admin navigation.
- [x] Validated cover upload fields, safe generated filenames, media-library records, localized alt text and synchronous preview conversions for Services, Projects, Products, Website Templates and Insights. The public storage link is added to Composer setup; test and verify storage permissions on each environment.
- [x] Backend inquiry capture, type/status, protected internal notes, server-side validation, and a rate-limited contact submission endpoint.
- [x] Product and Website Template admin catalogs with separate models, bilingual content, unique slugs, draft/published/archived state, and license/delivery/support publication gates. Verified in [CI run 40099542157](https://github.com/mazensabry2712/bahnes/actions/runs/40099542157): 43 tests / 165 assertions, TypeScript and Vite build passed.
- [x] Insights and category admin with bilingual content/SEO fields, category CRUD, scheduled publishing and complete-translation/category-active gates. Verified in [CI run 40099826855](https://github.com/mazensabry2712/bahnes/actions/runs/40099826855): 51 tests / 193 assertions, TypeScript and Vite build passed.
- [x] Bilingual public contact form, Spatie Honeypot middleware, and dynamic sitemap/robots endpoints are implemented and included in the current CI run.
- [ ] Confirm notification destination/provider and implement safe email delivery/failure handling after the recipient is approved.
- Localization foundations for Arabic/English, plus per-locale slug and SEO metadata as appropriate.
- Automated tests for access control, content visibility, validation, locale behavior, uploads, and inquiry submission.

**Exit criteria:** admin workflows and core backend behavior pass the agreed tests; unpublished content is not publicly exposed; content types remain distinct. Latest backend evidence is [run 40099826855](https://github.com/mazensabry2712/bahnes/actions/runs/40099826855): 51 tests passed (193 assertions), TypeScript check and Vite build passed. Empty catalogs can stay unpublished until real items exist.

## Phase 2 — Shared public frontend foundation

- Build the shared Blade layout, navigation, footer, typography, spacing, and reusable section views.
- Use Tailwind CSS 4 and limited custom CSS compiled through Laravel Vite.
- Add focused TypeScript modules for browser interactions, with `tsc --noEmit` included in verification. Use CSS/native browser features for ordinary motion and honor reduced-motion settings.
- Implement English/Arabic routes, RTL/LTR, language switching, light/dark themes, and preference persistence.
- Implement baseline metadata, canonical/locale alternates, social sharing metadata, sitemap, robots, and useful error states.
- Verify keyboard accessibility, reduced motion, responsive behavior, and media performance.

**Exit criteria:** the shared site foundation works across languages, themes, and screen sizes.

## Phase 3 — Core service website

- Build Home, Services overview, separate IT Infrastructure & Security and Software & Digital Solutions entry points, approved service detail pages, About, and Contact.
- Connect content to the CMS so routine copy/media changes do not require code edits.
- Test the full contact and inquiry path.
- Publish only service offers whose real delivery capability and terms are confirmed.

**Exit criteria:** a visitor can understand both service lines and send a useful inquiry; an admin can update approved content.

## Phase 4 — Trust and inbound sections

- Render Projects/Case Studies with accurate Completed, In Progress, Concept, or Confidential labels.
- Render Insights when reviewed articles exist.
- Implement the guided Project Brief form and lightweight inquiry pipeline.
- Prepare the bilingual company profile PDF after identity and company details are approved.
- Document the Care & Maintenance offer.

**Exit criteria:** all published proof is authorized and accurate; inquiries can be reviewed and followed up.

## Phase 5 — Product and template catalog frontend

- Build separate Products and Website Templates catalog/detail pages.
- Add real screenshots, safe demos/previews, license, prices, updates, delivery, and support terms only when available.
- Test demo, quote, and customization inquiry paths.
- Keep checkout/subscriptions out until the complete commercial flow is defined and tested.

**Exit criteria:** the catalog accurately represents real, supportable offerings; no placeholder purchase function suggests that payment works.

## Phase 6 — Pre-launch verification

- Test forms, mail, admin access, permissions, locales, themes, SEO, media, and performance.
- Review legal/privacy content with appropriate professional advice.
- Verify domain/hosting/DNS, backups, rollback, monitoring, and operational ownership.
- Run smoke tests on a release candidate and record the results.
- Measure qualified inquiries, follow-up, collection, delivery effort, and support load after launch.

## Deferred until justified

Client portal, full CRM, automated payments/licensing/subscriptions, complex universal page builder, multi-tenant/microservice architecture, heavy 3D, and excessive animation. Add them only if evidence justifies the build and maintenance cost.
