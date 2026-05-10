# Paging Wave 13 — Canon Guard

Wave 13 adds a final repository-hygiene guard for the first Paging release-candidate line.

## Canon fixed by this wave

- Component namespace remains `App\Paging\...`.
- Business language uses `Page` as the class/service/controller/form prefix.
- Database tables use `page` and `page_` names.
- Bundle configuration root remains `page`.
- Paging owns page lifecycle only.
- Backofficing owns EasyAdmin/operator screens.
- Interfacing owns visual shell and rendering integration.
- Attachment owns binary/file storage.
- Locale owns locale policy.

## Command

```bash
php bin/console page:canon:guard
php bin/console page:canon:guard --json
```

## Windows-safe smoke

```powershell
powershell -ExecutionPolicy Bypass -File tools/smoke/page-canon-guard-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
```

This wave intentionally does not add CMS breadth, SEO ownership, EasyAdmin ownership, route tree builders, or locale ownership.
