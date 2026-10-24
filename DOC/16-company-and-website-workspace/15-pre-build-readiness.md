# 15 — Pre-Build Readiness Gate

This is the planning checkpoint before implementation. It separates the decisions required to start coding from details that can be completed closer to public launch. It is not a requirement to perfect every business detail in advance.

## A. Planning direction established

These are agreed directions for planning, not evidence that the features or content already exist:

- [x] One BAHNES IT SOLUTIONS customer-facing brand.
- [x] Two service lines: IT Infrastructure & Security, and Software & Digital Solutions.
- [x] One website serving both audiences with clearly separated service content.
- [x] Laravel + Blade server-rendered pages; no separate SPA requirement.
- [x] Technology baseline documented: Laravel 13 + PHP 8.4+ + custom Blade admin and public pages + Tailwind CSS 4.1+ + TypeScript browser modules + Vite; MySQL is preferred.
- [x] Inertia, React/Vue SPA and nonessential interaction frameworks are excluded from the initial stack unless a real requirement changes. TypeScript is used only for browser-side modules, not as backend runtime.
- [x] Arabic and English with correct RTL/LTR behavior.
- [x] Properly designed light and dark themes.
- [x] Projects/case studies, Products, and Website Templates remain separate content types.
- [x] Insights/articles as an editorial section.
- [x] Admin management for content, media, locales, SEO, and inquiries.
- [x] Security, accessibility, performance, SEO, and tests are requirements from the start.
- [x] Backend is prepared first; the public frontend is then built section by section.
- [x] Client portal, full CRM, automated checkout/subscriptions, and heavy 3D are outside the initial scope unless justified later.

## Package selection

