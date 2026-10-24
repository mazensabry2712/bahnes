# 15 — Pre-Build Readiness Gate

This is the planning checkpoint before implementation. It separates the decisions required to start coding from details that can be completed closer to public launch. It is not a requirement to perfect every business detail in advance.

## A. Planning direction established

These are agreed directions for planning, not evidence that the features or content already exist:

- [x] One BAHNES IT SOLUTIONS customer-facing brand.
- [x] Two service lines: IT Infrastructure & Security, and Software & Digital Solutions.
- [x] One website serving both audiences with clearly separated service content.
- [x] Laravel + Blade server-rendered pages; no separate SPA requirement.
- [x] Arabic and English with correct RTL/LTR behavior.
- [x] Properly designed light and dark themes.
- [x] Projects/case studies, Products, and Website Templates remain separate content types.
- [x] Insights/articles as an editorial section.
- [x] Admin management for content, media, locales, SEO, and inquiries.
- [x] Security, accessibility, performance, SEO, and tests are requirements from the start.
- [x] Backend is prepared first; the public frontend is then built section by section.
- [x] Client portal, full CRM, automated checkout/subscriptions, and heavy 3D are outside the initial scope unless justified later.

## B. Required before the first backend coding milestone

Resolve each point or record a safe temporary assumption:

- [ ] **Repository baseline:** Inspect PHP/Composer/Node versions, lockfiles, routes, views, models, migrations, tests, config, and available storage. Record current test results before changing anything.
- [ ] **Admin access:** Decide how the first administrator is created and recovered. No public admin registration by default.
- [ ] **Inquiry handling:** Decide which inquiry types are captured and record the initial notification/recipient assumption. Real mail secrets and live recipient details may remain environment-specific.
- [ ] **Locale approach:** Choose route/default-locale and translation storage after auditing the existing scaffold.
- [ ] **Storage/mail assumptions:** Define development defaults and safe configuration points. Keep secrets outside Git.
- [ ] **CMS boundary:** Confirm the manageable content types and fields listed in the content-model document. Use predefined homepage section types with order/visibility controls; do not build a universal page builder.
- [ ] **Publication rules:** Define Draft/Published/Archived behavior and ensure unpublished content never appears publicly.
- [ ] **First milestone acceptance:** Agree to observable pass/fail criteria, including admin authorization, validation, publishing, inquiry submission, safe uploads, locale handling, and automated tests.

**Important distinction:** The data model can support the full planned service taxonomy now. Individual services must be marked unavailable/draft until delivery capability, supplier dependencies, warranty, and support terms are confirmed. Those commercial confirmations are required before publishing/selling offers, not a reason to block the initial schema design.

If an item is unknown, record the assumption, its risk, and when it must be resolved. Do not silently treat an assumption as a verified fact.

## C. Required before the public website launches

- [ ] Verify company/legal and public contact information; obtain appropriate professional advice on Egyptian registration, tax, privacy, and any relevant permissions.
- [ ] Approve logo, final color/typography system, and public copy.
- [ ] Confirm domain, hosting, DNS, mail, storage, backups, monitoring, and deployment/rollback process.
- [ ] Confirm which service offers can be sold and their scope, supplier dependencies, warranty, and support terms.
- [ ] Review Arabic and English content.
- [ ] Test inquiry forms, mail notifications, rate limits, validation, and privacy wording.
- [ ] Validate responsive behavior, themes, RTL/LTR, keyboard use, reduced motion, SEO metadata, sitemap, robots, canonical/hreflang, social previews, and performance.
- [ ] Publish only authorized projects and substantiated customer claims.
- [ ] Keep empty or unready product/template catalogs unpublished rather than filling them with fabricated content.

## D. Additional gate before selling a product or template online

- [ ] Confirm the actual item exists and can be supported.
- [ ] Define license, price, delivery, updates, prerequisites, support limits, refunds/cancellation, and rights to included third-party assets.
- [ ] Test the full buyer journey and delivery.
- [ ] Add online payment only after provider configuration, accounting/tax treatment, failure handling, security, refunds, and support processes are defined and tested.

## E. Agreed build order

1. Audit the scaffold and record the test baseline.
2. Build and test the **backend first**: admin access, content models, validation, authorization, media handling, publishing, localization/SEO fields, and inquiry handling for the agreed CMS content types.
3. Verify backend behavior and record the test results before relying on it.
4. Build the Blade frontend section by section using the backend.
5. Show public portfolio/catalog sections only when accurate content and terms are ready.
6. Run release checks before deployment.

This is a staged build, not a demand to implement payments, customer accounts, a full CRM, or an advanced page builder.

## Definition of “planning complete enough to start”

Planning is ready for the first backend milestone when section A is accepted, section B is reviewed, unknowns are written down, the first milestone has a bounded scope, and its acceptance criteria are testable. Sections C and D are launch/sales gates; they do not all need to be finalized before coding begins.
