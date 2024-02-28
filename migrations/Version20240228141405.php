<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228141405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE baby (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, sexe VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, poids DOUBLE PRECISION NOT NULL, taille DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_medicaux (id INT AUTO_INCREMENT NOT NULL, baby_name_id INT DEFAULT NULL, maladie VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, nbr_vaccin INT NOT NULL, date_vaccin DATE NOT NULL, blood_type VARCHAR(255) NOT NULL, sickness_estimation VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8B20A71CC7897E01 (baby_name_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE med (id INT AUTO_INCREMENT NOT NULL, info_medicaux_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, specialite VARCHAR(255) NOT NULL, INDEX IDX_ED1BBBC2A9559549 (info_medicaux_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE info_medicaux ADD CONSTRAINT FK_8B20A71CC7897E01 FOREIGN KEY (baby_name_id) REFERENCES baby (id)');
        $this->addSql('ALTER TABLE med ADD CONSTRAINT FK_ED1BBBC2A9559549 FOREIGN KEY (info_medicaux_id) REFERENCES info_medicaux (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE info_medicaux DROP FOREIGN KEY FK_8B20A71CC7897E01');
        $this->addSql('ALTER TABLE med DROP FOREIGN KEY FK_ED1BBBC2A9559549');
        $this->addSql('DROP TABLE baby');
        $this->addSql('DROP TABLE info_medicaux');
        $this->addSql('DROP TABLE med');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
