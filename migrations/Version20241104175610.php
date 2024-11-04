<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104175610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add Event table part 1 + update activity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, preparation_infos LONGTEXT DEFAULT NULL, event_distance VARCHAR(25) DEFAULT NULL, rdv_place_name VARCHAR(150) DEFAULT NULL, rdv_latitude VARCHAR(150) DEFAULT NULL, rdv_longitude VARCHAR(150) DEFAULT NULL, time_start_at TIME DEFAULT NULL, time_end_at TIME DEFAULT NULL, date_start_at DATE DEFAULT NULL, date_end_at DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD type VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE event');
        $this->addSql('ALTER TABLE activity DROP type');
    }
}
