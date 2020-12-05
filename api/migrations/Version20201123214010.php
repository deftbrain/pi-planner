<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201123214010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE workitem_dependencies (workitem_id INT NOT NULL, dependency_id INT NOT NULL, PRIMARY KEY(workitem_id, dependency_id))');
        $this->addSql('CREATE INDEX IDX_A969D92240A573FF ON workitem_dependencies (workitem_id)');
        $this->addSql('CREATE INDEX IDX_A969D922C2F67723 ON workitem_dependencies (dependency_id)');
        $this->addSql('ALTER TABLE workitem_dependencies ADD CONSTRAINT FK_A969D92240A573FF FOREIGN KEY (workitem_id) REFERENCES workitem (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE workitem_dependencies ADD CONSTRAINT FK_A969D922C2F67723 FOREIGN KEY (dependency_id) REFERENCES workitem (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE workitem_dependencies');
    }
}
