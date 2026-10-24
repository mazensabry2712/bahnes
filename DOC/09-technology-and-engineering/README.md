# Technology & Engineering

## Repository baseline

The initial repository contains a Laravel application scaffold. The current `composer.json` declares:
- PHP constraint: `^8.3`
- Laravel framework constraint: `^13.17`
- Laravel Tinker constraint: `^3.0`
- Pest-based test dependencies

These are dependency constraints in the repository, not confirmation of the installed runtime, deployed version, or production infrastructure. Verify those independently before deployment or architecture decisions.

## Engineering principles

- Prefer Laravel's established conventions and server-rendered Blade where they meet the requirements.
- Add packages only for a defined need; evaluate maintenance, security, licensing, and long-term cost.
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
