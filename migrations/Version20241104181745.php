<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104181745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add animator in event table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD animator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA770FBD26D FOREIGN KEY (animator_id) REFERENCES animator (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA770FBD26D ON event (animator_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA770FBD26D');
        $this->addSql('DROP INDEX IDX_3BAE0AA770FBD26D ON event');
        $this->addSql('ALTER TABLE event DROP animator_id');
    }
}
