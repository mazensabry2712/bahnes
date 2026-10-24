# 12 — Implementation Roadmap

## Principle

Finish and test the backend foundation before building the public frontend. Then implement the frontend section by section. Work in small, testable milestones and do not turn every future idea into a launch requirement.

## Phase 0 — Planning handoff and scaffold audit

- Audit the existing Laravel scaffold, Composer dependencies/lockfile, PHP/Node runtimes, routes, views, assets, configuration, storage assumptions, and tests.
- Run the existing test suite and record the baseline before making changes.
- Confirm how the first administrator will be provisioned and how inquiries will be handled.
- Document unresolved assumptions for locale routing, storage, mail, and initial content.
- Keep service capability, supplier, legal, identity, and privacy items visible as separate content/launch gates.

**Exit criteria:** the real scaffold/test baseline is known; the first backend milestone is bounded; unknowns and safe assumptions are documented.

## Phase 1 — Backend and CMS foundation

Build the backend capabilities needed to support the agreed website content types before starting the public frontend:

- Secure admin access and a documented first-admin provisioning/recovery path; no public admin registration by default.
- Migrations and maintainable content models for pages/predefined homepage sections, service lines/services, projects/case studies, products, website templates, Insights/articles and categories, media metadata as needed, inquiries, locale content, and SEO fields.
- Admin create/edit/preview/publish/archive workflows for the agreed content types.
- Predefined homepage section ordering/visibility rather than a universal drag-and-drop page builder.
- Separate Projects, Products, and Website Templates in the data model and admin navigation.
- Validated uploads, safe storage paths, authorization, input validation, and safe output handling.
- Backend inquiry capture, inquiry type/status, protected internal notes where needed, and clear notification failure handling.
- Localization foundations for Arabic/English, plus per-locale slug and SEO metadata as appropriate.
- Automated tests for access control, content visibility, validation, locale behavior, uploads, and inquiry submission.

**Exit criteria:** admin workflows and core backend behavior pass the agreed tests; unpublished content is not publicly exposed; content types remain distinct. Empty catalogs can stay unpublished until real items exist.

## Phase 2 — Shared public frontend foundation

- Build the shared Blade layout, navigation, footer, typography, spacing, and reusable section views.
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
