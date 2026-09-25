# Paging

Paging component for the multi-domain platform.

## Canon

- Component/repository: `Paging`
- Namespace: `App\Paging\...`
- Business stem/prefix: `Page`
- Database table prefix: `page_`
- Configuration prefix/root: `page`
- Runtime target: PHP `>=8.4`, Symfony `^8.0`
- Architecture: Symfony-oriented, Doctrine-first, Entity-first
- No `/src/Domain`, no Port/Adapter pattern
- Standalone Page component: no Doctrine or runtime coupling to Cataloging or any other business component
- EasyAdmin is allowed as the component-owned operator UI
- Cruding owns generic CRUD route processing
- Objecting supplies reusable system field-pack vocabulary

## Wave 1 scope

This baseline provides:

- standalone Symfony runtime skeleton;
- bundle entrypoint through `PageBundle`;
- `page` configuration extension;
- Doctrine mapping baseline for future `Entity/Page*` classes;
- service autowiring baseline;
- runtime probe service and debug command;
- health endpoint at `/_page/health`.

## Local debug

```bash
composer install
php bin/console page:debug:container
php -S 127.0.0.1:8000 -t public
```

Then open `/_page/health`.


## Wave 2 scope

This wave adds the Doctrine entity-first page model:

- `Page`
- `PageRevision`
- `PagePublication`
- `PageAttachmentReference`
- `PageGrant`
- `Page*` enums, value objects, and repositories

The component namespace remains `App\Paging\...`, while business naming uses the `Page` stem and database tables use the `page` / `page_` prefix.


## Wave 5 RC hardening

Paging now includes the first RC-oriented hardening layer:

- `PageVoter` for `PAGE_VIEW`, `PAGE_EDIT`, `PAGE_PUBLISH`, and `PAGE_MANAGE`.
- Local `PageGrant` checks without owning host role hierarchy.
- Editor payload normalizer and conservative HTML sanitizer.
- Symfony forms for Page, PageRevision, and PagePublication DTO boundaries.
- `page:seed:demo` command for standalone debug/demo pages.

Paging includes EasyAdmin as its operator-facing administration surface. The EasyAdmin controllers remain thin and delegate Page lifecycle behavior to Paging services. Generic CRUD HTTP processing belongs to Cruding.

## Host integration checks

Paging can be verified as a standalone runtime or as a host-application bundle surface.

```bash
php bin/console page:host:integration-check
php bin/console page:rc:readiness
php bin/console page:audit:readiness
```

Windows PowerShell smoke wrapper:

```powershell
powershell -ExecutionPolicy Bypass -File tools/smoke/page-host-integration-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
```

Paging embeds EasyAdmin for operator workflows. External back-office hosts may reuse Page forms/services/DTOs, while Interfacing consumes Page bridge payloads.

## Wave 9 API contract stabilization

Paging exposes a small explicit Page API/output contract for host applications and bridge layers:

```bash
php bin/console page:api:contract
powershell -ExecutionPolicy Bypass -File tools/smoke/page-api-contract-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
```

The contract keeps the component focused on Page business functionality: pages, revisions, publications, exports, bridge payloads, attachment references, local grants, and legal acceptance. EasyAdmin remains outside Paging in the Backofficing layer.

## Wave 10 operational handoff

Paging exposes a final post-RC operational check command for standalone and host debugging:

```bash
php bin/console page:operations:check
```

On Windows, the PS5-safe smoke script can be run with:

```powershell
powershell -ExecutionPolicy Bypass -File tools/smoke/page-operational-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
```

The component remains bounded to Page business lifecycle. EasyAdmin belongs to Backofficing, visual shell belongs to Interfacing, attachments belong to the Attachment component, locale belongs to the Locale component, and host role hierarchy belongs to the host security layer.

## Wave 11 final RC validation

Wave 11 adds the final RC status surface for the Paging/Page component.

```powershell
php bin/console page:rc:final-status
php bin/console page:rc:final-status --json
powershell -ExecutionPolicy Bypass -File tools/smoke/page-final-rc-smoke.ps1 -ProjectRoot "D:\PhpstormProjects\www\Paging"
```

This closes the first component-owned RC baseline. EasyAdmin/Backofficing and Interfacing remain separate integration layers.

## Wave 12 — post-RC handoff

Paging exposes a final handoff summary for the next integration layer:

```bash
php bin/console page:handoff:summary
php bin/console page:handoff:summary --json
composer page:handoff-check
```

The handoff confirms the fixed component boundary: Paging owns Page business lifecycle and bridge payloads; Backofficing owns EasyAdmin/operator screens; Interfacing owns visual shell rendering.

## Wave 13 — Canon guard

Wave 13 adds the final Page/Paging canon guard:

```bash
php bin/console page:canon:guard
php bin/console page:canon:guard --json
composer page:canon-check
```

The guard verifies the RC naming/boundary baseline: `App\Paging\...` namespace, `Page` business prefix, `page`/`page_` database names, and no ownership drift into EasyAdmin, SEO, locale, or attachment storage.

## Wave 14 final completion marker

Wave 14 adds the aggregate `page:completion:status` command and `page:completion-check` Composer script. This is the final RC marker before Paging is consumed by Backofficing/EasyAdmin or Interfacing bridges. The component remains scoped to Page lifecycle and does not own CMS, SEO, locale policy, attachment storage, or host role hierarchy.



## Wave 15 release stamp

Wave 15 adds the final RC release stamp for transfer to Backofficing/Interfacing work:

```bash
php bin/console page:release:stamp
php bin/console page:release:stamp --json
composer page:release-check
```

The release stamp keeps the final canon explicit: `App\Paging\...` namespace, `Page` business prefix, `page` config root, and `page_` database table prefix.


## Wave 16 — Page Bridge Contract Canonization

Paging now exposes an explicit `Bridge` namespace for Interfacing consumers:

- `App\Paging\DTO\Bridge\PageBridgePayload`
- `App\Paging\ProviderInterface\Bridge\PageBridgeContractProviderInterface`
- `App\Paging\FactoryInterface\Bridge\PageBridgePayloadFactoryInterface`

Interfacing should consume bridge payloads and must not use Doctrine `Page` entities as the visual rendering boundary.

Check the bridge contract:

```bash
php bin/console page:bridge:contract
php bin/console page:bridge:contract --json
composer page:bridge-check
```
