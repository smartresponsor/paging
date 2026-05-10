# Page host integration

Paging is intentionally split into two usable modes:

1. **Standalone runtime** for local debugging, dependency-container inspection, Doctrine mapping checks, smoke tests, fixtures, and direct API probing.
2. **Host-application bundle mode** where a host Symfony application registers `App\Paging\PageBundle` and wires the Page services into the larger Smart Responsor runtime.

The component namespace remains `App\Paging\...`, while the business stem is `Page` and all database/configuration prefixes use `page` / `page_`.

## Host responsibilities

The host application owns:

- global authentication;
- role hierarchy;
- global security policy;
- database connection selection;
- attachment storage implementation;
- Backofficing/EasyAdmin screens;
- Interfacing visual shell and theme rendering.

Paging owns:

- Page entity lifecycle;
- PageRevision lifecycle;
- PagePublication records;
- PageAcceptance records for legal/policy acknowledgement;
- PageAttachmentReference records pointing to the attachment component;
- PageGrant baseline checks;
- HTML/Markdown/JSON export;
- bridge payloads for Interfacing.

## Minimal host bundle registration

```php
return [
    App\Paging\PageBundle::class => ['all' => true],
];
```

## Minimal host configuration

```yaml
page:
  public_route_prefix: /pages
  api_route_prefix: /api/page
  revision_lock_after_publish: true
  standalone_runtime: false
```

## Recommended host verification

```powershell
php bin/console page:host:integration-check
php bin/console page:rc:readiness
php bin/console page:audit:readiness
php bin/console lint:container
```

The smoke wrapper is available at:

```powershell
powershell -ExecutionPolicy Bypass -File tools/smoke/page-host-integration-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
```
