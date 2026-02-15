<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260214170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_two_factor_enabled boolean to employee';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE employee ADD is_two_factor_enabled TINYINT(1) DEFAULT 0 NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE employee DROP is_two_factor_enabled');
    }
}
