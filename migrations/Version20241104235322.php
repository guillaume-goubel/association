<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104235322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD last_user_update_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7BB716ED1 FOREIGN KEY (last_user_update_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7BB716ED1 ON event (last_user_update_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7BB716ED1');
        $this->addSql('DROP INDEX IDX_3BAE0AA7BB716ED1 ON event');
        $this->addSql('ALTER TABLE event DROP last_user_update_id');
    }
}
