<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250601100412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'task/pri-2';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE restaurants (id UUID NOT NULL, name VARCHAR(32) NOT NULL, description VARCHAR(255) NOT NULL, address VARCHAR(64) NOT NULL, coordinates POINT NOT NULL, phone VARCHAR(16) NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN restaurants.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN restaurants.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN restaurants.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE working_hours (id SERIAL NOT NULL, restaurant_id UUID NOT NULL, day_of_week SMALLINT NOT NULL, open_time TIME(0) WITHOUT TIME ZONE NOT NULL, close_time TIME(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D72CDC3DB1E7706E ON working_hours (restaurant_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN working_hours.restaurant_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN working_hours.open_time IS '(DC2Type:time_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN working_hours.close_time IS '(DC2Type:time_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE working_hours ADD CONSTRAINT FK_D72CDC3DB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurants (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE working_hours DROP CONSTRAINT FK_D72CDC3DB1E7706E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE restaurants
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE working_hours
        SQL);
    }
}
