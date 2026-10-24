# BAHNES — Company & Website Workspace

**Purpose:** A coordinated workspace to turn the agreed company direction and website ideas into a practical specification. This is planning documentation, not a statement that features have been implemented.

## Direction

BAHNES IT SOLUTIONS is planned as one company brand with two service lines:

1. **IT Infrastructure & Security:** CCTV/surveillance, networking, business IT setup, support and maintenance, and related systems when capability and requirements are confirmed.
2. **Software & Digital Solutions:** websites, e-commerce, web/mobile apps, APIs/integrations, business systems, automation, and validated software products/templates.

The website should generate service inquiries, establish trust through real work and useful content, and present a curated catalog of software products and website templates.

## Workspace map

| Document | Work area |
|---|---|
| [01 — Positioning & Service Lines](./01-positioning-and-service-lines.md) | Company presentation, full service taxonomy |
| [02 — Site Map & User Journeys](./02-site-map-and-user-journeys.md) | Pages, navigation, calls to action |
| [03 — Brand & Visual System](./03-brand-and-visual-system.md) | Logo brief, palette, typography, layout |
| [04 — Motion & Accessibility](./04-motion-and-accessibility.md) | Animation, themes, accessibility, responsiveness |
| [05 — Content Model & Admin](./05-content-model-and-admin.md) | CMS areas, publishing workflow, permissions |
| [06 — Projects, Products & Templates](./06-projects-products-and-templates.md) | Separate catalogs and rules |
| [07 — Insights & Editorial](./07-insights-and-editorial.md) | Articles and editorial standards |
| [08 — Leads, Sales & Support](./08-leads-sales-and-support.md) | Project brief, inquiry pipeline, maintenance, company PDF |
| [09 — Architecture & Data Model](./09-architecture-and-data-model.md) | Laravel/Blade architecture and simple content model |
| [10 — Localization, SEO & Performance](./10-localization-seo-performance.md) | Arabic/English, RTL/LTR, search and speed |
| [11 — Security, Testing & Release](./11-security-testing-and-release.md) | Security baseline and release checks |
| [12 — Implementation Roadmap](./12-implementation-roadmap.md) | Ordered phases and exit criteria |
| [13 — Decisions & Backlog](./13-decisions-and-backlog.md) | Open decisions and deferred items |
| [14 — Interactive Demos & Sales Assets](./14-interactive-demos-and-sales-assets.md) | System showcase, demos, company profile PDF |

## Working rules

- Keep one canonical source for each policy and link to existing DOC sections rather than creating conflicting duplicates.
- Mark items Proposed, Needs confirmation, Approved, Implemented, or Verified when their status matters.
- Do not invent customers, testimonials, metrics, certifications, prices, supplier capabilities, or legal status.
- Verify service readiness and support boundaries before publishing an offer.
- Audit the current Laravel scaffold before implementing; use Blade server-rendered pages as the planned baseline.
- Build the smallest robust content system for real needs. Avoid over-engineering.
- Every major feature needs acceptance criteria and test evidence before it is called complete.

## Status

**Specification workspace established.** The codebase audit, backend implementation, public website, and tests are not completed by this documentation change. Next step: audit the scaffold, record the test baseline, then implement the backend foundation before designing and building public sections incrementally.
