# Security & Privacy

## Baseline rules

- Never commit `.env` files, passwords, private keys, access tokens, payment secrets, backups, or customer exports.
- Use least-privilege accounts and unique credentials; enable multi-factor authentication on critical services.
- Separate development, staging, and production credentials and data.
- Keep dependencies and supported runtimes updated through a reviewed process.
- Validate inputs, authorize actions server-side, protect against common web risks, and rate-limit sensitive endpoints where appropriate.
- Avoid logging passwords, session tokens, payment data, private documents, or unnecessary personal information.
- Encrypt traffic in transit and protect backups according to sensitivity.
- Limit access to production and audit who can change it.

## Data inventory

For each system, document:
- Data categories and purpose.
- Whether it includes personal, financial, confidential, or regulated information.
- Collection source and lawful basis/consent where applicable.
- Storage location and access roles.
- Processors and third-party transfers.
- Retention period and deletion method.
- Backup and recovery needs.

Collect the minimum data needed to deliver the service.

## Access and secrets

- Store secrets in the hosting/provider secret manager or protected environment configuration.
- Rotate credentials after suspected exposure, access changes, or applicable incidents.
- Revoke access promptly when a collaborator or vendor no longer needs it.
- Never paste production secrets into chat, tickets, source files, or screenshots.

## Backups and incident response

Define a backup cadence based on recovery needs, keep an access-controlled copy, and periodically test restoration. A backup that has never been restored is not a verified recovery plan.

For an incident:
1. Contain the exposure or affected system.
2. Preserve relevant evidence and timestamps.
3. Identify data, accounts, and customers potentially affected.
4. Rotate credentials and restore from a known-good state as appropriate.
5. Notify the right internal/customer/legal contacts according to the contract and applicable law.
6. Document root cause, remediation, and follow-up tests.

## Privacy obligations

Review the applicable Egyptian data-protection rules and the laws of markets in which customers or data subjects are located. Contracts should define controller/processor responsibilities, security obligations, subprocessors, retention, deletion, and breach handling. Obtain professional advice for actual processing activities and cross-border transfers.

This file is a baseline checklist, not a claim of legal compliance or a security certification.
