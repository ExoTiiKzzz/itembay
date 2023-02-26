<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230225135426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_nature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE default_item ADD item_nature_id INT NOT NULL');
        $this->addSql('ALTER TABLE default_item ADD CONSTRAINT FK_5AEA1E18371138BB FOREIGN KEY (item_nature_id) REFERENCES item_nature (id)');
        $this->addSql('CREATE INDEX IDX_5AEA1E18371138BB ON default_item (item_nature_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE default_item DROP FOREIGN KEY FK_5AEA1E18371138BB');
        $this->addSql('DROP TABLE item_nature');
        $this->addSql('DROP INDEX IDX_5AEA1E18371138BB ON default_item');
        $this->addSql('ALTER TABLE default_item DROP item_nature_id');
    }
}
