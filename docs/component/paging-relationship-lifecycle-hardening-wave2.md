# Paging relationship/lifecycle hardening wave 2

Status: applied as a conservative hardening pass.

## Scope

- Adds `PageLifecyclePolicy`.
- Keeps lifecycle validation string-based to avoid schema drift.
- Does not touch `*EnGb*` / translation normalization.
- Does not touch Attachment/Attaching mechanics.

## Lifecycle decision

Page revision/publication lifecycle. Revision locks and publication timestamps stay page lifecycle facts.

## Transition map

- `draft` -> `review`, `published`, `archived`
- `review` -> `draft`, `published`, `archived`
- `published` -> `updated`, `withdrawn`, `archived`
- `updated` -> `published`, `withdrawn`, `archived`
- `withdrawn` -> `draft`, `archived`
- `archived` -> `terminal`
