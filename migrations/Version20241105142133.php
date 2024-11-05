<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105142133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'edit event entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD rdv_address VARCHAR(180) DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE rdv_place_name rdv_place_name VARCHAR(150) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP rdv_address, CHANGE name name VARCHAR(255) NOT NULL, CHANGE rdv_place_name rdv_place_name VARCHAR(150) DEFAULT NULL');
    }
}
