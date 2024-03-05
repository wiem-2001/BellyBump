<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305154747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE med ADD info_medicaux_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE med ADD CONSTRAINT FK_ED1BBBC2A9559549 FOREIGN KEY (info_medicaux_id) REFERENCES info_medicaux (id)');
        $this->addSql('CREATE INDEX IDX_ED1BBBC2A9559549 ON med (info_medicaux_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE med DROP FOREIGN KEY FK_ED1BBBC2A9559549');
        $this->addSql('DROP INDEX IDX_ED1BBBC2A9559549 ON med');
        $this->addSql('ALTER TABLE med DROP info_medicaux_id');
    }
}
