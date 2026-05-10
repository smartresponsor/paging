# Page API contract

Wave 9 freezes the first RC-facing Page API/output surface. The contract remains intentionally small and explicit so host applications, Backofficing, Interfacing, smoke scripts, and documentation can all reason about the same endpoint set.

## Stable endpoint groups

### Public render

- `GET /pages/{slug}` renders the published Page revision using the standalone Twig fallback template.

### Read and bridge

- `GET /api/page/pages/{code}` returns the published Page render view as JSON.
- `GET /api/page/pages/{code}/bridge` returns `PageBridgePayload` for Interfacing/host bridge rendering.

### Export

- `GET /api/page/export/{code}.html`
- `GET /api/page/export/{code}.json`
- `GET /api/page/export/{code}.md`

The export surface is for Page content distribution. It is not a CMS theme system and does not own SEO, locale, or attachment storage.

### Authoring, revision, publication

- `POST /api/page/authoring/pages`
- `PATCH /api/page/authoring/pages/{code}`
- `GET /api/page/pages/{code}/revisions`
- `POST /api/page/pages/{code}/revisions`
- `GET /api/page/pages/{code}/publications`
- `POST /api/page/pages/{code}/publications/revision/{revisionNumber}`

Revision creation must not mutate already published revision history.

### Legal acceptance

- `POST /api/page/pages/{code}/acceptances/revision/{revisionNumber}` records acceptance against a concrete revision/checksum.

## Runtime check

```bash
php bin/console page:api:contract
```

The command prints the same endpoint registry exposed through `PageApiContractServiceInterface`.
