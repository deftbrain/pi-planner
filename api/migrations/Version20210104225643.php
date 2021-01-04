<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210104225643 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE project_settings_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE project_settings (id INT NOT NULL, project_id INT NOT NULL, program_increment_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D80B2B1E166D1F9C ON project_settings (project_id)');
        $this->addSql('CREATE INDEX IDX_D80B2B1E1AA07519 ON project_settings (program_increment_id)');
        $this->addSql('CREATE TABLE project_settings_sprint (project_settings_id INT NOT NULL, sprint_id INT NOT NULL, PRIMARY KEY(project_settings_id, sprint_id))');
        $this->addSql('CREATE INDEX IDX_D8090035915B6F73 ON project_settings_sprint (project_settings_id)');
        $this->addSql('CREATE INDEX IDX_D80900358C24077B ON project_settings_sprint (sprint_id)');
        $this->addSql('CREATE TABLE project_settings_epic_status (project_settings_id INT NOT NULL, epic_status_id INT NOT NULL, PRIMARY KEY(project_settings_id, epic_status_id))');
        $this->addSql('CREATE INDEX IDX_44A05F11915B6F73 ON project_settings_epic_status (project_settings_id)');
        $this->addSql('CREATE INDEX IDX_44A05F11DA1D8B71 ON project_settings_epic_status (epic_status_id)');
        $this->addSql('ALTER TABLE project_settings ADD CONSTRAINT FK_D80B2B1E166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_settings ADD CONSTRAINT FK_D80B2B1E1AA07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_settings_sprint ADD CONSTRAINT FK_D8090035915B6F73 FOREIGN KEY (project_settings_id) REFERENCES project_settings (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_settings_sprint ADD CONSTRAINT FK_D80900358C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_settings_epic_status ADD CONSTRAINT FK_44A05F11915B6F73 FOREIGN KEY (project_settings_id) REFERENCES project_settings (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_settings_epic_status ADD CONSTRAINT FK_44A05F11DA1D8B71 FOREIGN KEY (epic_status_id) REFERENCES epic_status (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE program_increment_sprint');
        $this->addSql('DROP TABLE program_increment_epic_status');
        $this->addSql('ALTER TABLE program_increment DROP CONSTRAINT fk_3bab791166d1f9c');
        $this->addSql('DROP INDEX idx_3bab791166d1f9c');
        $this->addSql('ALTER TABLE program_increment DROP project_id');
        $this->addSql('ALTER TABLE team_sprint_capacity DROP CONSTRAINT fk_e6b1fd391aa07519');
        $this->addSql('DROP INDEX idx_e6b1fd391aa07519');
        $this->addSql('ALTER TABLE team_sprint_capacity RENAME COLUMN program_increment_id TO project_settings_id');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT FK_E6B1FD39915B6F73 FOREIGN KEY (project_settings_id) REFERENCES project_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E6B1FD39915B6F73 ON team_sprint_capacity (project_settings_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE project_settings_sprint DROP CONSTRAINT FK_D8090035915B6F73');
        $this->addSql('ALTER TABLE project_settings_epic_status DROP CONSTRAINT FK_44A05F11915B6F73');
        $this->addSql('ALTER TABLE team_sprint_capacity DROP CONSTRAINT FK_E6B1FD39915B6F73');
        $this->addSql('DROP SEQUENCE project_settings_id_seq CASCADE');
        $this->addSql('CREATE TABLE program_increment_sprint (program_increment_id INT NOT NULL, sprint_id INT NOT NULL, PRIMARY KEY(program_increment_id, sprint_id))');
        $this->addSql('CREATE INDEX idx_1001532a8c24077b ON program_increment_sprint (sprint_id)');
        $this->addSql('CREATE INDEX idx_1001532a1aa07519 ON program_increment_sprint (program_increment_id)');
        $this->addSql('CREATE TABLE program_increment_epic_status (program_increment_id INT NOT NULL, epic_status_id INT NOT NULL, PRIMARY KEY(program_increment_id, epic_status_id))');
        $this->addSql('CREATE INDEX idx_7298f3f0da1d8b71 ON program_increment_epic_status (epic_status_id)');
        $this->addSql('CREATE INDEX idx_7298f3f01aa07519 ON program_increment_epic_status (program_increment_id)');
        $this->addSql('ALTER TABLE program_increment_sprint ADD CONSTRAINT fk_1001532a1aa07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_sprint ADD CONSTRAINT fk_1001532a8c24077b FOREIGN KEY (sprint_id) REFERENCES sprint (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_epic_status ADD CONSTRAINT fk_7298f3f01aa07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_epic_status ADD CONSTRAINT fk_7298f3f0da1d8b71 FOREIGN KEY (epic_status_id) REFERENCES epic_status (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE project_settings');
        $this->addSql('DROP TABLE project_settings_sprint');
        $this->addSql('DROP TABLE project_settings_epic_status');
        $this->addSql('ALTER TABLE program_increment ADD project_id INT NOT NULL');
        $this->addSql('ALTER TABLE program_increment ADD CONSTRAINT fk_3bab791166d1f9c FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3bab791166d1f9c ON program_increment (project_id)');
        $this->addSql('DROP INDEX IDX_E6B1FD39915B6F73');
        $this->addSql('ALTER TABLE team_sprint_capacity RENAME COLUMN project_settings_id TO program_increment_id');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT fk_e6b1fd391aa07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e6b1fd391aa07519 ON team_sprint_capacity (program_increment_id)');
    }
}
