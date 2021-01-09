<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201106071502 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE team_sprint_capacity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE program_increment_sprint (program_increment_id INT NOT NULL, sprint_id INT NOT NULL, PRIMARY KEY(program_increment_id, sprint_id))');
        $this->addSql('CREATE INDEX IDX_1001532A1AA07519 ON program_increment_sprint (program_increment_id)');
        $this->addSql('CREATE INDEX IDX_1001532A8C24077B ON program_increment_sprint (sprint_id)');
        $this->addSql('CREATE TABLE team_sprint_capacity (id INT NOT NULL, team_id INT NOT NULL, sprint_id INT DEFAULT NULL, program_increment_id INT NOT NULL, capacity JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E6B1FD39296CD8AE ON team_sprint_capacity (team_id)');
        $this->addSql('CREATE INDEX IDX_E6B1FD398C24077B ON team_sprint_capacity (sprint_id)');
        $this->addSql('CREATE INDEX IDX_E6B1FD391AA07519 ON team_sprint_capacity (program_increment_id)');
        $this->addSql('ALTER TABLE program_increment_sprint ADD CONSTRAINT FK_1001532A1AA07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_sprint ADD CONSTRAINT FK_1001532A8C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT FK_E6B1FD39296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT FK_E6B1FD398C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT FK_E6B1FD391AA07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment ADD project_id INT NOT NULL');
        $this->addSql('ALTER TABLE program_increment DROP project_settings');
        $this->addSql('ALTER TABLE program_increment ADD CONSTRAINT FK_3BAB791166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3BAB791166D1F9C ON program_increment (project_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE team_sprint_capacity_id_seq CASCADE');
        $this->addSql('DROP TABLE program_increment_sprint');
        $this->addSql('DROP TABLE team_sprint_capacity');
        $this->addSql('ALTER TABLE program_increment DROP CONSTRAINT FK_3BAB791166D1F9C');
        $this->addSql('DROP INDEX IDX_3BAB791166D1F9C');
        $this->addSql('ALTER TABLE program_increment ADD project_settings JSON NOT NULL');
        $this->addSql('ALTER TABLE program_increment DROP project_id');
    }
}
