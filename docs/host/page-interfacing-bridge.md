# Page Interfacing bridge contract

Interfacing should render published Page content from `PageBridgePayload` or `PageRenderView` instead of reading Doctrine entities directly.

## Stable output concepts

- `code`
- `slug`
- `title`
- `kind`
- `version`
- `status`
- `bodyHtml`
- `bodyText`
- `attachmentReferences`
- `effectiveFrom`
- `publishedAt`
- `checksum`
- `renderHints`

## Boundary rule

Paging can provide content and rendering hints. Interfacing owns layout, theme, shell, visual zones, responsive behavior, breadcrumbs, and UI composition.

This keeps Page as a business object while allowing Interfacing to control the visual contract.
