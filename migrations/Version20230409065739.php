<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409065739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE batch_user_basket (batch_id INT NOT NULL, user_basket_id INT NOT NULL, INDEX IDX_B30F09BBF39EBE7A (batch_id), INDEX IDX_B30F09BB1B0176B6 (user_basket_id), PRIMARY KEY(batch_id, user_basket_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE batch_user_basket ADD CONSTRAINT FK_B30F09BBF39EBE7A FOREIGN KEY (batch_id) REFERENCES batch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE batch_user_basket ADD CONSTRAINT FK_B30F09BB1B0176B6 FOREIGN KEY (user_basket_id) REFERENCES user_basket (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch_user_basket DROP FOREIGN KEY FK_B30F09BBF39EBE7A');
        $this->addSql('ALTER TABLE batch_user_basket DROP FOREIGN KEY FK_B30F09BB1B0176B6');
        $this->addSql('DROP TABLE batch_user_basket');
    }
}
