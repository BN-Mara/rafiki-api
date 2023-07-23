<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723083406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, vote_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, currency VARCHAR(4) NOT NULL, reference VARCHAR(13) NOT NULL, status VARCHAR(10) DEFAULT NULL, created_at DATETIME DEFAULT NULL, email VARCHAR(64) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, username VARCHAR(32) DEFAULT NULL, UNIQUE INDEX UNIQ_6D28840D72DCDAFC (vote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, artist_id INT NOT NULL, prime_id INT NOT NULL, number_of_vote INT NOT NULL, is_payed TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_5A108564B7970CF8 (artist_id), INDEX IDX_5A10856469247986 (prime_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D72DCDAFC FOREIGN KEY (vote_id) REFERENCES vote (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856469247986 FOREIGN KEY (prime_id) REFERENCES prime (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D72DCDAFC');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564B7970CF8');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856469247986');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE vote');
    }
}
