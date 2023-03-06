<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305122255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D9311871B');
        $this->addSql('DROP INDEX IDX_6EEAA67D9311871B ON commande');
        $this->addSql('ALTER TABLE commande CHANGE code_id_id code_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D27DAFE17 FOREIGN KEY (code_id) REFERENCES codepromo (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D27DAFE17 ON commande (code_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D27DAFE17');
        $this->addSql('DROP INDEX IDX_6EEAA67D27DAFE17 ON commande');
        $this->addSql('ALTER TABLE commande CHANGE code_id code_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D9311871B FOREIGN KEY (code_id_id) REFERENCES codepromo (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D9311871B ON commande (code_id_id)');
    }
}