- [x] Package-first rule: prefer a maintained compatible package for non-trivial shared capabilities, but avoid packages for trivial helpers or where complexity exceeds value.
- [x] Selected: custom Blade admin, Spatie Media Library for media, Spatie Sitemap for public URLs, Spatie Honeypot for public forms, Larastan for PHP analysis, GSAP/Lucide and self-hostable fonts for the frontend layer.
- [x] Removed the rejected third-party admin-panel packages. Composer regenerated `composer.lock`; the updated package set passed Composer/npm audits, Larastan, 51 tests (193 assertions), TypeScript checking and Vite production build ([run 40102913453](https://github.com/mazensabry2712/bahnes/actions/runs/40102913453)). No third-party admin-panel packages remain in `composer.json` or `composer.lock`.


## B. Required before the first backend coding milestone

Resolve each point or record a safe temporary assumption:

- [x] **Repository scaffold and CI baseline:** Reviewed manifests/lockfiles, routes, views, models, migrations and sample tests. GitHub Actions passed Composer validation/install, Laravel tests, `npm ci`, TypeScript checking and Vite build on PHP 8.4 / Node 24 ([baseline run](https://github.com/mazensabry2712/bahnes/actions/runs/40097226745); [TypeScript run](https://github.com/mazensabry2712/bahnes/actions/runs/40097563846)).
- [x] **Admin access:** Implemented protected admin login/dashboard, CLI account creation and password reset; feature tests pass in [CI](https://github.com/mazensabry2712/bahnes/actions/runs/40097754107). No public admin registration.
- [ ] **Local/hosting runtime and storage:** Confirm the selected PHP executable in Herd, PHP `gd`/`exif`/`fileinfo`, local Node/npm, MySQL connection, media-storage path/permissions, `storage:link`, and target-host compatibility. CI success does not verify the user's local machine or deployment host.
- [x] **Inquiry types and storage:** Defined inquiry categories, input validation, default status/source and admin-only notes; localized public contact forms use Spatie Honeypot plus rate limits, and submissions are stored in the database.
- [ ] **Notification destination:** Confirm the real inbox/owner before enabling email notifications. Mail credentials and provider settings remain environment-specific.
- [x] **Translation storage:** The first content models use explicit `_en` and `_ar` columns with locale-aware publication scopes. Route prefixes/default locale will be confirmed before public page routing.
- [ ] **Storage/mail assumptions:** The media library now stores one validated cover image per content record on the public disk with preview conversion and localized alt text. Confirm production storage persistence/backups and the real mail recipient/provider; never commit secrets.
- [ ] **CMS boundary:** Confirm the manageable content types and fields listed in the content-model document. Use predefined homepage section types with order/visibility controls; do not build a universal page builder.
- [x] **Initial service publication rules:** Services default to draft; locale queries require published status, a complete locale title/slug, an active service line, and a reached publication time. Apply the same tests to future content types.
- [x] **Admin-access milestone acceptance:** Admin-only sign-in, regular-user denial, guest redirect, logout/session invalidation, CLI creation/reset and scaffold checks pass in CI.
- [x] **First service CMS milestone:** Service list/search/status filters, create/edit, localized fields, publish validation and archive state passed CI ([run 40098468204](https://github.com/mazensabry2712/bahnes/actions/runs/40098468204)).
- [x] **Projects / Case Studies:** Admin CRUD and permission-aware publication rules passed CI ([run 40098999954](https://github.com/mazensabry2712/bahnes/actions/runs/40098999954)).
- [x] **Inquiry backend milestone:** Rate-limited contact POST, database storage, admin inbox, status and protected notes passed CI ([run 40099285127](https://github.com/mazensabry2712/bahnes/actions/runs/40099285127)).
- [x] **Products / Website Templates milestone:** separate admin catalogs, bilingual fields and publication gates passed CI ([run 40099542157](https://github.com/mazensabry2712/bahnes/actions/runs/40099542157)).
- [x] **Insights / categories milestone:** bilingual category/article CRUD, scheduling and locale/category publication guards passed CI ([run 40099826855](https://github.com/mazensabry2712/bahnes/actions/runs/40099826855)): 51 tests, 193 assertions, TypeScript check and Vite build.

**Important distinction:** The data model can support the full planned service taxonomy now. Individual services must be marked unavailable/draft until delivery capability, supplier dependencies, warranty, and support terms are confirmed. Those commercial confirmations are required before publishing/selling offers, not a reason to block the initial schema design.

If an item is unknown, record the assumption, its risk, and when it must be resolved. Do not silently treat an assumption as a verified fact.

## C. Required before the public website launches

- [ ] Verify company/legal and public contact information; obtain appropriate professional advice on Egyptian registration, tax, privacy, and any relevant permissions.
- [ ] Approve logo, final color/typography system, and public copy.
- [ ] Confirm domain, hosting, DNS, mail, storage, backups, monitoring, and deployment/rollback process.
- [ ] Confirm which service offers can be sold and their scope, supplier dependencies, warranty, and support terms.
- [ ] Review Arabic and English content.
- [ ] Test inquiry forms, mail notifications, rate limits, validation, and privacy wording.
- [ ] Validate responsive behavior, themes, RTL/LTR, keyboard use, reduced motion, SEO metadata, sitemap content URLs, robots, canonical/hreflang, social previews, and performance.
- [ ] Publish only authorized projects and substantiated customer claims.
- [ ] Keep empty or unready product/template catalogs unpublished rather than filling them with fabricated content.

## D. Additional gate before selling a product or template online

- [ ] Confirm the actual item exists and can be supported.
- [ ] Define license, price, delivery, updates, prerequisites, support limits, refunds/cancellation, and rights to included third-party assets.
- [ ] Test the full buyer journey and delivery.
- [ ] Add online payment only after provider configuration, accounting/tax treatment, failure handling, security, refunds, and support processes are defined and tested.

## E. Agreed build order

1. [x] Audit the scaffold and establish a passing CI baseline on PHP 8.4 / Node 24.
2. [x] Implement and test the secure admin access/provisioning foundation.
3. [x] Add and test the first ServiceLine/Service models and English/Arabic publication scopes.
4. [x] Add and test service CRUD screens with status/locale validation.
5. [x] Add and test project/case-study CRUD with project-stage labels and authorization checks.
6. [x] Add Products and Website Templates admin catalogs with explicit commercial publication gates.
7. [x] Add Insights/category admin with bilingual content and publication guards.
8. [x] Add and test media cover uploads, bilingual contact form with honeypot/rate limits, and dynamic sitemap/robots endpoints (public content URLs remain pending until public pages exist).
9. Continue the **backend first** with email notification settings after the recipient is confirmed.
9. Verify each backend milestone and record its results before relying on it.
10. Build the public Blade frontend section by section using tested backend workflows.
11. Show public portfolio/catalog/editorial sections only when accurate content and terms are ready.
12. Run release checks before deployment.

This is a staged build, not a demand to implement payments, customer accounts, a full CRM, or an advanced page builder.

## Definition of “planning complete enough to start”

Planning is ready for the first backend milestone when section A is accepted, section B is reviewed, unknowns are written down, the first milestone has a bounded scope, and its acceptance criteria are testable. Sections C and D are launch/sales gates; they do not all need to be finalized before coding begins.
