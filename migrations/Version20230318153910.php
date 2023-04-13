<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230318153910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profession_experience (id INT AUTO_INCREMENT NOT NULL, level INT NOT NULL, exp INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe ADD profession_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137FDEF8996 FOREIGN KEY (profession_id) REFERENCES profession (id)');
        $this->addSql('CREATE INDEX IDX_DA88B137FDEF8996 ON recipe (profession_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE profession_experience');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137FDEF8996');
        $this->addSql('DROP INDEX IDX_DA88B137FDEF8996 ON recipe');
        $this->addSql('ALTER TABLE recipe DROP profession_id');
    }
}
