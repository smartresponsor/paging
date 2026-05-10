# Page response shapes

These shapes are the RC baseline for consumers. They are documentation contracts, not generated OpenAPI output.

## Render view JSON

```json
{
  "code": "privacy_policy",
  "slug": "privacy-policy",
  "title": "Privacy Policy",
  "kind": "policy",
  "version": 1,
  "bodyHtml": "<h1>Privacy Policy</h1>",
  "bodyText": "Privacy Policy",
  "checksum": "sha256:...",
  "publishedAt": "2026-05-04T00:00:00+00:00",
  "effectiveFrom": "2026-05-04T00:00:00+00:00"
}
```

## Bridge payload JSON

```json
{
  "code": "privacy_policy",
  "slug": "privacy-policy",
  "title": "Privacy Policy",
  "kind": "policy",
  "version": 1,
  "bodyHtml": "<h1>Privacy Policy</h1>",
  "bodyText": "Privacy Policy",
  "checksum": "sha256:...",
  "attachments": [],
  "renderHints": {
    "showUpdatedAt": true,
    "showVersion": true,
    "preferredTemplateKey": "page.legal"
  }
}
```

## Acceptance JSON

```json
{
  "pageCode": "privacy_policy",
  "revisionNumber": 1,
  "checksum": "sha256:...",
  "subjectUserId": "user-123",
  "acceptedAt": "2026-05-04T00:00:00+00:00"
}
```

Consumers should treat `code`, `revisionNumber`, and `checksum` as the legal trace tuple.
