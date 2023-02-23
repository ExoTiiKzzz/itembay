<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223131837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // drop foreign key
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EE0EA031B');
        $this->addSql('ALTER TABLE default_item CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EE0EA031B FOREIGN KEY (default_item_id) REFERENCES default_item (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE default_item CHANGE id id INT NOT NULL');
    }
}
