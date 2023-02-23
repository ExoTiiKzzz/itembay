<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222192948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_class (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_class_item_type (player_class_id INT NOT NULL, item_type_id INT NOT NULL, INDEX IDX_F62B0FA6ECD74AF0 (player_class_id), INDEX IDX_F62B0FA6CE11AAC7 (item_type_id), PRIMARY KEY(player_class_id, item_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_class_item_type ADD CONSTRAINT FK_F62B0FA6ECD74AF0 FOREIGN KEY (player_class_id) REFERENCES player_class (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_class_item_type ADD CONSTRAINT FK_F62B0FA6CE11AAC7 FOREIGN KEY (item_type_id) REFERENCES item_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE default_item ADD item_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE default_item ADD CONSTRAINT FK_5AEA1E18CE11AAC7 FOREIGN KEY (item_type_id) REFERENCES item_type (id)');
        $this->addSql('CREATE INDEX IDX_5AEA1E18CE11AAC7 ON default_item (item_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE default_item DROP FOREIGN KEY FK_5AEA1E18CE11AAC7');
        $this->addSql('ALTER TABLE player_class_item_type DROP FOREIGN KEY FK_F62B0FA6ECD74AF0');
        $this->addSql('ALTER TABLE player_class_item_type DROP FOREIGN KEY FK_F62B0FA6CE11AAC7');
        $this->addSql('DROP TABLE item_type');
        $this->addSql('DROP TABLE player_class');
        $this->addSql('DROP TABLE player_class_item_type');
        $this->addSql('DROP INDEX IDX_5AEA1E18CE11AAC7 ON default_item');
        $this->addSql('ALTER TABLE default_item DROP item_type_id');
    }
}
