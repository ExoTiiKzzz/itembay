<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316133428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE default_item ADD profession_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE default_item ADD CONSTRAINT FK_5AEA1E18FDEF8996 FOREIGN KEY (profession_id) REFERENCES profession (id)');
        $this->addSql('CREATE INDEX IDX_5AEA1E18FDEF8996 ON default_item (profession_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE default_item DROP FOREIGN KEY FK_5AEA1E18FDEF8996');
        $this->addSql('DROP INDEX IDX_5AEA1E18FDEF8996 ON default_item');
        $this->addSql('ALTER TABLE default_item DROP profession_id');
    }
}
