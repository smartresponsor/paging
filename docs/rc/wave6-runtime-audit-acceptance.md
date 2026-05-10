# Paging Wave 6 — Runtime audit and acceptance baseline

Wave 6 adds the first legal acceptance baseline for governed pages. The component can now record that a subject accepted a specific page revision and checksum.

## Added

- `PageAcceptance` entity and repository.
- `PageAcceptanceInput` and `PageAcceptanceView` DTOs.
- `PageAcceptanceServiceInterface` and implementation.
- `PageAcceptanceController` API endpoints.
- `page:audit:readiness` runtime command.
- Initial Doctrine migration for the Page table family.
- API examples for authoring, publishing, exporting, and accepting legal pages.

## Responsibility boundary

Paging records acceptance against a page revision and checksum. It does not own identity, role hierarchy, global security policy, locale ownership, attachment storage, SEO ownership, or EasyAdmin screens.
