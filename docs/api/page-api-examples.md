# Page API examples

## Create page

```http
POST /api/page/authoring/page
Content-Type: application/json

{
  "code": "privacy_policy",
  "slug": "privacy-policy",
  "title": "Privacy Policy",
  "kind": "policy",
  "ownerUserId": "owner-1"
}
```

## Create revision

```http
POST /api/page/revision/privacy_policy
Content-Type: application/json

{
  "title": "Privacy Policy",
  "bodyHtml": "<h1>Privacy Policy</h1><p>Initial policy text.</p>",
  "bodyText": "Privacy Policy\nInitial policy text.",
  "bodyMarkdown": "# Privacy Policy\n\nInitial policy text.",
  "changeNote": "Initial legal policy baseline.",
  "createdByUserId": "owner-1"
}
```

## Publish revision

```http
POST /api/page/publication/revision/1?code=privacy_policy
Content-Type: application/json

{
  "effectiveFrom": "2026-05-10T00:00:00-05:00",
  "publishedByUserId": "publisher-1"
}
```

## Export published page

```http
GET /api/page/export/privacy_policy?format=html
GET /api/page/export/privacy_policy?format=json
GET /api/page/export/privacy_policy?format=md
```

## Accept legal revision

```http
POST /api/page/acceptance/revision/1?code=privacy_policy
Content-Type: application/json

{
  "subjectUserId": "user-123",
  "acceptanceContext": {
    "surface": "registration",
    "checkboxLabel": "I accept the Privacy Policy"
  }
}
```

## Check acceptance

```http
GET /api/page/acceptance/subject/user-123?code=privacy_policy&revisionNumber=1
```
