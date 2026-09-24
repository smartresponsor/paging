<?php

declare(strict_types=1);

namespace App\Paging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260923232500ObjectingIdentityConstraintNames extends AbstractMigration
{
    /**
     * @var array<string, array{canonical: string, legacy: list<string>}>
     */
    private const array INDEXES = [
        'uuid' => [
            'canonical' => 'uniq_page_uuid',
            'legacy' => ['page_uuid_uq', 'uniq_140ab620d17f50a6'],
        ],
        'slug' => [
            'canonical' => 'uniq_page_slug',
            'legacy' => ['page_slug_uniq'],
        ],
    ];

    public function getDescription(): string
    {
        return 'Adopt Objecting-owned deterministic identity unique constraint names for page.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Paging Objecting identity constraint naming supports PostgreSQL only.',
        );

        foreach (self::INDEXES as $column => $definition) {
            $canonical = $definition['canonical'];

            $this->addSql(sprintf(
                <<<'SQL'
DO $$
DECLARE
    existing_legacy text;
    legacy_count integer;
BEGIN
    IF to_regclass('public.%1$s') IS NOT NULL THEN
        RETURN;
    END IF;

    SELECT count(*), min(indexname)
      INTO legacy_count, existing_legacy
      FROM pg_indexes
     WHERE schemaname = 'public'
       AND tablename = 'page'
       AND indexname = ANY (ARRAY[%2$s]);

    IF legacy_count > 1 THEN
        RAISE EXCEPTION 'Multiple legacy page identity indexes exist for %3$s; manual reconciliation is required.';
    ELSIF legacy_count = 1 THEN
        EXECUTE format('ALTER INDEX %%I RENAME TO %1$s', existing_legacy);
    ELSE
        RAISE EXCEPTION 'No legacy unique page identity index exists for %3$s.';
    END IF;
END
$$
SQL,
                $canonical,
                implode(', ', array_map(
                    static fn (string $name): string => "'".str_replace("'", "''", $name)."'",
                    $definition['legacy'],
                )),
                $column,
            ));
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException(
            'Objecting-owned Paging identity constraint naming is intentionally irreversible.',
        );
    }
}

