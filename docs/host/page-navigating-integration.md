# Page Navigating integration

Paging exposes a deterministic navigation contract for the host shell and the
Navigating component.

## Command

```powershell
php bin/console page:navigation:contract
```

The command returns JSON with two compatible forms:

- `items` for host shell menu rendering;
- `navigatingEntitySeeds` for `NavigationItem` seed/import flows.

## Canonical placement

Paging admin/operator links belong in `shell.left.bottom` because they are
admin-only system/operator controls. They require `ROLE_ADMIN` or
`ROLE_SUPER_ADMIN`.

## Entries

- Pages;
- Revisions;
- Publications;
- Page grants;
- Acceptance history.

