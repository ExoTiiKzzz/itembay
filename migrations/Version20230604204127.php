<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230604204127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loot_box_line ADD loot_box_id INT NOT NULL');
        $this->addSql('ALTER TABLE loot_box_line ADD CONSTRAINT FK_12CC387362D7833 FOREIGN KEY (loot_box_id) REFERENCES loot_box (id)');
        $this->addSql('CREATE INDEX IDX_12CC387362D7833 ON loot_box_line (loot_box_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loot_box_line DROP FOREIGN KEY FK_12CC387362D7833');
        $this->addSql('DROP INDEX IDX_12CC387362D7833 ON loot_box_line');
        $this->addSql('ALTER TABLE loot_box_line DROP loot_box_id');
    }
}
