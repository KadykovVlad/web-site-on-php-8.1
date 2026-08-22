<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Database\Seeder;
use PDO;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Support\TestDatabase;

final class SeederTest extends TestCase
{
    private PDO $pdo;
    private Seeder $seeder;

    #[Before]
    protected function setUpDatabase(): void
    {
        $this->pdo = TestDatabase::create();
        $this->seeder = new Seeder($this->pdo);
    }

    public function testRunInsertsCategoriesArticlesAndLinks(): void
    {
        $categories = [
            ['name' => 'PHP', 'slug' => 'php', 'description' => 'О PHP'],
            ['name' => 'MySQL', 'slug' => 'mysql', 'description' => 'О MySQL'],
        ];
        $articles = [
            $this->article('a', ['php']),
            $this->article('b', ['php', 'mysql']),
        ];

        $result = $this->seeder->run($categories, $articles);

        $this->assertSame(['categories' => 2, 'articles' => 2], $result);
        $this->assertSame(2, (int) $this->pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn());
        $this->assertSame(2, (int) $this->pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn());
        $this->assertSame(3, (int) $this->pdo->query('SELECT COUNT(*) FROM article_category')->fetchColumn());
    }

    public function testRunIsIdempotentOnRepeatedCalls(): void
    {
        $categories = [['name' => 'PHP', 'slug' => 'php', 'description' => null]];
        $articles = [$this->article('a', ['php'])];

        $this->seeder->run($categories, $articles);
        $this->seeder->run($categories, $articles);

        $this->assertSame(1, (int) $this->pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn());
        $this->assertSame(1, (int) $this->pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn());
    }

    public function testRunRollsBackWhenArticleReferencesUnknownCategory(): void
    {
        $categories = [['name' => 'PHP', 'slug' => 'php', 'description' => null]];
        $articles = [$this->article('a', ['does-not-exist'])];

        try {
            $this->seeder->run($categories, $articles);
            $this->fail('Ожидался RuntimeException.');
        } catch (RuntimeException) {
            // ожидаемое поведение
        }

        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn());
        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn());
    }

    private function article(string $slug, array $categories): array
    {
        return [
            'title' => strtoupper($slug),
            'slug' => $slug,
            'description' => 'desc',
            'content' => 'content',
            'image' => null,
            'views_count' => 0,
            'published_at' => '2026-01-01 00:00:00',
            'categories' => $categories,
        ];
    }
}
