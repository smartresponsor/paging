<?php

declare(strict_types=1);

namespace App\Paging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260730053500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Use human-readable URL slugs for Paging pages.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on PostgreSQL.');

        $this->addSql('ALTER TABLE page ALTER slug TYPE VARCHAR(128)');
        $this->addSql("UPDATE page SET slug = 'privacy-policy' WHERE code = 'privacy_policy'");
        $this->addSql("UPDATE page SET slug = 'terms-of-service' WHERE code = 'terms_of_service'");
        $this->addSql("UPDATE page SET slug = 'about' WHERE code = 'about'");
        $this->addSql("UPDATE page SET slug = 'sample-blog' WHERE code = 'sample_blog'");
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on PostgreSQL.');

        $this->addSql('ALTER TABLE page ALTER slug TYPE VARCHAR(36)');
    }
}
