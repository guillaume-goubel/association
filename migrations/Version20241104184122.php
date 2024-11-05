<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104184122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add event animator relations';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_animator (event_id INT NOT NULL, animator_id INT NOT NULL, INDEX IDX_26537BD871F7E88B (event_id), INDEX IDX_26537BD870FBD26D (animator_id), PRIMARY KEY(event_id, animator_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_animator ADD CONSTRAINT FK_26537BD871F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_animator ADD CONSTRAINT FK_26537BD870FBD26D FOREIGN KEY (animator_id) REFERENCES animator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event ADD activity_id INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA781C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA781C06096 ON event (activity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_animator DROP FOREIGN KEY FK_26537BD871F7E88B');
        $this->addSql('ALTER TABLE event_animator DROP FOREIGN KEY FK_26537BD870FBD26D');
        $this->addSql('DROP TABLE event_animator');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA781C06096');
        $this->addSql('DROP INDEX IDX_3BAE0AA781C06096 ON event');
        $this->addSql('ALTER TABLE event DROP activity_id');
    }
}
