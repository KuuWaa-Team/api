<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240131020703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, province_id INT DEFAULT NULL, country_id INT DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, number VARCHAR(255) DEFAULT NULL, ref VARCHAR(255) DEFAULT NULL, INDEX IDX_D4E6F81E946114A (province_id), INDEX IDX_D4E6F81F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE beverage (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, count_taken INT DEFAULT NULL, INDEX IDX_3D8CACBB71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, invitation_id INT DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, INDEX IDX_CBE5A33171F7E88B (event_id), INDEX IDX_CBE5A331A35D7AF0 (invitation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, phone_initial VARCHAR(255) DEFAULT NULL, short_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_feature (event_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_9A80DCC971F7E88B (event_id), INDEX IDX_9A80DCC960E4B879 (feature_id), PRIMARY KEY(event_id, feature_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_admin (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_C915BC5A71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, price NUMERIC(10, 0) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, amount NUMERIC(10, 0) DEFAULT NULL, ref VARCHAR(255) DEFAULT NULL, date DATE DEFAULT NULL, order_number VARCHAR(255) DEFAULT NULL, payment_method VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, currency VARCHAR(255) DEFAULT NULL, INDEX IDX_6D28840D71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_4ADAD40BF92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, ticket_type_id INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_97A0ADA371F7E88B (event_id), INDEX IDX_97A0ADA3C980D5C1 (ticket_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket_type (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, price NUMERIC(10, 2) DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, INDEX IDX_BE05421171F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE beverage ADD CONSTRAINT FK_3D8CACBB71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331A35D7AF0 FOREIGN KEY (invitation_id) REFERENCES invitation (id)');
        $this->addSql('ALTER TABLE event_feature ADD CONSTRAINT FK_9A80DCC971F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_feature ADD CONSTRAINT FK_9A80DCC960E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_admin ADD CONSTRAINT FK_C915BC5A71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE province ADD CONSTRAINT FK_4ADAD40BF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3C980D5C1 FOREIGN KEY (ticket_type_id) REFERENCES ticket_type (id)');
        $this->addSql('ALTER TABLE ticket_type ADD CONSTRAINT FK_BE05421171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE event ADD address_id INT DEFAULT NULL, ADD paid_event TINYINT(1) DEFAULT NULL, ADD payment_status VARCHAR(255) DEFAULT NULL, ADD image_path VARCHAR(255) DEFAULT NULL, ADD slug VARCHAR(255) DEFAULT NULL, ADD tables_count INT DEFAULT NULL, ADD invitations_count INT DEFAULT NULL, ADD beverages_count INT DEFAULT NULL, ADD admins_count INT DEFAULT NULL, DROP start_hour, DROP end_hour, DROP address, CHANGE date date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3BAE0AA7F5B7AF75 ON event (address_id)');
        $this->addSql('ALTER TABLE event_category ADD paid TINYINT(1) DEFAULT NULL, ADD validated TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE event_pack ADD paid TINYINT(1) DEFAULT NULL, ADD pack_order INT DEFAULT NULL, DROP features, CHANGE max_tickets max_tickets INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invitation ADD invitation_table_id INT DEFAULT NULL, ADD beverage_id INT DEFAULT NULL, ADD phone_number VARCHAR(255) NOT NULL, ADD status VARCHAR(255) DEFAULT NULL, ADD code_path VARCHAR(255) DEFAULT NULL, ADD code VARCHAR(255) DEFAULT NULL, ADD book_sent TINYINT(1) DEFAULT NULL, ADD message LONGTEXT DEFAULT NULL, CHANGE count count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2CCEB50C1 FOREIGN KEY (invitation_table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A249F6E812 FOREIGN KEY (beverage_id) REFERENCES beverage (id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2CCEB50C1 ON invitation (invitation_table_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A249F6E812 ON invitation (beverage_id)');
        $this->addSql('ALTER TABLE `table` ADD count_taken INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7F5B7AF75');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A249F6E812');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81E946114A');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81F92F3E70');
        $this->addSql('ALTER TABLE beverage DROP FOREIGN KEY FK_3D8CACBB71F7E88B');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33171F7E88B');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331A35D7AF0');
        $this->addSql('ALTER TABLE event_feature DROP FOREIGN KEY FK_9A80DCC971F7E88B');
        $this->addSql('ALTER TABLE event_feature DROP FOREIGN KEY FK_9A80DCC960E4B879');
        $this->addSql('ALTER TABLE event_admin DROP FOREIGN KEY FK_C915BC5A71F7E88B');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D71F7E88B');
        $this->addSql('ALTER TABLE province DROP FOREIGN KEY FK_4ADAD40BF92F3E70');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA371F7E88B');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3C980D5C1');
        $this->addSql('ALTER TABLE ticket_type DROP FOREIGN KEY FK_BE05421171F7E88B');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE beverage');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE event_feature');
        $this->addSql('DROP TABLE event_admin');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE ticket_type');
        $this->addSql('ALTER TABLE `table` DROP count_taken');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A2CCEB50C1');
        $this->addSql('DROP INDEX IDX_F11D61A2CCEB50C1 ON invitation');
        $this->addSql('DROP INDEX IDX_F11D61A249F6E812 ON invitation');
        $this->addSql('ALTER TABLE invitation DROP invitation_table_id, DROP beverage_id, DROP phone_number, DROP status, DROP code_path, DROP code, DROP book_sent, DROP message, CHANGE count count INT NOT NULL');
        $this->addSql('ALTER TABLE event_pack ADD features JSON NOT NULL, DROP paid, DROP pack_order, CHANGE max_tickets max_tickets INT NOT NULL');
        $this->addSql('ALTER TABLE event_category DROP paid, DROP validated');
        $this->addSql('DROP INDEX UNIQ_3BAE0AA7F5B7AF75 ON event');
        $this->addSql('ALTER TABLE event ADD start_hour TIME NOT NULL, ADD end_hour TIME DEFAULT NULL, ADD address VARCHAR(255) NOT NULL, DROP address_id, DROP paid_event, DROP payment_status, DROP image_path, DROP slug, DROP tables_count, DROP invitations_count, DROP beverages_count, DROP admins_count, CHANGE date date DATE NOT NULL');
    }
}
