# Paging Wave 11 — Final RC Validation

Wave 11 closes the first Paging/Page release-candidate baseline with a machine-readable final status command and a single Windows-safe smoke entrypoint.

## Scope

Included:

- `page:rc:final-status` command.
- `PageFinalStatusService` and finalization DTOs.
- Machine-readable `--json` final RC output.
- `tools/smoke/page-final-rc-smoke.ps1`.
- Final generated RC manifest.

Not included:

- EasyAdmin screens.
- Backofficing implementation.
- Interfacing visual themes.
- Locale ownership.
- Attachment storage.
- SEO subsystem.
- Role hierarchy.

## Canon confirmed

- Component namespace: `App\Paging\...`.
- Business stem: `Page`.
- Database tables: `page` / `page_*`.
- Configuration prefix: `page`.
- Core responsibility: governed pages, revisions, publications, exports, bridge payloads, attachment references, and local grants.

## Validation

```powershell
php bin/console page:rc:final-status
php bin/console page:rc:final-status --json
powershell -ExecutionPolicy Bypass -File tools/smoke/page-final-rc-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
```
