<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repository\ArticleRepository;
use PDO;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use Tests\Support\TestDatabase;

final class ArticleRepositoryTest extends TestCase
{
    private PDO $pdo;
    private ArticleRepository $repository;

    #[Before]
    protected function setUpDatabase(): void
    {
        $this->pdo = TestDatabase::create();
        $this->repository = new ArticleRepository($this->pdo);
        $this->insertCategory(1, 'PHP', 'php');
    }

    public function testLatestByCategoryOrdersByPublishedAtDescAndRespectsLimit(): void
    {
        $this->insertArticle(1, 'A', 'a', '2026-01-01');
        $this->insertArticle(2, 'B', 'b', '2026-03-01');
        $this->insertArticle(3, 'C', 'c', '2026-02-01');
        $this->insertArticle(4, 'D', 'd', '2026-04-01');
        foreach ([1, 2, 3, 4] as $id) {
            $this->linkArticleToCategory($id, 1);
        }

        $latest = $this->repository->latestByCategory(1, 3);

        $this->assertCount(3, $latest);
        $this->assertSame(['d', 'b', 'c'], array_column($latest, 'slug'));
    }

    public function testPaginateByCategorySortsByViews(): void
    {
        $this->insertArticle(1, 'A', 'a', '2026-01-01', views: 5);
        $this->insertArticle(2, 'B', 'b', '2026-01-02', views: 20);
        $this->insertArticle(3, 'C', 'c', '2026-01-03', views: 10);
        foreach ([1, 2, 3] as $id) {
            $this->linkArticleToCategory($id, 1);
        }

        $result = $this->repository->paginateByCategory(1, 'views', 1, 2);

        $this->assertSame(3, $result['total']);
        $this->assertCount(2, $result['items']);
        $this->assertSame(['b', 'c'], array_column($result['items'], 'slug'));
    }

    public function testPaginateByCategoryPaginatesSecondPage(): void
    {
        $this->insertArticle(1, 'A', 'a', '2026-01-01');
        $this->insertArticle(2, 'B', 'b', '2026-01-02');
        $this->insertArticle(3, 'C', 'c', '2026-01-03');
        foreach ([1, 2, 3] as $id) {
            $this->linkArticleToCategory($id, 1);
        }

        $result = $this->repository->paginateByCategory(1, 'date', 2, 2);

        $this->assertSame(3, $result['total']);
        $this->assertCount(1, $result['items']);
        $this->assertSame('a', $result['items'][0]['slug']);
    }

    public function testPaginateByCategoryFallsBackToDateForUnknownSort(): void
    {
        $this->insertArticle(1, 'A', 'a', '2026-01-01', views: 100);
        $this->insertArticle(2, 'B', 'b', '2026-01-02', views: 1);
        foreach ([1, 2] as $id) {
            $this->linkArticleToCategory($id, 1);
        }

        $result = $this->repository->paginateByCategory(1, 'unknown', 1, 10);

        $this->assertSame(['b', 'a'], array_column($result['items'], 'slug'));
    }

    public function testFindBySlugReturnsArticle(): void
    {
        $this->insertArticle(1, 'A', 'a', '2026-01-01');

        $article = $this->repository->findBySlug('a');

        $this->assertNotNull($article);
        $this->assertSame('A', $article['title']);
    }

    public function testFindBySlugReturnsNullWhenMissing(): void
    {
        $this->assertNull($this->repository->findBySlug('missing'));
    }

    public function testCategoriesForArticleReturnsAssignedCategories(): void
    {
        $this->insertCategory(2, 'MySQL', 'mysql');
        $this->insertArticle(1, 'A', 'a', '2026-01-01');
        $this->linkArticleToCategory(1, 1);
        $this->linkArticleToCategory(1, 2);

        $categories = $this->repository->categoriesForArticle(1);

        $this->assertCount(2, $categories);
        $this->assertSame(['mysql', 'php'], array_column($categories, 'slug'));
    }

    public function testSimilarExcludesSelfAndOrdersByPublishedAtDesc(): void
    {
        $this->insertArticle(1, 'A', 'a', '2026-01-01');
        $this->insertArticle(2, 'B', 'b', '2026-03-01');
        $this->insertArticle(3, 'C', 'c', '2026-02-01');
        foreach ([1, 2, 3] as $id) {
            $this->linkArticleToCategory($id, 1);
        }

        $similar = $this->repository->similar(1, [1], 3);

        $this->assertCount(2, $similar);
        $this->assertSame(['b', 'c'], array_column($similar, 'slug'));
    }

    public function testSimilarReturnsEmptyArrayWhenNoCategoriesGiven(): void
    {
        $this->assertSame([], $this->repository->similar(1, [], 3));
    }

    public function testIncrementViewsIncreasesCounter(): void
    {
        $this->insertArticle(1, 'A', 'a', '2026-01-01', views: 0);

        $this->repository->incrementViews(1);
        $this->repository->incrementViews(1);

        $article = $this->repository->findBySlug('a');
        $this->assertSame(2, (int) $article['views_count']);
    }

    private function insertCategory(int $id, string $name, string $slug): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO categories (id, name, slug) VALUES (?, ?, ?)');
        $stmt->execute([$id, $name, $slug]);
    }

    private function insertArticle(int $id, string $title, string $slug, string $publishedAt, int $views = 0): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO articles (id, title, slug, content, published_at, views_count) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$id, $title, $slug, 'content', $publishedAt . ' 00:00:00', $views]);
    }

    private function linkArticleToCategory(int $articleId, int $categoryId): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO article_category (article_id, category_id) VALUES (?, ?)');
        $stmt->execute([$articleId, $categoryId]);
    }
}
