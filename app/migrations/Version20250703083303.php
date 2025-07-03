<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250703083303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD email_verified BOOLEAN NOT NULL DEFAULT FALSE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD email_verification_token VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD email_verification_token_expiry TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD reset_password_token VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD reset_password_token_expiry TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP email_verified
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP email_verification_token
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP email_verification_token_expiry
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP reset_password_token
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP reset_password_token_expiry
        SQL);
    }
}
