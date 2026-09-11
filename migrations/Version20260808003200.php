<?php

declare(strict_types=1);

namespace App\Paging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260808003200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Materialize Objecting object fields on page and canonical audit fields on page_grant.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on PostgreSQL.');

        $this->addSql('ALTER TABLE page ADD object_uuid BYTEA DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD object_slug VARCHAR(190) DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD object_first_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD object_middle_title TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD object_last_title TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD object_created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD object_modified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD object_created_by VARCHAR(190) DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD object_modified_by VARCHAR(190) DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD object_active BOOLEAN DEFAULT TRUE NOT NULL');
        $this->addSql('ALTER TABLE page ADD object_enabled BOOLEAN DEFAULT TRUE NOT NULL');
        $this->addSql('ALTER TABLE page ADD object_status VARCHAR(64) DEFAULT NULL');
        $this->addSql("UPDATE page SET object_uuid = decode(md5('paging:page:' || id::text), 'hex'), object_slug = slug, object_first_title = title, object_created_at = created_at, object_modified_at = updated_at, object_created_by = owner_user_id, object_modified_by = owner_user_id, object_active = CASE WHEN status = 'archived' THEN FALSE ELSE TRUE END, object_status = status");
        $this->addSql('ALTER TABLE page ALTER object_uuid SET NOT NULL');
        $this->addSql('ALTER TABLE page ALTER object_slug SET NOT NULL');
        $this->addSql('ALTER TABLE page ALTER object_created_at SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX page_object_uuid_uq ON page (object_uuid)');
        $this->addSql('CREATE UNIQUE INDEX page_object_slug_uq ON page (object_slug)');
        $this->addSql('ALTER TABLE page DROP created_at');
        $this->addSql('ALTER TABLE page DROP updated_at');

        $this->addSql('ALTER TABLE page_grant ADD object_created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE page_grant ADD object_modified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE page_grant ADD object_created_by VARCHAR(190) DEFAULT NULL');
        $this->addSql('ALTER TABLE page_grant ADD object_modified_by VARCHAR(190) DEFAULT NULL');
        $this->addSql('UPDATE page_grant SET object_created_at = created_at, object_created_by = created_by_user_id');
        $this->addSql('ALTER TABLE page_grant ALTER object_created_at SET NOT NULL');
        $this->addSql('ALTER TABLE page_grant DROP created_at');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on PostgreSQL.');

        $this->addSql('ALTER TABLE page_grant ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('UPDATE page_grant SET created_at = object_created_at');
        $this->addSql('ALTER TABLE page_grant ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE page_grant DROP object_created_at');
        $this->addSql('ALTER TABLE page_grant DROP object_modified_at');
        $this->addSql('ALTER TABLE page_grant DROP object_created_by');
        $this->addSql('ALTER TABLE page_grant DROP object_modified_by');

        $this->addSql('ALTER TABLE page ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('UPDATE page SET created_at = object_created_at, updated_at = COALESCE(object_modified_at, object_created_at)');
        $this->addSql('ALTER TABLE page ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE page ALTER updated_at SET NOT NULL');
        $this->addSql('DROP INDEX page_object_uuid_uq');
        $this->addSql('DROP INDEX page_object_slug_uq');
        foreach (['object_uuid', 'object_slug', 'object_first_title', 'object_middle_title', 'object_last_title', 'object_created_at', 'object_modified_at', 'object_created_by', 'object_modified_by', 'object_active', 'object_enabled', 'object_status'] as $column) {
            $this->addSql(sprintf('ALTER TABLE page DROP %s', $column));
        }
    }
}
