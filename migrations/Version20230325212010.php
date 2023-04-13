<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325212010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report ADD type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7AC54C8C93 FOREIGN KEY (type_id) REFERENCES bug_report_type (id)');
        $this->addSql('CREATE INDEX IDX_F6F2DC7AC54C8C93 ON bug_report (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report DROP FOREIGN KEY FK_F6F2DC7AC54C8C93');
        $this->addSql('DROP INDEX IDX_F6F2DC7AC54C8C93 ON bug_report');
        $this->addSql('ALTER TABLE bug_report DROP type_id');
    }
}
