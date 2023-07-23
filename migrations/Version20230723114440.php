<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723114440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD birth_date DATE DEFAULT NULL, ADD is_active TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE vote_mode ADD competition_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vote_mode ADD CONSTRAINT FK_20A8B69A7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('CREATE INDEX IDX_20A8B69A7B39D312 ON vote_mode (competition_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP birth_date, DROP is_active');
        $this->addSql('ALTER TABLE vote_mode DROP FOREIGN KEY FK_20A8B69A7B39D312');
        $this->addSql('DROP INDEX IDX_20A8B69A7B39D312 ON vote_mode');
        $this->addSql('ALTER TABLE vote_mode DROP competition_id');
    }
}
