<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191225172715 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE enqueue (id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', published_at BIGINT NOT NULL, body LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, headers LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, properties LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, redelivered TINYINT(1) DEFAULT NULL, queue VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, priority SMALLINT DEFAULT NULL, delayed_until BIGINT DEFAULT NULL, time_to_live BIGINT DEFAULT NULL, delivery_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', redeliver_after BIGINT DEFAULT NULL, INDEX IDX_CFC35A6862A6DC27E0D4FDE17FFD7F63121369211A065DF8BF396750 (priority, published_at, queue, delivery_id, delayed_until, id), INDEX IDX_CFC35A68AA0BDFF712136921 (redeliver_after, delivery_id), INDEX IDX_CFC35A68E0669C0612136921 (time_to_live, delivery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE events ADD is_review_failed TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE enqueue');
        $this->addSql('ALTER TABLE events DROP is_review_failed');
    }
}
