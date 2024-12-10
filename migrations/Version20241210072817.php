<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210072817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD is_crud_create TINYINT(1) DEFAULT NULL, ADD is_crud_edit TINYINT(1) DEFAULT NULL, ADD is_crud_delete TINYINT(1) DEFAULT NULL, ADD is_crud_event_canceler TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP is_crud_create, DROP is_crud_edit, DROP is_crud_delete, DROP is_crud_event_canceler');
    }
}
