<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230408203424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch ADD account_id INT NOT NULL');
        $this->addSql('ALTER TABLE batch ADD CONSTRAINT FK_F80B52D49B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('CREATE INDEX IDX_F80B52D49B6B5FBA ON batch (account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch DROP FOREIGN KEY FK_F80B52D49B6B5FBA');
        $this->addSql('DROP INDEX IDX_F80B52D49B6B5FBA ON batch');
        $this->addSql('ALTER TABLE batch DROP account_id');
    }
}
