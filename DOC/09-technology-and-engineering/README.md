# Technology & Engineering

## Repository baseline

The initial repository contains a Laravel application scaffold. The current `composer.json` declares:
- PHP constraint: `^8.4`
- Laravel framework constraint: `^13.17`
- Laravel Tinker constraint: `^3.0`
- Pest-based test dependencies

These are dependency constraints in the repository, not confirmation of the installed runtime, deployed version, or production infrastructure. Verify those independently before deployment or architecture decisions.

## Website stack decision

The canonical stack and exclusions for the company website are in the [Website Tech Stack & Frontend Architecture decision](./website-tech-stack.md).

Planning baseline: PHP `^8.4`, Laravel `^13.17`, custom Blade admin and public pages, MySQL, Tailwind CSS `^4.1`, TypeScript, Vite `^8.0`, Pest, Pint and Larastan. Supporting packages cover media uploads/conversions, XML sitemaps, honeypot spam protection, self-hosted fonts and tree-shaken icons. The latest feature CI passed **59 tests (234 assertions)**, security audits, static analysis, type-checking and Vite build ([feature CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104580639); [dependency-lock CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104175812)); local/hosting configuration still needs a separate check.

Inertia, React/Vue SPA, Livewire, Alpine.js and third-party admin-panel packages are excluded from the baseline. Custom Laravel Blade handles the admin; GSAP is available for complex motion only.

## Engineering principles

- Prefer Laravel's established conventions and server-rendered Blade where they meet the requirements.
- **Package-first:** prefer a maintained compatible package over a homegrown replacement for non-trivial shared capabilities; check compatibility, maintenance, advisories, license, bundle size and hosting constraints first. Do not install packages for trivial one-off logic.
- Keep business rules in appropriate application/domain layers rather than scattering them across views.
- Validate input server-side and authorize every sensitive operation.
- Use migrations for database changes and keep schema changes reviewable.
- Keep environment-specific configuration outside source control.
- Make interfaces accessible, responsive, and performant.
- Design for observable failures and safe recovery, not only the happy path.

## Repository workflow

- Work against the agreed branch policy; current repository default branch is `main`.
- Keep commits focused and messages meaningful.
- Avoid destructive history rewrites and unrelated file changes.
- Review diffs before merging or deployment.
- Add or update tests when behavior changes.
- Use code formatting and static checks available in the project.

## Minimum quality checks

Before release, choose relevant checks for the affected change:
- Dependency and configuration validation.
- Automated feature/unit tests.
- Authorization and validation checks.
- Database migration review.
- Asset build and browser checks when frontend assets change.
- Performance checks for high-traffic or data-heavy paths.
- Backup/rollback and smoke tests for production changes.

Do not claim that checks have passed unless they were actually run and their result recorded.

## Architecture decision records

For decisions with long-term consequences, record context, options, decision, trade-offs, and review triggers. Examples include authentication, tenant isolation, payment integrations, file storage, queueing, hosting, and a move to an API/mobile architecture.

## Keep it simple

Do not pre-build a large microservice platform, multi-tenant SaaS architecture, complex design system, or mobile API before the business requirement justifies the cost. Optimize for safe delivery and maintainability.
