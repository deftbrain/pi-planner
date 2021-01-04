<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210106225218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE project_settings_epic_status');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE project_settings_epic_status (project_settings_id INT NOT NULL, epic_status_id INT NOT NULL, PRIMARY KEY(project_settings_id, epic_status_id))');
        $this->addSql('CREATE INDEX idx_44a05f11da1d8b71 ON project_settings_epic_status (epic_status_id)');
        $this->addSql('CREATE INDEX idx_44a05f11915b6f73 ON project_settings_epic_status (project_settings_id)');
        $this->addSql('ALTER TABLE project_settings_epic_status ADD CONSTRAINT fk_44a05f11915b6f73 FOREIGN KEY (project_settings_id) REFERENCES project_settings (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_settings_epic_status ADD CONSTRAINT fk_44a05f11da1d8b71 FOREIGN KEY (epic_status_id) REFERENCES epic_status (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
