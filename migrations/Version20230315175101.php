<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315175101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, class_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_7D3656A4A76ED395 (user_id), INDEX IDX_7D3656A4EA000B10 (class_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE default_item (id INT AUTO_INCREMENT NOT NULL, item_type_id INT DEFAULT NULL, item_nature_id INT NOT NULL, ankama_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', buy_price INT NOT NULL, sell_price INT NOT NULL, description LONGTEXT DEFAULT NULL, level INT NOT NULL, UNIQUE INDEX UNIQ_5AEA1E18D17F50A6 (uuid), INDEX IDX_5AEA1E18CE11AAC7 (item_type_id), INDEX IDX_5AEA1E18371138BB (item_nature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, default_item_id INT NOT NULL, account_id INT DEFAULT NULL, buy_price INT NOT NULL, sell_price INT NOT NULL, is_default_item TINYINT(1) NOT NULL, INDEX IDX_1F1B251EE0EA031B (default_item_id), INDEX IDX_1F1B251E9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_nature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_type (id INT AUTO_INCREMENT NOT NULL, item_nature_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_44EE13D2371138BB (item_nature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_class (id INT AUTO_INCREMENT NOT NULL, ankama_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_class_item_type (player_class_id INT NOT NULL, item_type_id INT NOT NULL, INDEX IDX_F62B0FA6ECD74AF0 (player_class_id), INDEX IDX_F62B0FA6CE11AAC7 (item_type_id), PRIMARY KEY(player_class_id, item_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, item_id INT NOT NULL, note INT NOT NULL, comment LONGTEXT NOT NULL, INDEX IDX_794381C69B6B5FBA (account_id), INDEX IDX_794381C6126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_723705D19B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction_line (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, transaction_id INT NOT NULL, price INT NOT NULL, INDEX IDX_33578A57126F525E (item_id), INDEX IDX_33578A572FC0CB0F (transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, active_account_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, avatar VARCHAR(255) NOT NULL, roles JSON NOT NULL, money INT NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649BAC93D4C (active_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_basket (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_47144B46A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_basket_item (user_basket_id INT NOT NULL, item_id INT NOT NULL, INDEX IDX_7D8FF5BE1B0176B6 (user_basket_id), INDEX IDX_7D8FF5BE126F525E (item_id), PRIMARY KEY(user_basket_id, item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4EA000B10 FOREIGN KEY (class_id) REFERENCES player_class (id)');
        $this->addSql('ALTER TABLE default_item ADD CONSTRAINT FK_5AEA1E18CE11AAC7 FOREIGN KEY (item_type_id) REFERENCES item_type (id)');
        $this->addSql('ALTER TABLE default_item ADD CONSTRAINT FK_5AEA1E18371138BB FOREIGN KEY (item_nature_id) REFERENCES item_nature (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EE0EA031B FOREIGN KEY (default_item_id) REFERENCES default_item (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE item_type ADD CONSTRAINT FK_44EE13D2371138BB FOREIGN KEY (item_nature_id) REFERENCES item_nature (id)');
        $this->addSql('ALTER TABLE player_class_item_type ADD CONSTRAINT FK_F62B0FA6ECD74AF0 FOREIGN KEY (player_class_id) REFERENCES player_class (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_class_item_type ADD CONSTRAINT FK_F62B0FA6CE11AAC7 FOREIGN KEY (item_type_id) REFERENCES item_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C69B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6126F525E FOREIGN KEY (item_id) REFERENCES default_item (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transaction_line ADD CONSTRAINT FK_33578A57126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE transaction_line ADD CONSTRAINT FK_33578A572FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BAC93D4C FOREIGN KEY (active_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE user_basket ADD CONSTRAINT FK_47144B46A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_basket_item ADD CONSTRAINT FK_7D8FF5BE1B0176B6 FOREIGN KEY (user_basket_id) REFERENCES user_basket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_basket_item ADD CONSTRAINT FK_7D8FF5BE126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4A76ED395');
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4EA000B10');
        $this->addSql('ALTER TABLE default_item DROP FOREIGN KEY FK_5AEA1E18CE11AAC7');
        $this->addSql('ALTER TABLE default_item DROP FOREIGN KEY FK_5AEA1E18371138BB');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EE0EA031B');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E9B6B5FBA');
        $this->addSql('ALTER TABLE item_type DROP FOREIGN KEY FK_44EE13D2371138BB');
        $this->addSql('ALTER TABLE player_class_item_type DROP FOREIGN KEY FK_F62B0FA6ECD74AF0');
        $this->addSql('ALTER TABLE player_class_item_type DROP FOREIGN KEY FK_F62B0FA6CE11AAC7');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C69B6B5FBA');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6126F525E');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19B6B5FBA');
        $this->addSql('ALTER TABLE transaction_line DROP FOREIGN KEY FK_33578A57126F525E');
        $this->addSql('ALTER TABLE transaction_line DROP FOREIGN KEY FK_33578A572FC0CB0F');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BAC93D4C');
        $this->addSql('ALTER TABLE user_basket DROP FOREIGN KEY FK_47144B46A76ED395');
        $this->addSql('ALTER TABLE user_basket_item DROP FOREIGN KEY FK_7D8FF5BE1B0176B6');
        $this->addSql('ALTER TABLE user_basket_item DROP FOREIGN KEY FK_7D8FF5BE126F525E');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE default_item');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE item_nature');
        $this->addSql('DROP TABLE item_type');
        $this->addSql('DROP TABLE player_class');
        $this->addSql('DROP TABLE player_class_item_type');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE transaction_line');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_basket');
        $this->addSql('DROP TABLE user_basket_item');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
