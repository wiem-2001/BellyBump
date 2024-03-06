<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306171613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE like_dislike (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, user_id INT NOT NULL, value TINYINT(1) NOT NULL, INDEX IDX_ADB6A6894B89032C (post_id), INDEX IDX_ADB6A689A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE like_dislike ADD CONSTRAINT FK_ADB6A6894B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE like_dislike ADD CONSTRAINT FK_ADB6A689A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE comment ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_9474526C7E3C61F9 ON comment (owner_id)');
        $this->addSql('ALTER TABLE post ADD auteur_id INT NOT NULL, DROP auteur');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D60BB6FE6 ON post (auteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE like_dislike DROP FOREIGN KEY FK_ADB6A6894B89032C');
        $this->addSql('ALTER TABLE like_dislike DROP FOREIGN KEY FK_ADB6A689A76ED395');
        $this->addSql('DROP TABLE like_dislike');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7E3C61F9');
        $this->addSql('DROP INDEX IDX_9474526C7E3C61F9 ON comment');
        $this->addSql('ALTER TABLE comment DROP owner_id');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D60BB6FE6');
        $this->addSql('DROP INDEX IDX_5A8A6C8D60BB6FE6 ON post');
        $this->addSql('ALTER TABLE post ADD auteur VARCHAR(255) NOT NULL, DROP auteur_id');
    }
}
