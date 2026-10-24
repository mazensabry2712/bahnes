# 08 — Leads, Sales & Support

## Project Brief Builder

Keep a short general contact form and offer a guided brief for substantial projects. Suggested fields:
- Inquiry type: IT installation/support, website, business system, app, product, template, or other.
- Business/organization name and contact method.
- Short description of the problem/outcome.
- Existing equipment/software/integrations where known.
- Target date and optional budget range.
- Privacy notice and any required consent.
- Source/referrer when appropriate and privacy-safe.

Ask only what helps qualify the inquiry. Never ask for passwords or confidential production data in a public form.

## Implementation status

- **Implemented and CI-verified:** `POST /contact` and localized `POST /en/contact` / `POST /ar/contact` submission endpoints with server-side validation, a five-per-minute throttle, Spatie Honeypot middleware, accepted inquiry types, lowercased email, preferred-language storage and a generated reference shown only after storing.
- **Implemented and CI-verified:** protected admin inbox with search/type/status filters, inquiry review, follow-up status updates and private internal notes.
- **Implemented:** bilingual `/en/contact` and `/ar/contact` Blade form, RTL/LTR layout, real `<x-honeypot />` component, per-field validation messages and stored inquiry reference. **Not yet implemented:** guided Project Brief form, confirmed notification recipient/provider, outbound email delivery/retry/failure handling, and a more detailed next-action/date pipeline.
- **Privacy boundary:** the public request cannot write admin notes or choose its own source/status. Email notifications remain disabled until the company confirms a real recipient and mail configuration.

## Inquiry pipeline

A lightweight admin workflow or spreadsheet is enough at first: New → Reviewed → Qualified → Discovery → Proposal sent → Won/Lost → Delivery → Support/follow-up.

Track next action/date and inquiry source; protect internal notes. Do not build a full CRM before volume requires it.

## Notifications and anti-spam

Validate on the server, rate-limit and use the Spatie Honeypot component/middleware, confirm receipt to the visitor, and notify the responsible company contact only after a real recipient and mail configuration are approved. The Honeypot check and rate limit do not themselves send email.

## Care & Maintenance

Potential recurring support includes agreed updates, health checks, backups where scope permits, small improvements, troubleshooting, and activity summaries. Contracts must specify service hours, response targets, included time, exclusions, emergencies, third-party fees, data responsibilities, and renewal/termination terms. Do not imply 24/7 coverage unless it is staffed.

## Company profile PDF and sales kit

After company identity and contact details are approved, prepare an English and Arabic profile with company intro, two service lines, delivery approach, approved work/examples, support offer, and contact details. Keep source files editable and PDF accessible/updatable. Do not use invented credentials or claims.

Reusable sales assets may include discovery questions, IT site-assessment checklist, requirements brief, proposal/quotation structure, handover checklist, and maintenance plan.

## Client portal decision

Defer login-based project tracking, client portal, and full CRM until the business volume or customer experience justifies their security and maintenance cost.
