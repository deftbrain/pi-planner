<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200624115103 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE team_sprint_capacity_id_seq CASCADE');
        $this->addSql('DROP TABLE team_sprint_capacity');
        $this->addSql('ALTER TABLE team ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE sprint_schedule ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE epic_status ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE sprint ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE epic ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE project ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE workitem ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE backlog_group ADD is_deleted BOOLEAN NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE team_sprint_capacity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE team_sprint_capacity (id INT NOT NULL, team_id INT NOT NULL, sprint_id INT NOT NULL, program_increment_id INT NOT NULL, capacity_frontend DOUBLE PRECISION NOT NULL, capacity_backend DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_e6b1fd398c24077b ON team_sprint_capacity (sprint_id)');
        $this->addSql('CREATE INDEX idx_e6b1fd39296cd8ae ON team_sprint_capacity (team_id)');
        $this->addSql('CREATE INDEX idx_e6b1fd391aa07519 ON team_sprint_capacity (program_increment_id)');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT fk_e6b1fd39296cd8ae FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT fk_e6b1fd398c24077b FOREIGN KEY (sprint_id) REFERENCES sprint (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT fk_e6b1fd391aa07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sprint_schedule DROP is_deleted');
        $this->addSql('ALTER TABLE sprint DROP is_deleted');
        $this->addSql('ALTER TABLE team DROP is_deleted');
        $this->addSql('ALTER TABLE epic_status DROP is_deleted');
        $this->addSql('ALTER TABLE epic DROP is_deleted');
        $this->addSql('ALTER TABLE project DROP is_deleted');
        $this->addSql('ALTER TABLE workitem DROP is_deleted');
        $this->addSql('ALTER TABLE backlog_group DROP is_deleted');
    }
}
