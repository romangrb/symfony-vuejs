<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200409082409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE places (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_attachments (id INT AUTO_INCREMENT NOT NULL, place_id INT NOT NULL, file_path VARCHAR(255) NOT NULL, INDEX IDX_4931B272DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_contents (id INT AUTO_INCREMENT NOT NULL, place_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, file_path VARCHAR(255) NOT NULL, is_published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_9EA1F6F4DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_locations (id INT AUTO_INCREMENT NOT NULL, place_id INT NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_637DC3C7DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE place_attachments ADD CONSTRAINT FK_4931B272DA6A219 FOREIGN KEY (place_id) REFERENCES places (id)');
        $this->addSql('ALTER TABLE place_contents ADD CONSTRAINT FK_9EA1F6F4DA6A219 FOREIGN KEY (place_id) REFERENCES places (id)');
        $this->addSql('ALTER TABLE place_locations ADD CONSTRAINT FK_637DC3C7DA6A219 FOREIGN KEY (place_id) REFERENCES places (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE place_attachments DROP FOREIGN KEY FK_4931B272DA6A219');
        $this->addSql('ALTER TABLE place_contents DROP FOREIGN KEY FK_9EA1F6F4DA6A219');
        $this->addSql('ALTER TABLE place_locations DROP FOREIGN KEY FK_637DC3C7DA6A219');
        $this->addSql('DROP TABLE places');
        $this->addSql('DROP TABLE place_attachments');
        $this->addSql('DROP TABLE place_contents');
        $this->addSql('DROP TABLE place_locations');
    }
}
