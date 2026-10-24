# Client Delivery

## Standard engagement lifecycle

1. **Qualify:** Confirm problem, decision-maker, budget, urgency, and fit.
2. **Discover:** Map workflow, users, constraints, risks, existing systems, and acceptance conditions.
3. **Propose:** Document deliverables, exclusions, timeline, dependencies, price, and payment milestones.
4. **Contract and kickoff:** Obtain written approval, required advance payment, and access permissions.
5. **Plan:** Break scope into demonstrable milestones and confirm who signs off.
6. **Build:** Work in version control, demonstrate progress, test continuously, and record decisions.
7. **Accept:** Run agreed acceptance checks; collect explicit customer feedback and sign-off.
8. **Release:** Use a release checklist, backup and rollback plan, and controlled access.
9. **Handover:** Provide user guidance, admin access transfer, deployment notes, known limitations, and agreed source-code/licensing information.
10. **Support:** Start only the support/warranty terms agreed in writing; offer a separate care plan where appropriate.

## Customer responsibilities

Define who supplies content, credentials, sample data, access, business rules, feedback, and approvals. Customer delays may change delivery dates. Never ask a customer to send production secrets in a public issue or repository.

## Acceptance

Acceptance should be based on observable criteria, not “looks good.” For each deliverable specify:
- Expected behavior.
- Required role/permission.
- Valid and invalid cases.
- Relevant device/browser requirements.
- Data or integration prerequisites.
- Who tests it and by when.

## Defects vs. changes

A defect is a failure to meet agreed requirements. A new workflow, revised requirement, extra integration, new report, or expanded data migration is a change request and may affect price/date. Record the difference before doing extra work.

## Production release and handover

- [ ] Approved release scope and version.
- [ ] Automated/manual checks passed.
- [ ] Production backup and rollback path confirmed where feasible.
- [ ] Secrets configured outside source control.
- [ ] Migrations and operational steps reviewed.
- [ ] Logging and error monitoring available.
- [ ] Customer acceptance or explicit release authorization recorded.
- [ ] Access and ownership transferred as agreed.
- [ ] Documentation and support contact shared.

## Feedback and referrals

After a successful delivery, ask for feedback and permission to describe the work publicly. A case study must not reveal confidential information and must not claim unverified business outcomes.
