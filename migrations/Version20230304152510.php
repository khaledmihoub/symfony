<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230304152510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE codepromo (id INT AUTO_INCREMENT NOT NULL, comandes_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_DBDD8021CA844967 (comandes_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE codepromo ADD CONSTRAINT FK_DBDD8021CA844967 FOREIGN KEY (comandes_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande ADD id_code_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF9475767 FOREIGN KEY (id_code_id) REFERENCES codepromo (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DF9475767 ON commande (id_code_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DF9475767');
        $this->addSql('ALTER TABLE codepromo DROP FOREIGN KEY FK_DBDD8021CA844967');
        $this->addSql('DROP TABLE codepromo');
        $this->addSql('DROP INDEX IDX_6EEAA67DF9475767 ON commande');
        $this->addSql('ALTER TABLE commande DROP id_code_id');
    }
}
