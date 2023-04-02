<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230402083422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, code VARCHAR(10) NOT NULL, created_at DATETIME DEFAULT NULL, created_by VARCHAR(32) DEFAULT NULL, modified_by VARCHAR(32) DEFAULT NULL, modified_at DATETIME DEFAULT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prime (id INT AUTO_INCREMENT NOT NULL, competition_id_id INT NOT NULL, name VARCHAR(32) NOT NULL, is_active TINYINT(1) NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by VARCHAR(32) DEFAULT NULL, modified_at DATETIME DEFAULT NULL, modified_by VARCHAR(32) DEFAULT NULL, INDEX IDX_544B0F578CF3AC81 (competition_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_data (id INT AUTO_INCREMENT NOT NULL, tutor_id_id INT DEFAULT NULL, name VARCHAR(64) NOT NULL, profile_photo VARCHAR(255) DEFAULT NULL, email VARCHAR(64) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, user_type VARCHAR(10) NOT NULL, registered_by VARCHAR(64) DEFAULT NULL, bio VARCHAR(255) DEFAULT NULL, birth_date DATE DEFAULT NULL, is_active TINYINT(1) NOT NULL, username VARCHAR(64) NOT NULL, province VARCHAR(32) DEFAULT NULL, address VARCHAR(128) DEFAULT NULL, status VARCHAR(10) DEFAULT NULL, gender VARCHAR(10) DEFAULT NULL, device_token VARCHAR(255) DEFAULT NULL, uid VARCHAR(128) DEFAULT NULL, INDEX IDX_D772BFAAAED1ECE5 (tutor_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video_data (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, username VARCHAR(64) NOT NULL, share_count INT DEFAULT NULL, style VARCHAR(64) DEFAULT NULL, video_url VARCHAR(255) NOT NULL, thumbnail VARCHAR(255) DEFAULT NULL, profile_photo VARCHAR(255) DEFAULT NULL, likes LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', views LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', status VARCHAR(10) DEFAULT NULL, prime_id INT NOT NULL, created_at DATETIME DEFAULT NULL, song_name VARCHAR(64) DEFAULT NULL, INDEX IDX_AA64AEBF9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prime ADD CONSTRAINT FK_544B0F578CF3AC81 FOREIGN KEY (competition_id_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE user_data ADD CONSTRAINT FK_D772BFAAAED1ECE5 FOREIGN KEY (tutor_id_id) REFERENCES tutor (id)');
        $this->addSql('ALTER TABLE video_data ADD CONSTRAINT FK_AA64AEBF9D86650F FOREIGN KEY (user_id_id) REFERENCES user_data (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prime DROP FOREIGN KEY FK_544B0F578CF3AC81');
        $this->addSql('ALTER TABLE user_data DROP FOREIGN KEY FK_D772BFAAAED1ECE5');
        $this->addSql('ALTER TABLE video_data DROP FOREIGN KEY FK_AA64AEBF9D86650F');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE prime');
        $this->addSql('DROP TABLE user_data');
        $this->addSql('DROP TABLE video_data');
    }
}
