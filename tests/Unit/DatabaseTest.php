<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Config\Database;
use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
    public function testBuildDsnUsesGivenConfig(): void
    {
        $dsn = Database::buildDsn([
            'host' => 'db',
            'port' => '3306',
            'database' => 'blogy',
        ]);

        $this->assertSame('mysql:host=db;port=3306;dbname=blogy;charset=utf8mb4', $dsn);
    }

    public function testBuildDsnFallsBackToDefaults(): void
    {
        $dsn = Database::buildDsn([]);

        $this->assertSame('mysql:host=127.0.0.1;port=3306;dbname=;charset=utf8mb4', $dsn);
    }
}
