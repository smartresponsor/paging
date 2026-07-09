# Paging entity-first migration retirement

## Scope

This pass retires the Paging schema-first migration source and keeps the component model in Doctrine entity-first form.

## Retired schema-first source

- `Paging/migrations/**`

## Entity-first coverage

The retired migration tables are already represented by current Doctrine entities:

- `page` -> `Page`
- `page_revision` -> `PageRevision`
- `page_publication` -> `PagePublication`
- `page_attachment_reference` -> `PageAttachmentReference`
- `page_grant` -> `PageGrant`
- `page_acceptance` -> `PageAcceptance`

## Metadata reconciled from retired migrations

- `PageGrant::$grant` now maps to the migration column `grant_type`.
- `PageAttachmentReference` uses the canonical retired migration index names for `page_id`, `revision_id`, and `usage`.
- `PageAttachmentReference::$revision` uses `ON DELETE SET NULL`, matching the retired foreign key semantics.
- `PagePublication` includes the migration-owned `revision_id` index.
- `PageRevision::$lockedBy` was already present and covers the later lifecycle migration.
- `Page::$id` already uses an integer generated primary key and `Page::$slug` already uses the normalized 36-character value from the identifier normalization migration.

## Repository contracts

Added repository interfaces for all Paging entities and made concrete repositories implement them:

- `PageRepositoryInterface`
- `PageRevisionRepositoryInterface`
- `PagePublicationRepositoryInterface`
- `PageAttachmentReferenceRepositoryInterface`
- `PageGrantRepositoryInterface`
- `PageAcceptanceRepositoryInterface`

## Objecting decision

No new Objecting embeddables were introduced in this pass. Paging has page/content lifecycle fields rather than a generic reusable business object identity model:

- `createdAt` / `updatedAt` on `Page` are page authoring lifecycle fields.
- `publishedAt`, `effectiveFrom`, `expiresAt` are publication lifecycle fields.
- `acceptedAt` and context hashes are legal acceptance evidence fields.
- `lockedAt` / `lockedBy` are revision locking lifecycle fields.

Adding Objecting traits here would create schema drift rather than retire schema-first state.

## Legacy monolith check

`Entity-src(6).zip` was checked for an older `Page/Paging` monolith. No legacy Paging aggregate with missing relations was present, so this pass only reconciles current entities with retired migrations.
