<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240105143840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD event_pack_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA79C4E1BB8 FOREIGN KEY (event_pack_id) REFERENCES event_pack (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA79C4E1BB8 ON event (event_pack_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA79C4E1BB8');
        $this->addSql('DROP INDEX IDX_3BAE0AA79C4E1BB8 ON event');
        $this->addSql('ALTER TABLE event DROP event_pack_id');
    }
}
