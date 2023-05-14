<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505193449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account_account (account_source INT NOT NULL, account_target INT NOT NULL, INDEX IDX_B41D42EC78BEB100 (account_source), INDEX IDX_B41D42EC615BE18F (account_target), PRIMARY KEY(account_source, account_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE private_message (id INT AUTO_INCREMENT NOT NULL, from_account_id INT NOT NULL, to_account_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4744FC9BB0CF99BD (from_account_id), INDEX IDX_4744FC9BBC58BDC7 (to_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account_account ADD CONSTRAINT FK_B41D42EC78BEB100 FOREIGN KEY (account_source) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE account_account ADD CONSTRAINT FK_B41D42EC615BE18F FOREIGN KEY (account_target) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BB0CF99BD FOREIGN KEY (from_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BBC58BDC7 FOREIGN KEY (to_account_id) REFERENCES account (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account_account DROP FOREIGN KEY FK_B41D42EC78BEB100');
        $this->addSql('ALTER TABLE account_account DROP FOREIGN KEY FK_B41D42EC615BE18F');
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9BB0CF99BD');
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9BBC58BDC7');
        $this->addSql('DROP TABLE account_account');
        $this->addSql('DROP TABLE private_message');
    }
}
