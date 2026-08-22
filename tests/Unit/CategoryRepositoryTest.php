<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repository\CategoryRepository;
use PDO;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use Tests\Support\TestDatabase;

final class CategoryRepositoryTest extends TestCase
{
    private PDO $pdo;
    private CategoryRepository $repository;

    #[Before]
    protected function setUpDatabase(): void
    {
        $this->pdo = TestDatabase::create();
        $this->repository = new CategoryRepository($this->pdo);
    }

    public function testFindWithArticlesReturnsOnlyCategoriesThatHaveArticles(): void
    {
        $this->insertCategory(1, 'PHP', 'php');
        $this->insertCategory(2, 'Пустая категория', 'empty');
        $this->insertArticle(1, 'Статья', 'article-1');
        $this->linkArticleToCategory(1, 1);

        $result = $this->repository->findWithArticles();

        $this->assertCount(1, $result);
        $this->assertSame('php', $result[0]['slug']);
    }

    public function testFindBySlugReturnsCategory(): void
    {
        $this->insertCategory(1, 'PHP', 'php', 'Про PHP');

        $category = $this->repository->findBySlug('php');

        $this->assertNotNull($category);
        $this->assertSame('PHP', $category['name']);
        $this->assertSame('Про PHP', $category['description']);
    }

    public function testFindBySlugReturnsNullWhenMissing(): void
    {
        $this->assertNull($this->repository->findBySlug('missing'));
    }

    private function insertCategory(int $id, string $name, string $slug, ?string $description = null): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO categories (id, name, slug, description) VALUES (?, ?, ?, ?)');
        $stmt->execute([$id, $name, $slug, $description]);
    }

    private function insertArticle(int $id, string $title, string $slug): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO articles (id, title, slug, content, published_at) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$id, $title, $slug, 'content', '2026-01-01 00:00:00']);
    }

    private function linkArticleToCategory(int $articleId, int $categoryId): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO article_category (article_id, category_id) VALUES (?, ?)');
        $stmt->execute([$articleId, $categoryId]);
    }
}
