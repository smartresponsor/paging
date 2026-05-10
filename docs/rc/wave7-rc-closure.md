# Paging Wave 7 — RC Closure

Wave 7 closes the first release-candidate baseline for the `Paging` component.

## Scope

- Keeps the component focused on pages, not a full CMS.
- Keeps the namespace as `App\Paging\...`.
- Keeps the business naming stem as `Page` and the database/config prefix as `page` / `page_`.
- Adds a static RC readiness checklist service and command.
- Adds a local smoke script for standalone runtime checks.
- Does not add EasyAdmin, SEO ownership, locale ownership, attachment storage, role hierarchy, themes, or a heavy visual-editor dependency.

## RC Readiness Command

```bash
php bin/console page:rc:readiness
```

Expected result: every checklist row is `passed`.

## Recommended Local Smoke

```powershell
powershell -ExecutionPolicy Bypass -File tools/smoke/page-rc-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
```

The smoke script is intentionally non-destructive. It only runs syntax/container/readiness commands.

## RC Contract

The first RC baseline includes:

- entity-first Page model;
- immutable PageRevision surface;
- PagePublication lifecycle;
- PageAttachmentReference neighbor contract;
- PageGrant and PageVoter baseline;
- PageAcceptance legal/audit baseline;
- HTML/JSON/Markdown output contracts;
- Interfacing bridge payload;
- form contracts for future Backofficing/EasyAdmin integration;
- standalone and bundle-oriented runtime shape.
