<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160812080252 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wall_post (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, wall_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, date DATETIME NOT NULL, img VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9C2718ACA76ED395 (user_id), INDEX IDX_9C2718ACC33923F1 (wall_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wall (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_13F5EFF6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wall_post ADD CONSTRAINT FK_9C2718ACA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wall_post ADD CONSTRAINT FK_9C2718ACC33923F1 FOREIGN KEY (wall_id) REFERENCES wall (id)');
        $this->addSql('ALTER TABLE wall ADD CONSTRAINT FK_13F5EFF6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wall_post DROP FOREIGN KEY FK_9C2718ACC33923F1');
        $this->addSql('DROP TABLE wall_post');
        $this->addSql('DROP TABLE wall');
    }
}
