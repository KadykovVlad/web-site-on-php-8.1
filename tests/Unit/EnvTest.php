<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Env;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

final class EnvTest extends TestCase
{
    private string $envFile;

    #[Before]
    protected function setUpEnvFile(): void
    {
        $this->envFile = sys_get_temp_dir() . '/blogy_test_' . uniqid() . '.env';
        file_put_contents(
            $this->envFile,
            "# comment line\n" .
            "DB_HOST=127.0.0.1\n" .
            "DB_DATABASE=\"blogy_test\"\n" .
            "\n" .
            "EMPTY_LINE_ABOVE=1\n"
        );
        Env::reset();
    }

    #[After]
    protected function tearDownEnvFile(): void
    {
        @unlink($this->envFile);
        Env::reset();
    }

    public function testLoadReadsKeyValuePairs(): void
    {
        Env::load($this->envFile);

        $this->assertSame('127.0.0.1', Env::get('DB_HOST'));
        $this->assertSame('1', Env::get('EMPTY_LINE_ABOVE'));
    }

    public function testLoadStripsSurroundingQuotes(): void
    {
        Env::load($this->envFile);

        $this->assertSame('blogy_test', Env::get('DB_DATABASE'));
    }

    public function testGetReturnsDefaultForMissingKey(): void
    {
        Env::load($this->envFile);

        $this->assertSame('fallback', Env::get('DOES_NOT_EXIST', 'fallback'));
        $this->assertNull(Env::get('DOES_NOT_EXIST'));
    }

    public function testLoadIsSilentWhenFileIsMissing(): void
    {
        Env::load('/path/does/not/exist.env');

        $this->assertSame('fallback', Env::get('ANYTHING', 'fallback'));
    }
}
