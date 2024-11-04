<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104181417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add animator to table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animator (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(180) NOT NULL, last_name VARCHAR(180) NOT NULL, picture VARCHAR(200) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_60BF9208A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animator ADD CONSTRAINT FK_60BF9208A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animator DROP FOREIGN KEY FK_60BF9208A76ED395');
        $this->addSql('DROP TABLE animator');
    }
}
