# Page Interfacing e2e contract

Paging exposes the command below for host and Interfacing verification.

```powershell
php bin/console page:interfacing:contract
```

The contract proves the component-owned side of the visual chain:

- published `Page`;
- `PageBridgeContractProviderInterface`;
- `PageBridgePayload`;
- `PageRenderView`;
- public routes `page_public_index` and `page_public_view`;
- Twig template extending `@Interfacing/base.html.twig`.

## Boundary

Paging owns the published page payload, render hints, legal metadata, and
acceptance metadata. Interfacing owns layout, shell, theme, responsive
composition, visual zones, breadcrumbs, and final presentation.

The host should verify a complete scenario by publishing a revision, opening the
public page, and rendering the payload through the Interfacing shell.
