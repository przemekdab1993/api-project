<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229075141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_api ADD uuid CHAR(36) DEFAULT NULL');
        $this->addSql('UPDATE user_api SET uuid = gen_random_uuid()');
        $this->addSql('ALTER TABLE user_api ALTER uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN user_api.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4613B984D17F50A6 ON user_api (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_4613B984D17F50A6');
        $this->addSql('ALTER TABLE user_api DROP uuid');
    }
}
