# 15 — Pre-Build Readiness Gate

This is the planning checkpoint before implementation. It is not a demand to finalize every business detail before writing any code. It separates what must be clear to start the backend from what can be finalized nearer public launch.

## A. Direction established for planning

These items describe the agreed working direction; implementation details still need review:
- [x] One BAHNES IT SOLUTIONS customer-facing brand.
- [x] Two service lines: IT Infrastructure & Security, and Software & Digital Solutions.
- [x] One website intended to serve both audiences without mixing their service content.
- [x] Laravel + Blade server-rendered pages as the baseline; no SPA requirement.
- [x] Arabic and English; correct RTL/LTR layout.
- [x] Light and dark themes designed as complete experiences.
- [x] Projects/case studies separated from Products and Website Templates.
- [x] Insights/articles as a useful content section.
- [x] Admin management for core content, media, translations, metadata, and inquiries.
- [x] Security, accessibility, performance, SEO, and test coverage are part of the plan.
- [x] Build in stages; keep client portal, full CRM, automated checkout, and heavy 3D out of the initial scope unless justified.

These checkmarks mean the planning direction is established, not that code or content exists.

## B. Required before starting backend implementation

Resolve or document the initial assumption for each:
- [ ] **Service availability:** Which IT and software offers can be delivered now, and which are future/partner-dependent? The backend can support future services, but public status must reflect reality.
- [ ] **Inquiry ownership:** Where new inquiries should be sent, who receives them, and which inquiry types need to be distinguished.
- [ ] **Runtime baseline:** Audit current PHP/Composer/Node versions, lockfiles, configuration, routes, views, models, migrations, and tests before changing architecture.
- [ ] **Admin access:** Decide how the first administrator is provisioned. No public admin registration by default.
- [ ] **Localization approach:** Decide route/default-locale and content-translation structure after checking the scaffold.
- [ ] **Storage and mail assumptions:** Record local/development storage and email delivery assumptions; keep real secrets outside Git.
- [ ] **Initial content types:** Confirm the minimal first schema for services, pages/sections, projects, products, templates, Insights, media, inquiries, and locale/SEO fields. Keep future capabilities as backlog, not required tables without a use case.
- [ ] **Acceptance plan:** Establish baseline tests and define the first backend milestone's pass criteria.

If an item is unknown, record a safe temporary assumption and its impact rather than silently pretending it is settled.

## C. Required before public launch

- [ ] Verify company name/legal and contact information; obtain suitable professional advice on Egyptian registration, tax, and privacy obligations.
- [ ] Approve logo, color/typography system, and final public copy.
- [ ] Confirm the domain, hosting, DNS, mail, storage, backups, and deployment/rollback process.
- [ ] Confirm which services are available and finalize scope, supplier dependencies, warranty, and support terms.
- [ ] Review Arabic and English content.
- [ ] Test contact/project forms, notification delivery, rate limits, and privacy wording.
- [ ] Validate mobile/responsive behavior, themes, RTL/LTR, keyboard access, reduced motion, SEO metadata, sitemap, robots, canonical/hreflang, social previews, and performance.
- [ ] Publish only authorized projects and verified customer claims.
- [ ] Ensure every page and catalog item shown as available is real and current.

## D. Additional gate before selling a product/template online

- [ ] Confirm the actual item exists and is supported.
- [ ] Define license, pricing, delivery, update policy, prerequisites, support boundaries, refunds/cancellation, and ownership of third-party assets.
- [ ] Test the customer journey and delivery process.
- [ ] Add online payment only after provider, accounting/tax treatment, security, failure handling, and refund flows are defined and tested.

## E. First implementation sequence after Gate B

1. Audit and record the actual Laravel scaffold/test baseline.
2. Implement and test admin access, content models, validation, and permissions.
3. Implement content publishing and media handling safely.
4. Implement locale/SEO foundations.
5. Build the shared Blade layout and core pages.
6. Add projects and Insights when real content is available.
7. Add product/template catalogs when real items and terms exist.
8. Run release checks before deployment.

## Definition of “planning complete enough to start”

Planning is ready for the first backend milestone when sections A and B are reviewed, unresolved assumptions are explicit, the first milestone has a bounded scope, and its acceptance criteria are testable. Sections C and D are launch/sales gates and do not all need to be finalized before the first backend work.
