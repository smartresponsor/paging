# Paging Wave 10 Operational Handoff

Wave 10 closes the post-RC operational layer for the Paging component. It does not add CMS scope, EasyAdmin screens, theme ownership, locale ownership, attachment storage, SEO ownership, or host role hierarchy.

## Component boundary

Paging owns governed pages and their lifecycle:

- `Page` as the main business entity.
- `PageRevision` for immutable content history.
- `PagePublication` for published/effective state.
- `PageAcceptance` for legal acceptance of a concrete revision/checksum.
- `PageAttachmentReference` for external attachment linkage.
- `PageGrant` for local owner/grant checks.
- HTML, Markdown, JSON, and bridge payload output.

The namespace remains `App\Paging\...`; business names, configuration keys, and database tables use the `page` / `page_` stem.

## Host handoff commands

Run these after applying the touched archive:

```powershell
cd D:\PhpstormProjects\www\Paging
composer dump-autoload
php bin/console lint:container
php bin/console page:operations:check
php bin/console page:rc:readiness
php bin/console page:api:contract
php bin/console page:host:integration-check
powershell -ExecutionPolicy Bypass -File tools/smoke/page-operational-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
php bin/phpunit
```

## Backofficing bridge

Backofficing should consume Paging through service interfaces, forms, DTO inputs, and route/API contracts. Backofficing may provide EasyAdmin CRUD controllers, but those controllers should not duplicate Page lifecycle rules.

## Interfacing bridge

Interfacing should consume `PageBridgePayload` and public/API output. Styling, layout, public shell, visual chrome, and templates outside the fallback Twig view remain Interfacing responsibilities.

## Acceptance baseline

Legal/policy pages should store acceptance against `PageAcceptance`, which links the accepted page, revision number, checksum, subject identifier, and acceptance context. This preserves a stable record even after later revisions are published.
