<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504193654 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_1483A5E9A0D96FBF ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9C05FB297 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E992FC23A8 ON users');
        $this->addSql('ALTER TABLE users ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL, DROP last_login, DROP username_canonical, DROP email_canonical, DROP enabled, DROP salt, DROP confirmation_token, DROP password_requested_at, CHANGE username username VARCHAR(255) NOT NULL, CHANGE roles roles JSON NOT NULL, CHANGE email email VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
        $this->addSql('ALTER TABLE users ADD last_login DATETIME DEFAULT NULL, ADD username_canonical VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, ADD email_canonical VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, ADD enabled TINYINT(1) NOT NULL, ADD salt VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD confirmation_token VARCHAR(180) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD password_requested_at DATETIME DEFAULT NULL, DROP created_at, DROP updated_at, DROP deleted_at, CHANGE username username VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9A0D96FBF ON users (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C05FB297 ON users (confirmation_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E992FC23A8 ON users (username_canonical)');
    }
}
