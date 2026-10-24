# BAHNES IT SOLUTIONS

**Integrated IT services, security systems, and digital solutions for businesses.**

BAHNES is being planned as one company brand with two clear service lines:

1. **IT Infrastructure & Security:** CCTV/surveillance, networking, business IT setup, technical support and maintenance, and related systems where capability and applicable requirements are confirmed.
2. **Software & Digital Solutions:** websites, e-commerce, web/mobile applications, APIs and integrations, business systems such as ERP/CRM/POS, automation, and custom software.

The longer-term plan includes validated software products and website templates. They should be sold only when scope, licensing, support, and buyer demand are understood.

> **Project status:** Early setup. This repository contains a Laravel application with secure administrator access, bilingual Services and Projects / Case Studies management, Products and Website Templates catalogs, customer inquiry follow-up, and bilingual Insights/category management, alongside planning documentation. It does not confirm a finished public website, registered legal entity, approved logo, launched product, or production deployment.

## Documentation

Start at the [DOC index](./DOC/README.md). The [Company & Website Workspace](./DOC/16-company-and-website-workspace/README.md) consolidates the agreed service portfolio, site strategy, visual system, CMS, project/product/template catalogs, Insights, lead generation, architecture, and implementation sequence.

## Technology baseline

The chosen planning baseline is **Laravel 13 + Blade + Tailwind CSS 4 + TypeScript + Vite**, with MySQL as the preferred relational database. Public pages and the custom admin use Blade server rendering; a separate SPA is not required. The repository baseline is PHP `^8.4`, Laravel `^13.17`, Blade for the public website, Filament 5 for CMS administration, Tailwind CSS `^4.1`, TypeScript and Vite `^8.0`. Selected supporting packages cover media management, dynamic sitemaps, contact-form spam protection and Laravel static analysis. The dependency lock refresh and CI validation are part of the current package setup; local/hosting runtime still needs independent verification.

See the [canonical Website Tech Stack & Frontend Architecture decision](./DOC/09-technology-and-engineering/website-tech-stack.md) and [architecture workspace](./DOC/16-company-and-website-workspace/09-architecture-and-data-model.md). The public site remains server-rendered Laravel Blade; Filament is limited to the admin CMS and uses Livewire internally. TypeScript is limited to browser-side asset modules and does not change the PHP/Laravel backend.

## Working principles

- Keep one company brand and clearly separate the two service lines in the customer experience.
- Sell defined outcomes and scope rather than vague promises.
- Use the site for qualified inquiries, credible work examples, and a curated digital catalog.
- Distinguish real client work from demos and concepts.
- Build simply and maintainably; do not over-engineer.
- Plan Arabic/English, RTL/LTR, light/dark themes, accessibility, security, SEO, and mobile usability from the beginning.
- Never commit secrets, production credentials, or private customer data.
- Do not claim features are implemented or tested unless they actually are.

The initial scaffold audit and CI baseline are complete. Secure administrator access, bilingual service and project/case-study management, Products and Website Templates admin catalogs, and customer inquiry follow-up are implemented and CI-tested. The latest Insights CI run passed 51 tests (193 assertions), TypeScript checking and the Vite production build ([run 40099826855](https://github.com/mazensabry2712/bahnes/actions/runs/40099826855)). Media uploads, public pages and email notification delivery remain separate milestones.

## Package-first engineering

Prefer a maintained, compatible package over writing a homegrown replacement for non-trivial shared capabilities. The project uses Filament for CMS administration, Spatie Media Library for content media, Spatie Sitemap for XML sitemaps, Spatie Honeypot for contact spam protection, Larastan for static analysis, and GSAP/Lucide plus self-hostable font packages for the public frontend. Every dependency must pass compatibility, maintenance, license, security and bundle/runtime checks. Avoid a package for trivial one-off code or where it would add more complexity than it removes. See the [canonical package-selection policy](./DOC/09-technology-and-engineering/website-tech-stack.md).
