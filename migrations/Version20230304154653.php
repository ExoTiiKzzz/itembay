<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230304154653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account ADD class_id INT NOT NULL');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4EA000B10 FOREIGN KEY (class_id) REFERENCES player_class (id)');
        $this->addSql('CREATE INDEX IDX_7D3656A4EA000B10 ON account (class_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4EA000B10');
        $this->addSql('DROP INDEX IDX_7D3656A4EA000B10 ON account');
        $this->addSql('ALTER TABLE account DROP class_id');
    }
}
