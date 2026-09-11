<?php

declare(strict_types=1);

namespace App\Paging\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260730052600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the Paging page lifecycle tables.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on PostgreSQL.');

        $this->addStatements(<<<'SQL'
CREATE TABLE page (
    id SERIAL NOT NULL,
    current_revision_id VARCHAR(32) DEFAULT NULL,
    published_revision_id VARCHAR(32) DEFAULT NULL,
    code VARCHAR(128) NOT NULL,
    slug VARCHAR(128) NOT NULL,
    title VARCHAR(255) NOT NULL,
    kind VARCHAR(32) NOT NULL,
    status VARCHAR(32) NOT NULL,
    owner_user_id VARCHAR(128) DEFAULT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
);
CREATE UNIQUE INDEX page_code_uniq ON page (code);
CREATE UNIQUE INDEX page_slug_uniq ON page (slug);
CREATE INDEX page_kind_idx ON page (kind);
CREATE INDEX page_status_idx ON page (status);
COMMENT ON COLUMN page.created_at IS '(DC2Type:datetime_immutable)';
COMMENT ON COLUMN page.updated_at IS '(DC2Type:datetime_immutable)';

CREATE TABLE page_revision (
    id VARCHAR(32) NOT NULL,
    page_id INT NOT NULL,
    revision_number INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body_html TEXT NOT NULL,
    body_markdown TEXT DEFAULT NULL,
    body_json JSON DEFAULT NULL,
    body_text TEXT NOT NULL,
    change_note TEXT DEFAULT NULL,
    checksum VARCHAR(64) NOT NULL,
    created_by_user_id VARCHAR(128) DEFAULT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    locked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    locked_by VARCHAR(128) DEFAULT NULL,
    PRIMARY KEY(id)
);
CREATE INDEX page_revision_page_idx ON page_revision (page_id);
CREATE UNIQUE INDEX page_revision_number_uniq ON page_revision (page_id, revision_number);
COMMENT ON COLUMN page_revision.created_at IS '(DC2Type:datetime_immutable)';
COMMENT ON COLUMN page_revision.locked_at IS '(DC2Type:datetime_immutable)';
ALTER TABLE page_revision ADD CONSTRAINT FK_PAGE_REVISION_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE;
ALTER TABLE page ADD CONSTRAINT FK_PAGE_CURRENT_REVISION FOREIGN KEY (current_revision_id) REFERENCES page_revision (id) ON DELETE SET NULL;
ALTER TABLE page ADD CONSTRAINT FK_PAGE_PUBLISHED_REVISION FOREIGN KEY (published_revision_id) REFERENCES page_revision (id) ON DELETE SET NULL;
CREATE INDEX IDX_PAGE_CURRENT_REVISION ON page (current_revision_id);
CREATE INDEX IDX_PAGE_PUBLISHED_REVISION ON page (published_revision_id);

CREATE TABLE page_publication (
    id VARCHAR(32) NOT NULL,
    page_id INT NOT NULL,
    revision_id VARCHAR(32) NOT NULL,
    published_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    effective_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    published_by_user_id VARCHAR(128) DEFAULT NULL,
    status VARCHAR(32) NOT NULL,
    PRIMARY KEY(id)
);
CREATE INDEX page_publication_page_idx ON page_publication (page_id);
CREATE INDEX page_publication_status_idx ON page_publication (status);
CREATE INDEX IDX_PAGE_PUBLICATION_REVISION ON page_publication (revision_id);
COMMENT ON COLUMN page_publication.published_at IS '(DC2Type:datetime_immutable)';
COMMENT ON COLUMN page_publication.effective_from IS '(DC2Type:datetime_immutable)';
COMMENT ON COLUMN page_publication.expires_at IS '(DC2Type:datetime_immutable)';
ALTER TABLE page_publication ADD CONSTRAINT FK_PAGE_PUBLICATION_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE;
ALTER TABLE page_publication ADD CONSTRAINT FK_PAGE_PUBLICATION_REVISION FOREIGN KEY (revision_id) REFERENCES page_revision (id) ON DELETE CASCADE;

CREATE TABLE page_attachment_reference (
    id VARCHAR(32) NOT NULL,
    page_id INT NOT NULL,
    revision_id VARCHAR(32) DEFAULT NULL,
    attachment_id VARCHAR(128) NOT NULL,
    attachment_code VARCHAR(128) DEFAULT NULL,
    usage VARCHAR(32) NOT NULL,
    position INT NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
);
CREATE INDEX page_attachment_page_idx ON page_attachment_reference (page_id);
CREATE INDEX page_attachment_revision_idx ON page_attachment_reference (revision_id);
CREATE INDEX page_attachment_usage_idx ON page_attachment_reference (usage);
COMMENT ON COLUMN page_attachment_reference.created_at IS '(DC2Type:datetime_immutable)';
ALTER TABLE page_attachment_reference ADD CONSTRAINT FK_PAGE_ATTACHMENT_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE;
ALTER TABLE page_attachment_reference ADD CONSTRAINT FK_PAGE_ATTACHMENT_REVISION FOREIGN KEY (revision_id) REFERENCES page_revision (id) ON DELETE SET NULL;

CREATE TABLE page_grant (
    id VARCHAR(32) NOT NULL,
    page_id INT NOT NULL,
    subject_user_id VARCHAR(128) DEFAULT NULL,
    subject_role VARCHAR(128) DEFAULT NULL,
    grant_type VARCHAR(32) NOT NULL,
    created_by_user_id VARCHAR(128) DEFAULT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
);
CREATE INDEX page_grant_page_idx ON page_grant (page_id);
CREATE INDEX page_grant_user_idx ON page_grant (subject_user_id);
CREATE INDEX page_grant_role_idx ON page_grant (subject_role);
COMMENT ON COLUMN page_grant.created_at IS '(DC2Type:datetime_immutable)';
ALTER TABLE page_grant ADD CONSTRAINT FK_PAGE_GRANT_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE;

CREATE TABLE page_acceptance (
    id VARCHAR(32) NOT NULL,
    page_id INT NOT NULL,
    revision_id VARCHAR(32) NOT NULL,
    subject_user_id VARCHAR(128) NOT NULL,
    revision_checksum VARCHAR(64) NOT NULL,
    accepted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    ip_hash VARCHAR(64) DEFAULT NULL,
    user_agent_hash VARCHAR(64) DEFAULT NULL,
    acceptance_context JSON DEFAULT NULL,
    PRIMARY KEY(id)
);
CREATE INDEX page_acceptance_page_idx ON page_acceptance (page_id);
CREATE INDEX page_acceptance_revision_idx ON page_acceptance (revision_id);
CREATE INDEX page_acceptance_subject_idx ON page_acceptance (subject_user_id);
CREATE INDEX page_acceptance_checksum_idx ON page_acceptance (revision_checksum);
COMMENT ON COLUMN page_acceptance.accepted_at IS '(DC2Type:datetime_immutable)';
ALTER TABLE page_acceptance ADD CONSTRAINT FK_PAGE_ACCEPTANCE_PAGE FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE;
ALTER TABLE page_acceptance ADD CONSTRAINT FK_PAGE_ACCEPTANCE_REVISION FOREIGN KEY (revision_id) REFERENCES page_revision (id) ON DELETE CASCADE;
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on PostgreSQL.');

        $this->addStatements(<<<'SQL'
DROP TABLE page_acceptance;
DROP TABLE page_grant;
DROP TABLE page_attachment_reference;
DROP TABLE page_publication;
ALTER TABLE page DROP CONSTRAINT FK_PAGE_CURRENT_REVISION;
ALTER TABLE page DROP CONSTRAINT FK_PAGE_PUBLISHED_REVISION;
DROP TABLE page_revision;
DROP TABLE page;
SQL);
    }

    private function addStatements(string $sql): void
    {
        foreach (preg_split('/;\s*(?:\r?\n|$)/', trim($sql), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $statement) {
            $this->addSql($statement);
        }
    }
}
