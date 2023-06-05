<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522090536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trade (id INT AUTO_INCREMENT NOT NULL, first_account_id INT NOT NULL, second_account_id INT NOT NULL, topic VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_7E1A4366AF33EBD (first_account_id), INDEX IDX_7E1A43667835BF29 (second_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A4366AF33EBD FOREIGN KEY (first_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A43667835BF29 FOREIGN KEY (second_account_id) REFERENCES account (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A4366AF33EBD');
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A43667835BF29');
        $this->addSql('DROP TABLE trade');
    }
}
