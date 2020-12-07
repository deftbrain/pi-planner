<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201206185129 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE epic_team (epic_id INT NOT NULL, team_id INT NOT NULL, PRIMARY KEY(epic_id, team_id))');
        $this->addSql('CREATE INDEX IDX_9304140E6B71E00E ON epic_team (epic_id)');
        $this->addSql('CREATE INDEX IDX_9304140E296CD8AE ON epic_team (team_id)');
        $this->addSql('ALTER TABLE epic_team ADD CONSTRAINT FK_9304140E6B71E00E FOREIGN KEY (epic_id) REFERENCES epic (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE epic_team ADD CONSTRAINT FK_9304140E296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE epic_team');
    }
}
