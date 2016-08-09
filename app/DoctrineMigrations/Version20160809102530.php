<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160809102530 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE map_resolver (id INT AUTO_INCREMENT NOT NULL, map_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_21F250B953C55F64 (map_id), INDEX IDX_21F250B9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE map_resolver ADD CONSTRAINT FK_21F250B953C55F64 FOREIGN KEY (map_id) REFERENCES map (id)');
        $this->addSql('ALTER TABLE map_resolver ADD CONSTRAINT FK_21F250B9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE marker DROP FOREIGN KEY FK_82CF20FEA76ED395');
        $this->addSql('DROP INDEX IDX_82CF20FEA76ED395 ON marker');
        $this->addSql('ALTER TABLE marker DROP user_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE map_resolver');
        $this->addSql('ALTER TABLE marker ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE marker ADD CONSTRAINT FK_82CF20FEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_82CF20FEA76ED395 ON marker (user_id)');
    }
}
