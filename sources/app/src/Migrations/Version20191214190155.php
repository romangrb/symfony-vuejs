<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191214190155 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_1483A5E9F85E0677 ON users');
        $this->addSql('ALTER TABLE users ADD username_canonical VARCHAR(180) NOT NULL, ADD email VARCHAR(180) NOT NULL, ADD email_canonical VARCHAR(180) NOT NULL, ADD enabled TINYINT(1) NOT NULL, ADD salt VARCHAR(255) DEFAULT NULL, ADD confirmation_token VARCHAR(180) DEFAULT NULL, ADD password_requested_at DATETIME DEFAULT NULL, DROP created, CHANGE username username VARCHAR(180) NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE updated last_login DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E992FC23A8 ON users (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9A0D96FBF ON users (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C05FB297 ON users (confirmation_token)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_1483A5E992FC23A8 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9A0D96FBF ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9C05FB297 ON users');
        $this->addSql('ALTER TABLE users ADD created DATETIME NOT NULL, ADD updated DATETIME DEFAULT NULL, DROP username_canonical, DROP email, DROP email_canonical, DROP enabled, DROP salt, DROP last_login, DROP confirmation_token, DROP password_requested_at, CHANGE username username VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
    }
}
