# Paging Wave 5 — RC hardening

Wave 5 closes the first release-candidate surface for the Paging component.

## Scope

- Local page security primitives without owning host role hierarchy.
- `PageVoter` attributes for view/edit/publish/manage checks.
- Editor payload contract and conservative fallback HTML sanitizer.
- Symfony Form classes for page, revision, and publication DTOs.
- Standalone demo seeding command for debug/runtime proof.
- Unit tests for sanitizer and baseline grant decisions.

## Responsibility boundary

Paging owns page business lifecycle, revisions, publications, attachment references, bridge payloads, and local page grants.

Paging does not own EasyAdmin, Backofficing composition, host role hierarchy, visual theme shell, SEO ownership, media storage, or locale ownership.

## Runtime commands

```bash
php bin/console page:debug:container
php bin/console page:seed:demo
```

## Naming canon

- Namespace: `App\Paging\...`
- Business stem: `Page`
- Database prefix: `page_` or direct `page` for the root page table
- Config prefix: `page`
