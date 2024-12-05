<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205080538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_message ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE activity_message ADD CONSTRAINT FK_B6EF469FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B6EF469FA76ED395 ON activity_message (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_message DROP FOREIGN KEY FK_B6EF469FA76ED395');
        $this->addSql('DROP INDEX IDX_B6EF469FA76ED395 ON activity_message');
        $this->addSql('ALTER TABLE activity_message DROP user_id');
    }
}
