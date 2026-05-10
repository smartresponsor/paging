<?php

declare(strict_types=1);

namespace App\Paging\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504206000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the Paging page tables, including revisions, publications, attachment references, grants, and legal acceptance baseline.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS page (id VARCHAR(32) NOT NULL, current_revision_id VARCHAR(32) DEFAULT NULL, published_revision_id VARCHAR(32) DEFAULT NULL, code VARCHAR(128) NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, kind VARCHAR(32) NOT NULL, status VARCHAR(32) NOT NULL, owner_user_id VARCHAR(128) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS page_code_uniq ON page (code)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS page_slug_uniq ON page (slug)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_kind_idx ON page (kind)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_status_idx ON page (status)');

        $this->addSql('CREATE TABLE IF NOT EXISTS page_revision (id VARCHAR(32) NOT NULL, page_id VARCHAR(32) NOT NULL, revision_number INT NOT NULL, title VARCHAR(255) NOT NULL, body_html TEXT NOT NULL, body_markdown TEXT DEFAULT NULL, body_json JSON DEFAULT NULL, body_text TEXT NOT NULL, change_note TEXT DEFAULT NULL, checksum VARCHAR(64) NOT NULL, created_by_user_id VARCHAR(128) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_revision_page_idx ON page_revision (page_id)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS page_revision_number_uniq ON page_revision (page_id, revision_number)');

        $this->addSql('CREATE TABLE IF NOT EXISTS page_publication (id VARCHAR(32) NOT NULL, page_id VARCHAR(32) NOT NULL, revision_id VARCHAR(32) NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, effective_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, published_by_user_id VARCHAR(128) DEFAULT NULL, status VARCHAR(32) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_publication_page_idx ON page_publication (page_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_publication_status_idx ON page_publication (status)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_PAGE_PUBLICATION_REVISION ON page_publication (revision_id)');

        $this->addSql('CREATE TABLE IF NOT EXISTS page_attachment_reference (id VARCHAR(32) NOT NULL, page_id VARCHAR(32) NOT NULL, revision_id VARCHAR(32) DEFAULT NULL, attachment_id VARCHAR(128) NOT NULL, attachment_code VARCHAR(128) DEFAULT NULL, usage VARCHAR(32) NOT NULL, position INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_attachment_page_idx ON page_attachment_reference (page_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_attachment_revision_idx ON page_attachment_reference (revision_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_attachment_usage_idx ON page_attachment_reference (usage)');

        $this->addSql('CREATE TABLE IF NOT EXISTS page_grant (id VARCHAR(32) NOT NULL, page_id VARCHAR(32) NOT NULL, subject_user_id VARCHAR(128) DEFAULT NULL, subject_role VARCHAR(128) DEFAULT NULL, grant_type VARCHAR(32) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by_user_id VARCHAR(128) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_grant_page_idx ON page_grant (page_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_grant_subject_user_idx ON page_grant (subject_user_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_grant_subject_role_idx ON page_grant (subject_role)');

        $this->addSql('CREATE TABLE IF NOT EXISTS page_acceptance (id VARCHAR(32) NOT NULL, page_id VARCHAR(32) NOT NULL, revision_id VARCHAR(32) NOT NULL, subject_user_id VARCHAR(128) NOT NULL, revision_checksum VARCHAR(64) NOT NULL, accepted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ip_hash VARCHAR(64) DEFAULT NULL, user_agent_hash VARCHAR(64) DEFAULT NULL, acceptance_context JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_acceptance_page_idx ON page_acceptance (page_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_acceptance_revision_idx ON page_acceptance (revision_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_acceptance_subject_idx ON page_acceptance (subject_user_id)');
        $this->addSql('CREATE INDEX IF NOT EXISTS page_acceptance_checksum_idx ON page_acceptance (revision_checksum)');

        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_PAGE_CURRENT_REVISION FOREIGN KEY (current_revision_id) REFERENCES page_revision (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_PAGE_PUBLISHED_REVISION FOREIGN KEY (published_revision_id) REFERENCES page_revision (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page_revision ADD CONSTRAINT FK_PAGE_REVISION_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page_publication ADD CONSTRAINT FK_PAGE_PUBLICATION_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page_publication ADD CONSTRAINT FK_PAGE_PUBLICATION_REVISION FOREIGN KEY (revision_id) REFERENCES page_revision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page_attachment_reference ADD CONSTRAINT FK_PAGE_ATTACHMENT_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page_attachment_reference ADD CONSTRAINT FK_PAGE_ATTACHMENT_REVISION FOREIGN KEY (revision_id) REFERENCES page_revision (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page_grant ADD CONSTRAINT FK_PAGE_GRANT_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page_acceptance ADD CONSTRAINT FK_PAGE_ACCEPTANCE_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE page_acceptance ADD CONSTRAINT FK_PAGE_ACCEPTANCE_REVISION FOREIGN KEY (revision_id) REFERENCES page_revision (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS page_acceptance');
        $this->addSql('DROP TABLE IF EXISTS page_grant');
        $this->addSql('DROP TABLE IF EXISTS page_attachment_reference');
        $this->addSql('DROP TABLE IF EXISTS page_publication');
        $this->addSql('DROP TABLE IF EXISTS page_revision');
        $this->addSql('DROP TABLE IF EXISTS page');
    }
}
