<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210073212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'not null change';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE is_crud_create is_crud_create TINYINT(1) NOT NULL, CHANGE is_crud_edit is_crud_edit TINYINT(1) NOT NULL, CHANGE is_crud_delete is_crud_delete TINYINT(1) NOT NULL, CHANGE is_crud_event_canceler is_crud_event_canceler TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE is_crud_create is_crud_create TINYINT(1) DEFAULT NULL, CHANGE is_crud_edit is_crud_edit TINYINT(1) DEFAULT NULL, CHANGE is_crud_delete is_crud_delete TINYINT(1) DEFAULT NULL, CHANGE is_crud_event_canceler is_crud_event_canceler TINYINT(1) DEFAULT NULL');
    }
}
