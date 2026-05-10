# Page API examples

## Create page

```http
POST /api/page/authoring/pages
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
POST /api/page/pages/privacy_policy/revisions
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
POST /api/page/pages/privacy_policy/publications/revision/1
Content-Type: application/json

{
  "effectiveFrom": "2026-05-10T00:00:00-05:00",
  "publishedByUserId": "publisher-1"
}
```

## Export published page

```http
GET /api/page/export/privacy_policy.html
GET /api/page/export/privacy_policy.json
GET /api/page/export/privacy_policy.md
```

## Accept legal revision

```http
POST /api/page/pages/privacy_policy/acceptance/revision/1
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
GET /api/page/pages/privacy_policy/acceptance/revision/1/subject/user-123
```
