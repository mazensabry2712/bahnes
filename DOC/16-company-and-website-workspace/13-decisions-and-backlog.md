# 13 — Decisions & Backlog

This document separates the established planning direction from proposals and details that must still be confirmed. “Planned” does not mean implemented.

## Working direction to preserve

| Topic | Direction | Status |
|---|---|---|
| Company presentation | One BAHNES IT SOLUTIONS brand with two clear service lines | Agreed planning direction |
| Service line A | IT Infrastructure & Security: cameras, networking, business IT, support | Portfolio direction; capability needs validation |
| Service line B | Software & Digital Solutions: websites, systems, apps, integration | Portfolio direction; capability needs validation |
| App framework | Existing Laravel scaffold | Exists; must be audited |
| Public rendering | Laravel Blade / server-rendered | Planned baseline |
| Separate SPA | Not required by current plan | Out of initial scope |
| Languages | Arabic and English | Planned requirement |
| Themes | Proper light and dark themes with user preference | Planned requirement |
| Public service pages | Both service lines, with detailed pages for approved offers | Planned scope |
| Work portfolio | Projects/case studies separate from products | Planned requirement |
| Digital catalog | Products and website templates are separate types | Planned requirement |
| Editorial | Insights/articles | Planned content section |
| CMS | Manage core content, media, locales, SEO, and inquiries | Planned scope |
| Sales flow | General contact plus guided project brief and simple pipeline | Planned scope |
| Company profile PDF | Bilingual, after company facts and identity are approved | Later deliverable |
| Interactive system showcase | Lightweight, clearly illustrative | Optional after core site |
| Client portal/full CRM | Defer until need is demonstrated | Deferred |
| Automated checkout/subscriptions | Defer until commercial flow is defined and tested | Deferred |
| Logo/colors/fonts/tagline | Proposed identity direction | Needs review/approval |

## Decisions required before backend implementation

1. Confirm the service categories that can be delivered directly versus via suppliers/partners.
2. Confirm the initial contact destination/owner and required inquiry types.
3. Audit the scaffold and confirm PHP/Composer/Node runtime, current tests, existing routes, and storage/database assumptions.
4. Choose a simple, testable approach for locale URLs and translated content.
5. Define the first admin account/provisioning method and authorization boundaries.
6. Confirm how inquiries are notified and stored, plus privacy/retention assumptions.
7. Select the initial CMS sections and models from the documented content types; defer anything not needed for launch.

## Decisions that can wait until public launch or catalog release

- Final logo and all production brand assets.
- Public domain, final company contacts, and approved company profile copy.
- Final prices and detailed service packages.
- Actual product/template inventory, licenses, prices, delivery/update/support policy.
- Portfolio assets and client permissions.
- Exact privacy/legal copy reviewed by qualified advisers.
- Production hosting, mail provider, backups, analytics, monitoring, and DNS details.

A visual concept can be drafted before the final logo, but launch must use approved assets and accurate company information.

## Prioritization

Prioritize correctness and security, core business flows, accessibility/usability, content truthfulness, performance, then optional polish. Every feature should have a user/business reason, scope, acceptance criteria, dependencies, and a test.

The interactive showcase is optional. A reliable contact flow, clear service pages, proper content management, and correct metadata are more important for the first release.
