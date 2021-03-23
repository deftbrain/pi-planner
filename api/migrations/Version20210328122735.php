<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210328122735 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE backlog_group ALTER changed_at DROP NOT NULL');
        $this->addSql('ALTER TABLE epic ADD program_increment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE epic ALTER status_id DROP NOT NULL');
        $this->addSql('ALTER TABLE epic ALTER changed_at DROP NOT NULL');
        $this->addSql('ALTER TABLE epic ADD CONSTRAINT FK_19C950711AA07519 FOREIGN KEY (program_increment_id) REFERENCES program_increment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_19C950711AA07519 ON epic (program_increment_id)');
        $this->addSql('ALTER TABLE workitem ALTER backlog_group_id DROP NOT NULL');
        $this->addSql('ALTER TABLE workitem ALTER status_id DROP NOT NULL');
        $this->addSql('ALTER TABLE workitem ALTER changed_at DROP NOT NULL');
        $this->addSql('ALTER TABLE program_increment ADD external_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE program_increment ADD changed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE program_increment ADD is_deleted BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE project ALTER changed_at DROP NOT NULL');
        $this->addSql('ALTER TABLE sprint_schedule ALTER changed_at DROP NOT NULL');
        $this->addSql('ALTER TABLE sprint ALTER changed_at DROP NOT NULL');
        $this->addSql('ALTER TABLE team ALTER changed_at DROP NOT NULL');
        $this->addSql('ALTER TABLE epic_status ALTER changed_at DROP NOT NULL');
        $this->addSql('ALTER TABLE project_settings ALTER default_workitem_status_id DROP NOT NULL');
        $this->addSql('ALTER TABLE workitem_status ALTER changed_at DROP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE workitem_status ALTER changed_at SET NOT NULL');
        $this->addSql('ALTER TABLE program_increment DROP external_id');
        $this->addSql('ALTER TABLE program_increment DROP changed_at');
        $this->addSql('ALTER TABLE program_increment DROP is_deleted');
        $this->addSql('ALTER TABLE epic_status ALTER changed_at SET NOT NULL');
        $this->addSql('ALTER TABLE epic DROP CONSTRAINT FK_19C950711AA07519');
        $this->addSql('DROP INDEX IDX_19C950711AA07519');
        $this->addSql('ALTER TABLE epic DROP program_increment_id');
        $this->addSql('ALTER TABLE epic ALTER status_id SET NOT NULL');
        $this->addSql('ALTER TABLE epic ALTER changed_at SET NOT NULL');
        $this->addSql('ALTER TABLE sprint_schedule ALTER changed_at SET NOT NULL');
        $this->addSql('ALTER TABLE sprint ALTER changed_at SET NOT NULL');
        $this->addSql('ALTER TABLE project ALTER changed_at SET NOT NULL');
        $this->addSql('ALTER TABLE team ALTER changed_at SET NOT NULL');
        $this->addSql('ALTER TABLE workitem ALTER backlog_group_id SET NOT NULL');
        $this->addSql('ALTER TABLE workitem ALTER status_id SET NOT NULL');
        $this->addSql('ALTER TABLE workitem ALTER changed_at SET NOT NULL');
        $this->addSql('ALTER TABLE backlog_group ALTER changed_at SET NOT NULL');
        $this->addSql('ALTER TABLE project_settings ALTER default_workitem_status_id SET NOT NULL');
    }
}
