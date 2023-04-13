<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230408173319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch ADD default_item_id INT NOT NULL');
        $this->addSql('ALTER TABLE batch ADD CONSTRAINT FK_F80B52D4E0EA031B FOREIGN KEY (default_item_id) REFERENCES default_item (id)');
        $this->addSql('CREATE INDEX IDX_F80B52D4E0EA031B ON batch (default_item_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch DROP FOREIGN KEY FK_F80B52D4E0EA031B');
        $this->addSql('DROP INDEX IDX_F80B52D4E0EA031B ON batch');
        $this->addSql('ALTER TABLE batch DROP default_item_id');
    }
}
