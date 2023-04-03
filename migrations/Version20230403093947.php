<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230403093947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification ADD type VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_data ADD language VARCHAR(5) DEFAULT NULL, ADD church_file VARCHAR(255) NOT NULL, ADD church_name VARCHAR(100) DEFAULT NULL, ADD church_address VARCHAR(128) DEFAULT NULL, ADD pastor_name VARCHAR(64) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP type');
        $this->addSql('ALTER TABLE user_data DROP language, DROP church_file, DROP church_name, DROP church_address, DROP pastor_name');
    }
}
