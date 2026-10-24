# 11 — Security, Testing & Release

## Security baseline

- Keep credentials and production configuration outside Git.
- Protect admin actions with authentication and server-side authorization.
- Validate and normalize inputs server-side; follow Laravel CSRF/session conventions.
- Rate-limit inquiry forms and sensitive actions.
- Validate file content/type/size and storage paths; prevent executable uploads. Current content cover uploads accept JPG/PNG/WebP up to 5 MiB, use generated filenames, keep one cover per record and attach localized alt text. Configure the public storage link, persistent storage/backups and PHP image extensions on the host.
- Escape output and sanitize any permitted rich text.
- Protect inquiry records and keep personal/confidential information out of logs.
- Avoid exposing stack traces, secret values, private paths, or admin content publicly.
- Review dependencies and supported runtimes.
- Define backups and verify restoration when business-critical.

This checklist does not itself establish legal compliance or security certification.

## Backend tests

Test admin access and authorization, content create/edit/publish/archive, upload type/size validation and cover removal, draft privacy, inquiry validation/throttling/honeypot rejection, notification failures, locale behavior, and sitemap/robots exclusions.

## Tooling checks

- Use declared Pest dependencies for Laravel tests and Laravel Pint for PHP formatting after confirming installed versions and the baseline.
- Run `npm run build` after frontend changes to verify Vite/Tailwind compilation.
- Browser-check responsive behavior, keyboard navigation, RTL/LTR and both themes. Add browser automation only if its maintenance cost is justified; do not assume Playwright is installed.
- Record actual commands/results; a manifest declaration does not prove a tool works.

## Frontend tests

Test mobile/desktop, navigation, form errors/success, themes, RTL/LTR, keyboard/focus, reduced motion, images/social previews, and broken links.

## Release checklist

- [x] Dependencies install from lockfiles; latest checks passed **59 tests (234 assertions)**, Composer/npm audits, Larastan, TypeScript and Vite build ([feature CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104057081); [dependency-lock CI](https://github.com/mazensabry2712/bahnes/actions/runs/40104175812)).
- [ ] Migrations are reviewed and tested on the target environment.
- [ ] Asset build succeeds.
- [ ] Production debug mode is off and required environment configuration exists.
- [ ] HTTPS, DNS, `storage:link` and storage permissions/persistence, PHP `gd`/`exif`/`fileinfo`, mail delivery, backups, and monitoring are checked on the actual target host.
- [ ] Public pages, admin login, forms, and content publishing pass smoke tests.
- [ ] Deployment and rollback steps are documented.

Do not call a feature ready or a check passed until it has been implemented and evidence is recorded.
