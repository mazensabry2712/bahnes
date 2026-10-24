# 05 — Content Model & Admin

## Goal

Let an authorized admin manage routine site content without editing code. Start with known content types; do not build a generic drag-and-drop page builder.

## Management areas

1. **Pages and home sections:** predefined section types, order, visibility, copy, media, and locale content.
2. **Services:** service line, detail page, scope, dependencies, FAQs, related work, CTA.
3. **Projects / Case Studies:** status, problem, approach, role, deliverables, approved images, disclosure permissions, SEO.
4. **Products:** buyer/problem, features, limitations, screenshots/demo, prerequisites, license/pricing/support/update details when approved, status, SEO.
5. **Website Templates:** category, included pages/files, preview images/URL, license, price if approved, dependencies, customization and support terms.
6. **Insights:** article, category/tags, summary/body, cover, status/publication date, related articles and SEO.
7. **Media:** safe upload, metadata, useful alt text, storage rules.
8. **Contact and sales inquiries:** type, status, source, next action and protected internal notes.
9. **SEO and translations:** per-language slugs/content/titles/descriptions, canonical/social metadata as needed.
10. **Shared settings:** approved company contacts, navigation, footer, and brand assets.

## Implementation status

- **Implemented and CI-verified:** protected admin access and the first Services list/search/status filter/create/edit workflows.
- **Service fields supported:** service line; separate English/Arabic title, slug, summary, description and SEO metadata; draft/published/archived status; display order and optional scheduled publication.
- **Publication safety:** a service needs a complete title/slug pair in at least one locale to publish. It appears in a locale only when that locale has its own complete title/slug, the status is published, the service line is active and any scheduled publication time has arrived.
- **Not yet implemented:** media upload UI, projects, products, website templates, Insights, shared page settings and inquiry management. These remain separate backend milestones.

## Workflow

Use Draft, Published, and Archived where appropriate; saving content should not automatically publish it. Provide preview/review and clear publication state. Projects also need honest status labels: Completed, In Progress, Concept, Confidential.

## Permissions and safety

Require admin authentication and server-side authorization. Validate every write and upload; validate file size/type/content and storage path; prevent executable uploads; escape output and sanitize permitted rich text. Protect inquiry records, avoid sensitive data in logs, paginate long lists, confirm destructive actions, and keep secrets out of Git.

## Translation workflow

Show translation status and allow a locale to remain unpublished until reviewed. Do not present machine-translated copy as professionally reviewed. Each locale needs its own SEO fields.

## Keep it maintainable

Use Laravel conventions and the fewest abstractions that meet real needs. Avoid a complex generic workflow engine, large permission matrix, or arbitrary page builder before volume justifies them.
