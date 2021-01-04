<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210105000825 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE epic ADD project_settings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE epic ADD CONSTRAINT FK_19C95071915B6F73 FOREIGN KEY (project_settings_id) REFERENCES project_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_19C95071915B6F73 ON epic (project_settings_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE epic DROP CONSTRAINT FK_19C95071915B6F73');
        $this->addSql('DROP INDEX IDX_19C95071915B6F73');
        $this->addSql('ALTER TABLE epic DROP project_settings_id');
    }
}
