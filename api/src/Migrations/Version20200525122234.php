<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200525122234 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE team_sprint_capacity ADD program_increment_id INT NOT NULL');
        $this->addSql('ALTER TABLE team_sprint_capacity ADD CONSTRAINT FK_E6B1FD391AA07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E6B1FD391AA07519 ON team_sprint_capacity (program_increment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE team_sprint_capacity DROP CONSTRAINT FK_E6B1FD391AA07519');
        $this->addSql('DROP INDEX IDX_E6B1FD391AA07519');
        $this->addSql('ALTER TABLE team_sprint_capacity DROP program_increment_id');
    }
}
