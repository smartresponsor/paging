# Page security and access integration

Paging exposes a host security contract through:

```powershell
php bin/console page:security:contract
```

The contract describes the component-owned access surface:

- `PageGrant` entity;
- `PageGrantServiceInterface`;
- `PageSecuritySubjectResolverInterface`;
- `PageVoter` attributes;
- `PageGrantType` values.

## Host-owned responsibility

The host owns authentication, role hierarchy, firewalls, user provider, and
global access control. Paging only resolves the current token subject and maps
that subject to Page grants.

## Global roles

- `ROLE_ADMIN`;
- `ROLE_PAGE_ADMIN`.

## Voter attributes
