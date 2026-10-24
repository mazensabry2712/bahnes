# BAHNES IT SOLUTIONS

**Business software and digital systems for growing businesses.**

BAHNES IT SOLUTIONS is being shaped as a founder-led software services company, with a long-term path toward building its own products after validating real customer problems.

> **Project status:** Early setup. This repository currently contains a Laravel application scaffold and the company's planning/documentation structure. It is not yet evidence of a completed public company website, registered legal entity, or launched SaaS product.

## Documentation

The company and project planning documents are organized under **[`DOC/`](./DOC/README.md)**.

Key sections:

- [Company overview](./DOC/00-company-overview/README.md)
- [Strategy and market](./DOC/01-strategy-and-market/README.md)
- [Brand and identity](./DOC/02-brand-and-identity/README.md)
- [Legal and compliance in Egypt](./DOC/03-legal-and-compliance-egypt/README.md)
- [Services and offers](./DOC/04-services-and-offers/README.md)
- [Sales and marketing](./DOC/05-sales-and-marketing/README.md)
- [Pricing and commercial terms](./DOC/06-pricing-and-commercials/README.md)
- [Client delivery](./DOC/07-client-delivery/README.md)
- [Technology and engineering](./DOC/09-technology-and-engineering/README.md)
- [Security and privacy](./DOC/10-security-and-privacy/README.md)
- [Website and SEO](./DOC/11-website-and-seo/README.md)
- [Products and SaaS discovery](./DOC/13-products-and-saas/README.md)
- [Roadmap and KPIs](./DOC/14-roadmap-and-kpis/README.md)

Start at the [documentation index](./DOC/README.md) for the full directory map.

## Current technology baseline

The initial application scaffold uses:

- PHP `^8.3`
- Laravel Framework `^13.17`
- Laravel Tinker `^3.0`
- Pest for automated tests

These are the dependency constraints declared in `composer.json`, not a guarantee of the installed runtime or any production deployment. Review [Technology & Engineering](./DOC/09-technology-and-engineering/README.md) before making architecture or deployment decisions.

## Development

Install dependencies:

```bash
composer install
```

Create a local environment file and application key:

```bash
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env`, then run migrations as appropriate for your local environment:

```bash
php artisan migrate
```

Run the application using the project's configured development scripts. Check the Laravel documentation for framework-specific setup help: https://laravel.com/docs

## Working principles

- Keep business decisions and verified project facts documented.
- Prefer simple, maintainable implementations over unnecessary complexity.
- Never commit secrets, production credentials, private customer data, or environment files.
- Label estimates and assumptions; verify legal, tax, security, and pricing matters before relying on them.
- Update the relevant documentation when an approved decision or implementation materially changes.

## Status and contributions

This is the early working repository for BAHNES IT SOLUTIONS. Scope, public brand assets, services, and product direction remain subject to validation and approval.
