<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105084945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'edit event entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD is_enabled TINYINT(1) NOT NULL, ADD main_picture VARCHAR(255) DEFAULT NULL, ADD picture2 VARCHAR(255) DEFAULT NULL, ADD picture3 VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP is_enabled, DROP main_picture, DROP picture2, DROP picture3');
    }
}
