<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260619123210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add users table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<MYSQL
                CREATE TABLE users (
                    id INT UNSIGNED AUTO_INCREMENT NOT NULL,
                    login VARCHAR(8) NOT NULL,
                    phone VARCHAR(8) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    roles JSON NOT NULL,
                    UNIQUE INDEX UNIQ_1483A5E9AA08CB10 (login),
                    PRIMARY KEY (id)
                ) DEFAULT CHARACTER SET utf8mb4
            MYSQL,
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<MYSQL
                DROP TABLE users
            MYSQL,
        );
    }
}
