# Page full user workflow acceptance

Paging exposes the full host workflow readiness contract through:

```powershell
php bin/console page:workflow:acceptance
```

The scenario covers the complete product path that a host application must wire:

1. create a Page draft;
2. create a Page revision;
3. publish the revision;
4. view the public Page route;
5. render the Page through the Interfacing bridge;
6. reach the operator surface through Navigating;
7. authorize access through host security;
8. expose the consolidated user usability report.
