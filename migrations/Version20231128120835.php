<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128120835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE payment_token');
        $this->addSql('ALTER TABLE payment DROP number, DROP description, DROP client_email, DROP client_id, DROP total_amount, DROP currency_code, DROP details');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment_token (hash VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, details LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:object)\', after_url LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, target_url LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, gateway_name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(hash)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE payment ADD number VARCHAR(255) DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL, ADD client_email VARCHAR(255) DEFAULT NULL, ADD client_id VARCHAR(255) DEFAULT NULL, ADD total_amount INT DEFAULT NULL, ADD currency_code VARCHAR(255) DEFAULT NULL, ADD details LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
