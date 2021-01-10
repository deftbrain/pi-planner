<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210110124102 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE workitem_status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE backlog_group_project (backlog_group_id INT NOT NULL, project_id INT NOT NULL, PRIMARY KEY(backlog_group_id, project_id))');
        $this->addSql('CREATE INDEX IDX_17DE23F5BCB6642 ON backlog_group_project (backlog_group_id)');
        $this->addSql('CREATE INDEX IDX_17DE23F166D1F9C ON backlog_group_project (project_id)');
        $this->addSql('CREATE TABLE workitem_status (id INT NOT NULL, external_id VARCHAR(255) DEFAULT NULL, changed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_deleted BOOLEAN NOT NULL, sort_order INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE backlog_group_project ADD CONSTRAINT FK_17DE23F5BCB6642 FOREIGN KEY (backlog_group_id) REFERENCES backlog_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE backlog_group_project ADD CONSTRAINT FK_17DE23F166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE backlog_group DROP CONSTRAINT fk_99aabd78166d1f9c');
        $this->addSql('DROP INDEX idx_99aabd78166d1f9c');
        $this->addSql('ALTER TABLE backlog_group DROP project_id');
        $this->addSql('ALTER TABLE workitem ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE workitem ADD CONSTRAINT FK_281E6B786BF700BD FOREIGN KEY (status_id) REFERENCES workitem_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_281E6B786BF700BD ON workitem (status_id)');
        $this->addSql('ALTER TABLE project_settings ADD default_workitem_status_id INT NOT NULL');
        $this->addSql('ALTER TABLE project_settings ADD CONSTRAINT FK_D80B2B1EE8E521BB FOREIGN KEY (default_workitem_status_id) REFERENCES workitem_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D80B2B1EE8E521BB ON project_settings (default_workitem_status_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE workitem DROP CONSTRAINT FK_281E6B786BF700BD');
        $this->addSql('ALTER TABLE project_settings DROP CONSTRAINT FK_D80B2B1EE8E521BB');
        $this->addSql('DROP SEQUENCE workitem_status_id_seq CASCADE');
        $this->addSql('DROP TABLE backlog_group_project');
        $this->addSql('DROP TABLE workitem_status');
        $this->addSql('DROP INDEX IDX_281E6B786BF700BD');
        $this->addSql('ALTER TABLE workitem DROP status_id');
        $this->addSql('ALTER TABLE backlog_group ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE backlog_group ADD CONSTRAINT fk_99aabd78166d1f9c FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_99aabd78166d1f9c ON backlog_group (project_id)');
        $this->addSql('DROP INDEX IDX_D80B2B1EE8E521BB');
        $this->addSql('ALTER TABLE project_settings DROP default_workitem_status_id');
    }
}
