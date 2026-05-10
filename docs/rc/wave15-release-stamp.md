# Wave 15 — Release stamp

Wave 15 adds the final Paging/Page RC release stamp surface.

The release stamp is a machine-readable closure report for transferring the component to host integration work. It does not add CMS, SEO, EasyAdmin, locale ownership, attachment storage, role hierarchy, or theme ownership.

## Commands

```bash
php bin/console page:release:stamp
php bin/console page:release:stamp --json
composer page:release-check
```

## Fixed canon

- Component/repository: `Paging`
- Namespace: `App\Paging\...`
- Business prefix/stem: `Page`
- Database table prefix: `page_`
- Config root: `page`

## Ownership boundary

Paging owns Page lifecycle, revisions, publications, legal acceptance, attachment references, local grants, export contracts, and bridge payloads.

Backofficing owns EasyAdmin/operator screens. Interfacing owns visual shell rendering. Attachment owns file storage. Locale owns locale policy. Host applications own global security role hierarchy.
