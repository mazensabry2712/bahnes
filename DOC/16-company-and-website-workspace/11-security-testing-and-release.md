# 11 — Security, Testing & Release

## Security baseline

- Keep credentials and production configuration outside Git.
- Protect admin actions with authentication and server-side authorization.
- Validate and normalize inputs server-side; follow Laravel CSRF/session conventions.
- Rate-limit inquiry forms and sensitive actions.
- Validate file content/type/size and storage paths; prevent executable uploads.
- Escape output and sanitize any permitted rich text.
- Protect inquiry records and keep personal/confidential information out of logs.
- Avoid exposing stack traces, secret values, private paths, or admin content publicly.
- Review dependencies and supported runtimes.
- Define backups and verify restoration when business-critical.

This checklist does not itself establish legal compliance or security certification.

## Backend tests

Test admin access and authorization, content create/edit/publish/archive, upload validation, draft privacy, inquiry validation/throttling/notification errors, locale fallback, and SEO metadata/sitemap/hreflang.

## Frontend tests

Test mobile/desktop, navigation, form errors/success, themes, RTL/LTR, keyboard/focus, reduced motion, images/social previews, and broken links.

## Release checklist

- [ ] Dependencies install from lockfile and automated tests pass.
- [ ] Migrations are reviewed and tested on the target environment.
- [ ] Asset build succeeds.
- [ ] Production debug mode is off and required environment configuration exists.
- [ ] HTTPS, DNS, storage permissions, mail delivery, backups, and monitoring are checked.
- [ ] Public pages, admin login, forms, and content publishing pass smoke tests.
- [ ] Deployment and rollback steps are documented.

Do not call a feature ready or a check passed until it has been implemented and evidence is recorded.
