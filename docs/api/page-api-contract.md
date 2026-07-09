# Page API contract

Wave 9 freezes the first RC-facing Page API/output surface. The contract remains intentionally small and explicit so host applications, Backofficing, Interfacing, smoke scripts, and documentation can all reason about the same endpoint set.

## Stable endpoint groups

### Public render

- `GET /page/` renders the public Page index surface.
- `GET /page/{slug}` renders the published Page revision using the standalone Twig fallback template.

### Read and bridge

- `GET /api/page/{code}` returns the published Page render view as JSON.
- `GET /api/page/bridge/{code}` returns `PageBridgePayload` for Interfacing/host bridge rendering.

### Export

- `GET /api/page/export/{code}?format=html`
- `GET /api/page/export/{code}?format=json`
- `GET /api/page/export/{code}?format=md`

The export surface is for Page content distribution. It is not a CMS theme system and does not own SEO, locale, or attachment storage.

### Authoring, revision, publication

- `POST /api/page/authoring/page`
- `PATCH /api/page/authoring/page/{code}`
- `GET /api/page/revision/{code}`
- `POST /api/page/revision/{code}`
- `GET /api/page/publication/{code}`
- `POST /api/page/publication/revision/{revisionNumber}?code={code}`

Revision creation must not mutate already published revision history.

### Legal acceptance

- `POST /api/page/acceptance/revision/{revisionNumber}?code={code}` records acceptance against a concrete revision/checksum.
- `GET /api/page/acceptance/subject/{subjectUserId}?code={code}&revisionNumber={revisionNumber}` checks acceptance for a concrete revision/checksum.

## Runtime check

```bash
php bin/console page:api:contract
```

The command prints the same endpoint registry exposed through `PageApiContractServiceInterface`.
