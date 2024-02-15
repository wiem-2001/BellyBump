<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215220854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit ADD id_partner_id INT NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2736197829 FOREIGN KEY (id_partner_id) REFERENCES partenaire (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC2736197829 ON produit (id_partner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2736197829');
        $this->addSql('DROP INDEX IDX_29A5EC2736197829 ON produit');
        $this->addSql('ALTER TABLE produit DROP id_partner_id');
    }
}
