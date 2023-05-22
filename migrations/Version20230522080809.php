<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522080809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A45E237E06 ON account (name)');
        $this->addSql('ALTER TABLE player_profession DROP FOREIGN KEY FK_E536895499E6F5DF');
        $this->addSql('ALTER TABLE player_profession ADD CONSTRAINT FK_E536895499E6F5DF FOREIGN KEY (player_id) REFERENCES account (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_7D3656A45E237E06 ON account');
        $this->addSql('ALTER TABLE player_profession DROP FOREIGN KEY FK_E536895499E6F5DF');
        $this->addSql('ALTER TABLE player_profession ADD CONSTRAINT FK_E536895499E6F5DF FOREIGN KEY (player_id) REFERENCES account (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
