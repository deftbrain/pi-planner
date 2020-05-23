<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200523133219 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE team_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE program_increment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sprint_schedule_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE epic_status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sprint_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE team_sprint_capacity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE epic_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE workitem_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE backlog_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE team (id INT NOT NULL, external_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE program_increment (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE program_increment_projects (pi_id INT NOT NULL, project_id INT NOT NULL, PRIMARY KEY(pi_id, project_id))');
        $this->addSql('CREATE INDEX IDX_711F1D47E0DEB379 ON program_increment_projects (pi_id)');
        $this->addSql('CREATE INDEX IDX_711F1D47166D1F9C ON program_increment_projects (project_id)');
        $this->addSql('CREATE TABLE program_increment_team (pi_id INT NOT NULL, team_id INT NOT NULL, PRIMARY KEY(pi_id, team_id))');
        $this->addSql('CREATE INDEX IDX_16E28682E0DEB379 ON program_increment_team (pi_id)');
        $this->addSql('CREATE INDEX IDX_16E28682296CD8AE ON program_increment_team (team_id)');
        $this->addSql('CREATE TABLE program_increment_sprint (pi_id INT NOT NULL, sprint_id INT NOT NULL, PRIMARY KEY(pi_id, sprint_id))');
        $this->addSql('CREATE INDEX IDX_1001532AE0DEB379 ON program_increment_sprint (pi_id)');
        $this->addSql('CREATE INDEX IDX_1001532A8C24077B ON program_increment_sprint (sprint_id)');
        $this->addSql('CREATE TABLE program_increment_epic (pi_id INT NOT NULL, sprint_id INT NOT NULL, PRIMARY KEY(pi_id, sprint_id))');
        $this->addSql('CREATE INDEX IDX_CBCB70ECE0DEB379 ON program_increment_epic (pi_id)');
        $this->addSql('CREATE INDEX IDX_CBCB70EC8C24077B ON program_increment_epic (sprint_id)');
        $this->addSql('CREATE TABLE sprint_schedule (id INT NOT NULL, external_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE epic_status (id INT NOT NULL, external_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sprint (id INT NOT NULL, schedule_id INT NOT NULL, external_id VARCHAR(255) DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EF8055B7A40BC2D5 ON sprint (schedule_id)');
        $this->addSql('CREATE TABLE team_sprint_capacity (id INT NOT NULL, team_id INT NOT NULL, sprint_id INT NOT NULL, capacity_frontend DOUBLE PRECISION NOT NULL, capacity_backend DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E6B1FD39296CD8AE ON team_sprint_capacity (team_id)');
        $this->addSql('CREATE INDEX IDX_E6B1FD398C24077B ON team_sprint_capacity (sprint_id)');
        $this->addSql('CREATE TABLE epic (id INT NOT NULL, status_id INT NOT NULL, external_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_19C950716BF700BD ON epic (status_id)');
        $this->addSql('CREATE TABLE project (id INT NOT NULL, sprint_schedule_id INT DEFAULT NULL, external_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEF0A3F61D ON project (sprint_schedule_id)');
        $this->addSql('CREATE TABLE workitem (id INT NOT NULL, project_id INT NOT NULL, team_id INT DEFAULT NULL, sprint_id INT DEFAULT NULL, epic_id INT NOT NULL, backlog_group_id INT NOT NULL, external_id VARCHAR(255) DEFAULT NULL, estimate_frontend DOUBLE PRECISION DEFAULT NULL, estimate_backend DOUBLE PRECISION DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_281E6B78166D1F9C ON workitem (project_id)');
        $this->addSql('CREATE INDEX IDX_281E6B78296CD8AE ON workitem (team_id)');
        $this->addSql('CREATE INDEX IDX_281E6B788C24077B ON workitem (sprint_id)');
        $this->addSql('CREATE INDEX IDX_281E6B786B71E00E ON workitem (epic_id)');
        $this->addSql('CREATE INDEX IDX_281E6B785BCB6642 ON workitem (backlog_group_id)');
        $this->addSql('CREATE TABLE backlog_group (id INT NOT NULL, project_id INT DEFAULT NULL, external_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_99AABD78166D1F9C ON backlog_group (project_id)');
        $this->addSql('ALTER TABLE program_increment_projects ADD CONSTRAINT FK_711F1D47E0DEB379 FOREIGN KEY (pi_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_projects ADD CONSTRAINT FK_711F1D47166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_team ADD CONSTRAINT FK_16E28682E0DEB379 FOREIGN KEY (pi_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_team ADD CONSTRAINT FK_16E28682296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_sprint ADD CONSTRAINT FK_1001532AE0DEB379 FOREIGN KEY (pi_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_sprint ADD CONSTRAINT FK_1001532A8C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_epic ADD CONSTRAINT FK_CBCB70ECE0DEB379 FOREIGN KEY (pi_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_epic ADD CONSTRAINT FK_CBCB70EC8C24077B FOREIGN KEY (sprint_id) REFERENCES epic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sprint ADD CONSTRAINT FK_EF8055B7A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES sprint_schedule (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT FK_E6B1FD39296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT FK_E6B1FD398C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE epic ADD CONSTRAINT FK_19C950716BF700BD FOREIGN KEY (status_id) REFERENCES epic_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEF0A3F61D FOREIGN KEY (sprint_schedule_id) REFERENCES sprint_schedule (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE workitem ADD CONSTRAINT FK_281E6B78166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE workitem ADD CONSTRAINT FK_281E6B78296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE workitem ADD CONSTRAINT FK_281E6B788C24077B FOREIGN KEY (sprint_id) REFERENCES sprint (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE workitem ADD CONSTRAINT FK_281E6B786B71E00E FOREIGN KEY (epic_id) REFERENCES epic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE workitem ADD CONSTRAINT FK_281E6B785BCB6642 FOREIGN KEY (backlog_group_id) REFERENCES backlog_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE backlog_group ADD CONSTRAINT FK_99AABD78166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE program_increment_team DROP CONSTRAINT FK_16E28682296CD8AE');
        $this->addSql('ALTER TABLE team_sprint_capacity DROP CONSTRAINT FK_E6B1FD39296CD8AE');
        $this->addSql('ALTER TABLE workitem DROP CONSTRAINT FK_281E6B78296CD8AE');
        $this->addSql('ALTER TABLE program_increment_projects DROP CONSTRAINT FK_711F1D47E0DEB379');
        $this->addSql('ALTER TABLE program_increment_team DROP CONSTRAINT FK_16E28682E0DEB379');
        $this->addSql('ALTER TABLE program_increment_sprint DROP CONSTRAINT FK_1001532AE0DEB379');
        $this->addSql('ALTER TABLE program_increment_epic DROP CONSTRAINT FK_CBCB70ECE0DEB379');
        $this->addSql('ALTER TABLE sprint DROP CONSTRAINT FK_EF8055B7A40BC2D5');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EEF0A3F61D');
        $this->addSql('ALTER TABLE epic DROP CONSTRAINT FK_19C950716BF700BD');
        $this->addSql('ALTER TABLE program_increment_sprint DROP CONSTRAINT FK_1001532A8C24077B');
        $this->addSql('ALTER TABLE team_sprint_capacity DROP CONSTRAINT FK_E6B1FD398C24077B');
        $this->addSql('ALTER TABLE workitem DROP CONSTRAINT FK_281E6B788C24077B');
        $this->addSql('ALTER TABLE program_increment_epic DROP CONSTRAINT FK_CBCB70EC8C24077B');
        $this->addSql('ALTER TABLE workitem DROP CONSTRAINT FK_281E6B786B71E00E');
        $this->addSql('ALTER TABLE program_increment_projects DROP CONSTRAINT FK_711F1D47166D1F9C');
        $this->addSql('ALTER TABLE workitem DROP CONSTRAINT FK_281E6B78166D1F9C');
        $this->addSql('ALTER TABLE backlog_group DROP CONSTRAINT FK_99AABD78166D1F9C');
        $this->addSql('ALTER TABLE workitem DROP CONSTRAINT FK_281E6B785BCB6642');
        $this->addSql('DROP SEQUENCE team_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE program_increment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sprint_schedule_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE epic_status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sprint_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE team_sprint_capacity_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE epic_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE workitem_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE backlog_group_id_seq CASCADE');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE program_increment');
        $this->addSql('DROP TABLE program_increment_projects');
        $this->addSql('DROP TABLE program_increment_team');
        $this->addSql('DROP TABLE program_increment_sprint');
        $this->addSql('DROP TABLE program_increment_epic');
        $this->addSql('DROP TABLE sprint_schedule');
        $this->addSql('DROP TABLE epic_status');
        $this->addSql('DROP TABLE sprint');
        $this->addSql('DROP TABLE team_sprint_capacity');
        $this->addSql('DROP TABLE epic');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE workitem');
        $this->addSql('DROP TABLE backlog_group');
    }
}
