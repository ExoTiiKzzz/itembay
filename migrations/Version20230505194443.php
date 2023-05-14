<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505194443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discussion (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discussion_account (discussion_id INT NOT NULL, account_id INT NOT NULL, INDEX IDX_F56870191ADED311 (discussion_id), INDEX IDX_F56870199B6B5FBA (account_id), PRIMARY KEY(discussion_id, account_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discussion_account ADD CONSTRAINT FK_F56870191ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discussion_account ADD CONSTRAINT FK_F56870199B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9BBC58BDC7');
        $this->addSql('DROP INDEX IDX_4744FC9BBC58BDC7 ON private_message');
        $this->addSql('ALTER TABLE private_message CHANGE to_account_id discussion_id INT NOT NULL');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9B1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id)');
        $this->addSql('CREATE INDEX IDX_4744FC9B1ADED311 ON private_message (discussion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9B1ADED311');
        $this->addSql('ALTER TABLE discussion_account DROP FOREIGN KEY FK_F56870191ADED311');
        $this->addSql('ALTER TABLE discussion_account DROP FOREIGN KEY FK_F56870199B6B5FBA');
        $this->addSql('DROP TABLE discussion');
        $this->addSql('DROP TABLE discussion_account');
        $this->addSql('DROP INDEX IDX_4744FC9B1ADED311 ON private_message');
        $this->addSql('ALTER TABLE private_message CHANGE discussion_id to_account_id INT NOT NULL');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BBC58BDC7 FOREIGN KEY (to_account_id) REFERENCES account (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4744FC9BBC58BDC7 ON private_message (to_account_id)');
    }
}
