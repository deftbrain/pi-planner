<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200510115303 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE program_increment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE program_increment (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE program_increment_projects (pi_id INT NOT NULL, project_id INT NOT NULL, PRIMARY KEY(pi_id, project_id))');
        $this->addSql('CREATE INDEX IDX_711F1D47E0DEB379 ON program_increment_projects (pi_id)');
        $this->addSql('CREATE INDEX IDX_711F1D47166D1F9C ON program_increment_projects (project_id)');
        $this->addSql('CREATE TABLE project (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE program_increment_projects ADD CONSTRAINT FK_711F1D47E0DEB379 FOREIGN KEY (pi_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_projects ADD CONSTRAINT FK_711F1D47166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE program_increment_projects DROP CONSTRAINT FK_711F1D47E0DEB379');
        $this->addSql('ALTER TABLE program_increment_projects DROP CONSTRAINT FK_711F1D47166D1F9C');
        $this->addSql('DROP SEQUENCE program_increment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_id_seq CASCADE');
        $this->addSql('DROP TABLE program_increment');
        $this->addSql('DROP TABLE program_increment_projects');
        $this->addSql('DROP TABLE project');
    }
}
