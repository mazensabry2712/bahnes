# 12 — Implementation Roadmap

## Phase 0 — Audit and decisions
- Inspect scaffold, Composer dependencies, routes, views, assets, config, and tests.
- Run existing tests and record the baseline.
- Confirm which services are genuinely ready to sell.
- Confirm public contact details and who owns inquiries.
- Track legal, identity, supplier, and privacy questions.

**Exit:** code and unknowns are documented; production readiness is not assumed.

## Phase 1 — Backend foundation
- Select a simple schema for pages/sections, services, projects, products, templates, Insights, media, inquiries, translations, and SEO.
- Implement reviewed migrations, validation, authorization, and admin management.
- Add draft/publish states and protected inquiry management.
- Test permissions and public visibility.
- Confirm media storage safety.

**Exit:** admin can manage core content safely, backed by tests.

## Phase 2 — Public site foundation
- Build Blade layout, navigation, footer, spacing/type foundations.
- Implement locales, RTL/LTR, theme toggle and persistence, and metadata.
- Publish Home, both service lines, service details, About, and Contact.
- Check mobile, keyboard accessibility, forms, SEO, and speed.

**Exit:** core pages work across languages, themes, and screen sizes.

## Phase 3 — Proof and inbound
- Add approved case studies and accurately labelled demos.
- Implement project brief and inquiry pipeline.
- Publish useful Insights when reviewed content exists.
- Create bilingual company profile PDF after identity/contact approval.
- Document maintenance/support terms.

**Exit:** visitors understand services, see honest proof, and can send actionable inquiries.

## Phase 4 — Catalog
- Add products and templates as separate types.
- Provide real screenshots/previews, licensing, prices, and support details when available.
- Test demo and inquiry paths.
- Defer checkout until payment, delivery, refunds, tax/accounting, and support are defined.

## Phase 5 — Release and learn
- Test forms, mail, admin, locales, themes, SEO, media, and performance.
- Review privacy/legal content with appropriate advice.
- Deploy with backup, rollback, and smoke-test plan.
- Track qualified inquiries and service delivery evidence.

## Deferred until justified

Client portal, full CRM, automated payments/licensing/subscriptions, complex page builder, multi-tenant/microservice architecture, heavy 3D, and excessive animation.
