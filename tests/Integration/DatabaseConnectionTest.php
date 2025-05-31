<?php
/**
 * © 2025 pet.restaurateur — Licensed under AGPL-3.0-or-later.
 * See LICENSE file or https://www.gnu.org/licenses/agpl-3.0.html for details.
 */

namespace App\Tests\Integration;

use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

final class DatabaseConnectionTest extends TestCase
{
    public function testDatabaseConnection(): void
    {
        $connectionParams = [
            'url' => $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL'),
        ];
        $conn = DriverManager::getConnection($connectionParams);
        $this->assertTrue($conn->connect());
    }
}
