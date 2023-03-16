<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316085231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player_profession (id INT AUTO_INCREMENT NOT NULL, profession_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_E5368954FDEF8996 (profession_id), INDEX IDX_E536895499E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profession (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, ankama_id INT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE player_profession ADD CONSTRAINT FK_E5368954FDEF8996 FOREIGN KEY (profession_id) REFERENCES profession (id)');
        $this->addSql('ALTER TABLE player_profession ADD CONSTRAINT FK_E536895499E6F5DF FOREIGN KEY (player_id) REFERENCES account (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_profession DROP FOREIGN KEY FK_E5368954FDEF8996');
        $this->addSql('ALTER TABLE player_profession DROP FOREIGN KEY FK_E536895499E6F5DF');
        $this->addSql('DROP TABLE player_profession');
        $this->addSql('DROP TABLE profession');
    }
}
