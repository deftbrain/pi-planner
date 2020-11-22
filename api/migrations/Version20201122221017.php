<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201122221017 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE program_increment_epic_status (program_increment_id INT NOT NULL, epic_status_id INT NOT NULL, PRIMARY KEY(program_increment_id, epic_status_id))');
        $this->addSql('CREATE INDEX IDX_7298F3F01AA07519 ON program_increment_epic_status (program_increment_id)');
        $this->addSql('CREATE INDEX IDX_7298F3F0DA1D8B71 ON program_increment_epic_status (epic_status_id)');
        $this->addSql('ALTER TABLE program_increment_epic_status ADD CONSTRAINT FK_7298F3F01AA07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_increment_epic_status ADD CONSTRAINT FK_7298F3F0DA1D8B71 FOREIGN KEY (epic_status_id) REFERENCES epic_status (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE epic_status ADD sort_order INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE program_increment_epic_status');
        $this->addSql('ALTER TABLE epic_status DROP sort_order');
    }
}
