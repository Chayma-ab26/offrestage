<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241207201745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature DROP status, CHANGE etudiant_id etudiant_id INT NOT NULL, CHANGE offre_stage_id offre_stage_id INT NOT NULL, CHANGE date_soumission date_soumission DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE offre_stage ADD status VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature ADD status VARCHAR(255) DEFAULT NULL, CHANGE offre_stage_id offre_stage_id INT DEFAULT NULL, CHANGE etudiant_id etudiant_id INT DEFAULT NULL, CHANGE date_soumission date_soumission DATE DEFAULT \'CURRENT_TIMESTAMP\'');
        $this->addSql('ALTER TABLE offre_stage DROP status');
    }
}
