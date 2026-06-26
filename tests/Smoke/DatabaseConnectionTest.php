<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DatabaseConnectionTest extends KernelTestCase
{
    public function testItConnectsToPostgres(): void
    {
        self::bootKernel();

        $connection = self::getContainer()->get(Connection::class);

        $result = $connection->executeQuery('SELECT 1 AS one')->fetchOne();

        self::assertSame(1, (int) $result);
    }
}
