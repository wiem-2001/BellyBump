<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306100150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE baby (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, sexe VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, poids DOUBLE PRECISION NOT NULL, taille DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etab (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, localisation VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_medicaux (id INT AUTO_INCREMENT NOT NULL, baby_name_id INT DEFAULT NULL, maladie VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, nbr_vaccin INT NOT NULL, date_vaccin DATE NOT NULL, blood_type VARCHAR(255) NOT NULL, sickness_estimation VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8B20A71CC7897E01 (baby_name_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE med (id INT AUTO_INCREMENT NOT NULL, etab_id INT DEFAULT NULL, info_medicaux_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, specialite VARCHAR(255) NOT NULL, INDEX IDX_ED1BBBC2782C8EBC (etab_id), UNIQUE INDEX UNIQ_ED1BBBC2A9559549 (info_medicaux_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rendez_vous (id INT AUTO_INCREMENT NOT NULL, nom_med_id INT DEFAULT NULL, date_reservation DATE NOT NULL, heure_reservation INT NOT NULL, INDEX IDX_65E8AA0AF9F1A63B (nom_med_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE info_medicaux ADD CONSTRAINT FK_8B20A71CC7897E01 FOREIGN KEY (baby_name_id) REFERENCES baby (id)');
        $this->addSql('ALTER TABLE med ADD CONSTRAINT FK_ED1BBBC2782C8EBC FOREIGN KEY (etab_id) REFERENCES etab (id)');
        $this->addSql('ALTER TABLE med ADD CONSTRAINT FK_ED1BBBC2A9559549 FOREIGN KEY (info_medicaux_id) REFERENCES info_medicaux (id)');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AF9F1A63B FOREIGN KEY (nom_med_id) REFERENCES med (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE info_medicaux DROP FOREIGN KEY FK_8B20A71CC7897E01');
        $this->addSql('ALTER TABLE med DROP FOREIGN KEY FK_ED1BBBC2782C8EBC');
        $this->addSql('ALTER TABLE med DROP FOREIGN KEY FK_ED1BBBC2A9559549');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AF9F1A63B');
        $this->addSql('DROP TABLE baby');
        $this->addSql('DROP TABLE etab');
        $this->addSql('DROP TABLE info_medicaux');
        $this->addSql('DROP TABLE med');
        $this->addSql('DROP TABLE rendez_vous');
    }
}
