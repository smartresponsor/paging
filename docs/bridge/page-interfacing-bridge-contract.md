# Page Interfacing Bridge Contract

Wave 16 canonizes the Page output boundary for visual consumers.

## Boundary

Paging owns the Page lifecycle, revisions, publications, attachment references, legal acceptance metadata, export formats, and the Page bridge payload.

Interfacing owns layout, theme, page shell, widget zones, and the visual rendering discipline.

Interfacing must consume `PageBridgePayload` through `PageBridgeContractProviderInterface` instead of consuming Doctrine `Page` entities directly.

## Provider

```php
App\Paging\ServiceInterface\Bridge\PageBridgeContractProviderInterface
```

Supported entrypoints:

```php
byCode(string $code): PageBridgePayload
bySlug(string $slug): PageBridgePayload
forPublishedPage(Page $page): PageBridgePayload
```

## Payload

```php
App\Paging\DTO\Bridge\PageBridgePayload
```

The payload exposes stable fields for Interfacing:

- `code`
- `slug`
- `title`
- `kind`
- `status`
- `revisionNumber`
- `bodyHtml`
- `bodyText`
- `bodyMarkdown`
- `bodyJson`
- `checksum`
- `attachments`
- `renderHints`
- `legalNotice`
- `publishedAt`
- `effectiveFrom`
- `expiresAt`
- `updatedAt`

## Attachment boundary

Paging only provides `PageBridgeAttachment` references. Attachment storage, URL generation, preview generation, and file lifecycle belong to the Attachment component or host bridge.

## Render hints

`PageBridgeRenderHints` is not a theme system. It only tells Interfacing what the Page business layer expects the visual shell to preserve, such as legal mode, version visibility, and table-of-contents suitability.

## Legal notice

`PageBridgeLegalNotice` provides version/effective-date metadata for policy and rule pages. It allows the Interfacing layer to show legal update information without knowing the Page revision model.
