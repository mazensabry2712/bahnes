# BAHNES IT SOLUTIONS

**Integrated IT services, security systems, and digital solutions for businesses.**

BAHNES is being planned as one company brand with two clear service lines:

1. **IT Infrastructure & Security:** CCTV/surveillance, networking, business IT setup, technical support and maintenance, and related systems where capability and applicable requirements are confirmed.
2. **Software & Digital Solutions:** websites, e-commerce, web/mobile applications, APIs and integrations, business systems such as ERP/CRM/POS, automation, and custom software.

The longer-term plan includes validated software products and website templates. They should be sold only when scope, licensing, support, and buyer demand are understood.

> **Project status:** Early setup. This repository contains a Laravel scaffold and planning documentation. It does not confirm a finished public website, registered legal entity, approved logo, launched product, or production deployment.

## Documentation

Start at the [DOC index](./DOC/README.md). The [Company & Website Workspace](./DOC/16-company-and-website-workspace/README.md) consolidates the agreed service portfolio, site strategy, visual system, CMS, project/product/template catalogs, Insights, lead generation, architecture, and implementation sequence.

## Technology baseline

The chosen planning baseline is **Laravel 13 + Blade + Tailwind CSS 4 + native JavaScript + Vite**, with MySQL as the preferred relational database. Public pages and the custom admin use Blade server rendering; a separate SPA is not required. The repository declares PHP `^8.3`, Laravel `^13.17`, Tailwind CSS `^4.0` and Vite `^8.0`, along with Pest and Laravel Pint dependencies. These constraints do not confirm local runtime, installed dependencies, test results or hosting compatibility.

See the [canonical Website Tech Stack & Frontend Architecture decision](./DOC/09-technology-and-engineering/website-tech-stack.md) and [architecture workspace](./DOC/16-company-and-website-workspace/09-architecture-and-data-model.md). Inertia, React/Vue SPA, TypeScript and unnecessary frontend frameworks are not part of the initial plan.

## Working principles

- Keep one company brand and clearly separate the two service lines in the customer experience.
- Sell defined outcomes and scope rather than vague promises.
- Use the site for qualified inquiries, credible work examples, and a curated digital catalog.
- Distinguish real client work from demos and concepts.
- Build simply and maintainably; do not over-engineer.
- Plan Arabic/English, RTL/LTR, light/dark themes, accessibility, security, SEO, and mobile usability from the beginning.
- Never commit secrets, production credentials, or private customer data.
- Do not claim features are implemented or tested unless they actually are.

The current next step is to audit the Laravel scaffold and test baseline, then implement the backend foundation before building the public site section by section.
