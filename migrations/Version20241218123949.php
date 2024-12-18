<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218123949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annee (id INT AUTO_INCREMENT NOT NULL, num INT NOT NULL, hebreu VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jour_mois (id INT AUTO_INCREMENT NOT NULL, num INT NOT NULL, hebreu VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jour_semaine (id INT AUTO_INCREMENT NOT NULL, num INT NOT NULL, francais VARCHAR(255) NOT NULL, hebreu VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ketouva (id INT AUTO_INCREMENT NOT NULL, jour_semaine_id INT NOT NULL, jour_mois_id INT NOT NULL, mois_id INT NOT NULL, annee_id INT NOT NULL, jour_semaine_mariage_id INT DEFAULT NULL, jour_mois_mariage_id INT DEFAULT NULL, mois_mariage_id INT DEFAULT NULL, annee_mariage_id INT DEFAULT NULL, type_ketouva VARCHAR(255) NOT NULL, ville VARCHAR(255) DEFAULT NULL, titre_hatan VARCHAR(255) DEFAULT NULL, nom_hatan VARCHAR(255) DEFAULT NULL, titre_pere_hatan VARCHAR(255) DEFAULT NULL, nom_pere_hatan VARCHAR(255) DEFAULT NULL, nom_kala VARCHAR(255) DEFAULT NULL, titre_pere_kala VARCHAR(255) DEFAULT NULL, nom_pere_kala VARCHAR(255) DEFAULT NULL, statut_kala VARCHAR(255) DEFAULT NULL, provenance_kala VARCHAR(255) DEFAULT NULL, orpheline TINYINT(1) DEFAULT NULL, ville_mariage VARCHAR(255) DEFAULT NULL, date_mariage_connue TINYINT(1) DEFAULT NULL, nom_fichier VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', edited_at DATETIME DEFAULT NULL, INDEX IDX_3219DDD75DE37D35 (jour_semaine_id), INDEX IDX_3219DDD75FC4F2DB (jour_mois_id), INDEX IDX_3219DDD7FA0749B8 (mois_id), INDEX IDX_3219DDD7543EC5F0 (annee_id), INDEX IDX_3219DDD7BB864792 (jour_semaine_mariage_id), INDEX IDX_3219DDD733EB8C34 (jour_mois_mariage_id), INDEX IDX_3219DDD760464CCA (mois_mariage_id), INDEX IDX_3219DDD739EA6286 (annee_mariage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mois (id INT AUTO_INCREMENT NOT NULL, num INT NOT NULL, francais VARCHAR(255) NOT NULL, hebreu VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ketouva ADD CONSTRAINT FK_3219DDD75DE37D35 FOREIGN KEY (jour_semaine_id) REFERENCES jour_semaine (id)');
        $this->addSql('ALTER TABLE ketouva ADD CONSTRAINT FK_3219DDD75FC4F2DB FOREIGN KEY (jour_mois_id) REFERENCES jour_mois (id)');
        $this->addSql('ALTER TABLE ketouva ADD CONSTRAINT FK_3219DDD7FA0749B8 FOREIGN KEY (mois_id) REFERENCES mois (id)');
        $this->addSql('ALTER TABLE ketouva ADD CONSTRAINT FK_3219DDD7543EC5F0 FOREIGN KEY (annee_id) REFERENCES annee (id)');
        $this->addSql('ALTER TABLE ketouva ADD CONSTRAINT FK_3219DDD7BB864792 FOREIGN KEY (jour_semaine_mariage_id) REFERENCES jour_semaine (id)');
        $this->addSql('ALTER TABLE ketouva ADD CONSTRAINT FK_3219DDD733EB8C34 FOREIGN KEY (jour_mois_mariage_id) REFERENCES jour_mois (id)');
        $this->addSql('ALTER TABLE ketouva ADD CONSTRAINT FK_3219DDD760464CCA FOREIGN KEY (mois_mariage_id) REFERENCES mois (id)');
        $this->addSql('ALTER TABLE ketouva ADD CONSTRAINT FK_3219DDD739EA6286 FOREIGN KEY (annee_mariage_id) REFERENCES annee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ketouva DROP FOREIGN KEY FK_3219DDD75DE37D35');
        $this->addSql('ALTER TABLE ketouva DROP FOREIGN KEY FK_3219DDD75FC4F2DB');
        $this->addSql('ALTER TABLE ketouva DROP FOREIGN KEY FK_3219DDD7FA0749B8');
        $this->addSql('ALTER TABLE ketouva DROP FOREIGN KEY FK_3219DDD7543EC5F0');
        $this->addSql('ALTER TABLE ketouva DROP FOREIGN KEY FK_3219DDD7BB864792');
        $this->addSql('ALTER TABLE ketouva DROP FOREIGN KEY FK_3219DDD733EB8C34');
        $this->addSql('ALTER TABLE ketouva DROP FOREIGN KEY FK_3219DDD760464CCA');
        $this->addSql('ALTER TABLE ketouva DROP FOREIGN KEY FK_3219DDD739EA6286');
        $this->addSql('DROP TABLE annee');
        $this->addSql('DROP TABLE jour_mois');
        $this->addSql('DROP TABLE jour_semaine');
        $this->addSql('DROP TABLE ketouva');
        $this->addSql('DROP TABLE mois');
    }
}
