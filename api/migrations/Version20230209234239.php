<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230209234239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document ADD is_valid BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE document DROP status');
        $this->addSql('ALTER TABLE "user" ALTER is_verified SET DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ALTER is_verified DROP DEFAULT');
        $this->addSql('ALTER TABLE document ADD status VARCHAR(255) DEFAULT \'to_review\'');
        $this->addSql('ALTER TABLE document DROP is_valid');
    }
}
