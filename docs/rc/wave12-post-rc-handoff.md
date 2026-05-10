# Wave 12 — Post-RC handoff

Wave 12 closes the first Paging/Page delivery track with a machine-readable handoff summary.

## Fixed canon

- Component/repository: `Paging`
- Namespace: `App\Paging\...`
- Business stem: `Page`
- Database prefix: `page_` with the root `page` table allowed
- Config prefix: `page`

## Owned by Paging

- Page entity-first model
- Page revisions
- Page publications
- Page attachment references
- Page grants and owner checks
- Page acceptance records for legal/policy revisions
- HTML, Markdown, JSON, and bridge payload outputs
- Standalone runtime and bundle mode

## Not owned by Paging

- EasyAdmin CRUD screens: Backofficing
- Visual shell/themes: Interfacing
- Attachment storage: Attachment component
- Locale ownership: Locale component
- Host role hierarchy: host security layer

## Commands

```bash
php bin/console page:handoff:summary
php bin/console page:handoff:summary --json
composer page:handoff-check
```

## Windows smoke

```powershell
powershell -ExecutionPolicy Bypass -File tools/smoke/page-handoff-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
```
