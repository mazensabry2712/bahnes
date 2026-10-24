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
| Backend/runtime | PHP `^8.3` + Laravel `^13.17` | Declared in Composer; runtime audit pending |
| Database | MySQL with Eloquent/migrations | Preferred plan; confirm hosting/environment |
| CSS/build | Tailwind CSS `^4.0` + Vite `^8.0` + Laravel Vite plugin | Declared in package.json; install/build audit pending |
| Client-side behavior | TypeScript modules for small browser interactions | Implemented in the asset pipeline; `tsc --noEmit` is part of the build |
| PHP tests/format | Pest + Laravel Pint | Declared dependencies; baseline run pending |
| Admin UI | Custom Blade admin with session auth and server-side authorization | Initial login/dashboard, admin middleware, CLI creation/reset commands implemented and CI-tested; content admin remains pending |
| Inertia / React / Vue SPA | Not selected for initial website | Revisit only if requirements change |
| Motion libraries | CSS/native features first; GSAP not baseline | Optional only after a concrete need is approved |
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

## Technology decision and review rule

The canonical choice is [Website Tech Stack & Frontend Architecture](../09-technology-and-engineering/website-tech-stack.md): one Laravel app with PHP backend, Blade server rendering, Tailwind CSS 4, TypeScript for browser-side code and Vite; MySQL is preferred. Do not add Inertia, a SPA, Livewire, Alpine.js, GSAP or a packaged admin merely because they are available.

The checked-in manifests and lockfiles were validated, and CI passed on PHP 8.4 / Node 24, including Pest tests, TypeScript type-checking and Vite build. This verifies the GitHub CI environment, not the user's local Herd or deployment host. Update the docs before any technology change.

## Decisions required before backend implementation

1. Confirm the service categories that can be delivered directly versus via suppliers/partners.
2. Confirm the initial contact destination/owner and required inquiry types.
3. [x] Audit the scaffold, lockfiles, routes, migrations and sample tests; establish a green GitHub Actions baseline on PHP 8.4 / Node 24. Local/hosting runtime and storage/database settings still need environment-specific confirmation.
4. [x] Choose the initial translation storage: explicit `_en` and `_ar` columns for the two launch locales, with locale-aware queries and SEO fields; review before adding another locale.
5. [x] Define and implement admin provisioning/recovery with CLI commands and server-side authorization.
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
