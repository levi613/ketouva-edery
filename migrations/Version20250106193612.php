<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250106193612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ketouva ADD salle_francais VARCHAR(255) DEFAULT NULL, ADD ville_francais VARCHAR(255) DEFAULT NULL, ADD code_postal_francais VARCHAR(255) DEFAULT NULL, ADD date_francais VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ketouva DROP salle_francais, DROP ville_francais, DROP code_postal_francais, DROP date_francais');
    }
}
