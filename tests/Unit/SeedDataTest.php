<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Database\Seeder;
use PHPUnit\Framework\TestCase;
use Tests\Support\TestDatabase;

/**
 * Проверяет целостность реальных данных из database/seeds/, а не тестовых
 * фикстур — чтобы опечатка в slug категории не всплыла только в Docker на MySQL.
 */
final class SeedDataTest extends TestCase
{
    public function testCategorySlugsAreUnique(): void
    {
        $slugs = array_column($this->categories(), 'slug');

        $this->assertSame($slugs, array_unique($slugs));
    }

    public function testArticleSlugsAreUnique(): void
    {
        $slugs = array_column($this->articles(), 'slug');

        $this->assertSame($slugs, array_unique($slugs));
    }

    public function testArticlesReferenceOnlyKnownCategories(): void
    {
        $knownSlugs = array_column($this->categories(), 'slug');

        foreach ($this->articles() as $article) {
            foreach ($article['categories'] as $slug) {
                $this->assertContains(
                    $slug,
                    $knownSlugs,
                    "Статья {$article['slug']} ссылается на неизвестную категорию {$slug}"
                );
            }
        }
    }

    public function testSeederAcceptsRealSeedData(): void
    {
        $categories = $this->categories();
        $articles = $this->articles();

        $result = (new Seeder(TestDatabase::create()))->run($categories, $articles);

        $this->assertSame(count($categories), $result['categories']);
        $this->assertSame(count($articles), $result['articles']);
    }

    public function testAtLeastOneCategoryHasNoArticles(): void
    {
        $usedSlugs = array_unique(array_merge(...array_column($this->articles(), 'categories')));
        $allSlugs = array_column($this->categories(), 'slug');

        $this->assertNotEmpty(
            array_diff($allSlugs, $usedSlugs),
            'Ожидалась хотя бы одна категория без статей — проверяет, что главная страница её не покажет.'
        );
    }

    private function categories(): array
    {
        return require dirname(__DIR__, 2) . '/database/seeds/categories.php';
    }

    private function articles(): array
    {
        return require dirname(__DIR__, 2) . '/database/seeds/articles.php';
    }
}
