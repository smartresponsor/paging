# Page user usability integration

Paging is ready for full user-facing use when the host application connects the
component-owned Page lifecycle to Backofficing/EasyAdmin, host security,
Viewing/Interfacing, and Navigating.

## Component-owned readiness

Paging owns and exposes:

- `Page`, `PageRevision`, `PagePublication`, `PageAttachmentReference`, `PageGrant`, and `PageAcceptance` entities;
- `PageForm`, `PageRevisionForm`, and `PagePublicationForm` form contracts;
- service interfaces for draft creation, revision creation, publication, attachment references, grants, acceptance, editor normalization, and bridge payloads;
- API controllers for authoring, revisions, publications, and acceptance;
- public view routes and bridge payloads for visual consumers;
- `page:user-usability:check` for host-facing readiness evidence.

## Backofficing and EasyAdmin

EasyAdmin CRUD controllers are allowed as the admin UI exception. Paging now exposes a native EasyAdmin operator surface under `/admin/page`. Mutating actions must be service-driven and must not duplicate Page lifecycle rules.

Allowed operator actions:

- create page;
- edit page metadata;
- create revision;
- preview revision;
- publish revision;
- attach external attachment reference;
- view grants;
- view acceptance history.

Every mutating action should call a Paging service interface. Doctrine-direct
entity mutation from an EasyAdmin controller is not the canonical path for Page
lifecycle changes.

## Host security

The host owns authentication, role hierarchy, and global security policy.
Paging exposes `PageGrant`, `PageGrantServiceInterface`, `PageVoter`, and
acceptance records so the host can connect real users and roles to Page access.

## Viewing and Interfacing

Interfacing should consume `PageBridgeContractProviderInterface` and
`PageBridgePayload`. It should not read Doctrine Page entities directly for the
visual shell.

Viewing/Interfacing owns layout, theme, widget zones, and final visual rendering.
Paging owns the stable business payload and published revision metadata.

## Navigating

The host should add navigation entries for the operator and user surfaces:

- Pages;
- Revisions;
- Published pages;
- Policy and legal pages;
- Page grants;
- Acceptance history.

Navigating should describe UI intent only. It should not execute Page lifecycle
actions or duplicate Paging authorization logic.

## Verification
