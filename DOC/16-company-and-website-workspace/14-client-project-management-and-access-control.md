# Client, Project, Progress and Access-Control System

## Goals

Provide a private, authenticated workspace for managing BAHNES clients and delivery work. This module is internal by default and must not expose client data on public website routes.

## Client record

- Contact name, company, email, phone and preferred communication locale.
- Internal notes, agreed services, commercial summary, and private attachments.
- Created/updated audit trail and the responsible internal owner.
- Do not store payment-card credentials or unnecessary sensitive personal data.

## Project record

- Belongs to a client and has a title, description, reference, status, start date, target date and completion percentage.
- Tracks milestones/tasks, owner, due dates, blockers, delivery notes and decisions.
- Project activity history identifies who changed what and when.
- Progress percentage must be validated (0–100); status transitions are validated server-side.

## Client communication

- Internal notes and client-visible updates are separate fields/records and must never be mixed.
- A client-visible update contains a concise summary, completed work, current work, next step and target date.
- Only an authorized staff member can publish/send a client update.
- Store email attempts and outcomes; avoid duplicate sends when retrying.
- Mail delivery requires configured production mail transport. The log/array mailers are for development/testing only.
- Do not email internal notes, staff-only costs, credentials, or confidential discussions.
- Email content must escape untrusted text and use a branded, accessible template.
- Client portal access is a separate optional phase; never expose records by predictable IDs alone.

## Role and permission model

- Keep a protected Super Admin role.
- Roles are assignable to staff users; permissions are granular by module and action.
- Suggested permissions: clients.view/create/update/archive; projects.view/create/update/archive; project_updates.create/publish; users.manage; roles.manage; cms.services.view/create/update; cms.projects.view/create/update; cms.products.view/create/update; cms.insights.view/create/update; inquiries.view/update.
- Enforce authorization on server routes/actions, not only by hiding navigation links.
- Default deny for new modules. Avoid granting every staff account administrator privileges.
- Record security-sensitive role changes and never allow the last Super Admin to remove their own access.

## Acceptance checks

1. Unauthenticated visitors cannot access client/project data.
2. A user without a module permission receives HTTP 403 even when calling a URL directly.
3. Internal notes are absent from client email and any future client portal.
4. Client-visible updates can be sent only by authorized staff.
5. Email failures are visible and safely retryable without silently losing updates.
6. Tests cover authorization, validation, status/progress bounds, email contents, and duplicate-send behavior.
7. All database changes are additive migrations and must not delete existing CMS/client inquiry data.

## Implementation and release constraints

- Work on `main` only, do not deploy to production without explicit approval.
- Run the complete automated CI suite after implementation.
- Configure and verify SMTP/transactional mail in the deployment environment before claiming real delivery.
- Existing public CMS projects are not automatically converted into client delivery projects; do not infer client contracts from public portfolio entries.


## Backend implementation status

The internal backend now includes:

- Client profile fields for agreed services, commercial summary, account manager, preferred email language, and internal notes.
- Delivery project references (BH-PRJ-000001 format), status/progress validation, completion timestamps, blockers, delivery notes, assigned owner, and soft archiving.
- Project tasks and milestones with assignees, due dates, statuses, completion timestamps, internal notes, and soft archiving.
- Private client document upload/download/delete endpoints. Documents use the private local storage disk, are not placed under public/, and require explicit document permissions.
- Client/project activity history for record changes, task and milestone events, email outcomes, document events, role creation/changes, and staff access assignments.
- A protected, paginated JSON history endpoint for clients and projects, plus task listing for future UI integration.
- Granular permissions for document access, tasks/milestones, and archiving, enforced by route middleware.
- Email delivery state (not_sent, sending, sent, failed), attempt count, safe failure messages, duplicate-send claim protection, and recovery of abandoned sending states after ten minutes.

### Protected backend routes

- POST /admin-panel/clients/{client}/documents
- GET /admin-panel/client-documents/{document}/download
- DELETE /admin-panel/client-documents/{document}
- GET /admin-panel/clients/{client}/activity
- GET /admin-panel/access/activity (Super Admin only)
- GET /admin-panel/delivery-projects/{project}/activity
- GET /admin-panel/delivery-projects/{project}/tasks
- POST /admin-panel/delivery-projects/{project}/tasks
- PUT /admin-panel/project-tasks/{task}
- DELETE /admin-panel/project-tasks/{task}
- DELETE /admin-panel/clients/{client}
- DELETE /admin-panel/delivery-projects/{project}

The backend routes, data models and tests are being completed independently of the admin Blade/UI integration. No client portal has been created; these endpoints are protected for internal staff only.

### Release gates still required

- Run the latest full CI workflow successfully after all current backend changes.
- Verify migrations on the actual target database engine and confirm private storage directory permissions/persistence on the host.
- Configure and verify a real production mail transport before sending real client email.
- Build the admin screens for documents, tasks, activity history, and the new client/project fields only after explicit frontend approval.
