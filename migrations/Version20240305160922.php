<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305160922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE med DROP INDEX IDX_ED1BBBC2A9559549, ADD UNIQUE INDEX UNIQ_ED1BBBC2A9559549 (info_medicaux_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE med DROP INDEX UNIQ_ED1BBBC2A9559549, ADD INDEX IDX_ED1BBBC2A9559549 (info_medicaux_id)');
    }
}
