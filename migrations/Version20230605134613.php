<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230605134613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE loot_box_opening (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, loot_box_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F6F580CA9B6B5FBA (account_id), INDEX IDX_F6F580CA62D7833 (loot_box_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE loot_box_opening ADD CONSTRAINT FK_F6F580CA9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE loot_box_opening ADD CONSTRAINT FK_F6F580CA62D7833 FOREIGN KEY (loot_box_id) REFERENCES loot_box (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loot_box_opening DROP FOREIGN KEY FK_F6F580CA9B6B5FBA');
        $this->addSql('ALTER TABLE loot_box_opening DROP FOREIGN KEY FK_F6F580CA62D7833');
        $this->addSql('DROP TABLE loot_box_opening');
    }
}
