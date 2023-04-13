<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230412131111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bug_report_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bug_report ADD bug_report_status_id INT DEFAULT NULL, ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7A8FD31A08 FOREIGN KEY (bug_report_status_id) REFERENCES bug_report_status (id)');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F6F2DC7A8FD31A08 ON bug_report (bug_report_status_id)');
        $this->addSql('CREATE INDEX IDX_F6F2DC7AA76ED395 ON bug_report (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report DROP FOREIGN KEY FK_F6F2DC7A8FD31A08');
        $this->addSql('DROP TABLE bug_report_status');
        $this->addSql('ALTER TABLE bug_report DROP FOREIGN KEY FK_F6F2DC7AA76ED395');
        $this->addSql('DROP INDEX IDX_F6F2DC7A8FD31A08 ON bug_report');
        $this->addSql('DROP INDEX IDX_F6F2DC7AA76ED395 ON bug_report');
        $this->addSql('ALTER TABLE bug_report DROP bug_report_status_id, DROP user_id');
    }
}
