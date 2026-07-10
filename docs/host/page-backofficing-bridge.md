# Page Backofficing bridge contract

Paging allows EasyAdmin as the admin UI exception. Backofficing and EasyAdmin should consume Paging through forms, services, and DTO contracts.

## Backofficing may use

- `PageForm`
- `PageRevisionForm`
- `PagePublicationForm`
- `PageDraftServiceInterface`
- `PageRevisionServiceInterface`
- `PagePublicationServiceInterface`
- `PageAttachmentReferenceServiceInterface`
- `PageGrantServiceInterface`
- `PageEditorPayloadNormalizerInterface`

## Backofficing must not own

- page version numbering;
- revision checksums;
- publication records;
- legal acceptance records;
- page attachment references;
- public bridge payload construction.

## Suggested EasyAdmin actions

- Create Page
- Save Page metadata
- Create Revision
- Preview Revision
- Publish Revision
- Attach external Attachment reference
- View acceptance history
- View grants

Every action should call a Paging service interface rather than mutating Doctrine entities directly from an EasyAdmin controller. Generic business CRUD routes remain the responsibility of Cruding; EasyAdmin is only the operator UI exception.
