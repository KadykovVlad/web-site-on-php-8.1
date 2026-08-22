<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use RuntimeException;
use Throwable;

/**
 * Наполняет БД категориями и статьями из массивов данных.
 * Перед вставкой очищает таблицы — сидинг можно запускать повторно.
 */
final class Seeder
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array{categories: int, articles: int}
     */
    public function run(array $categories, array $articles): array
    {
        $this->clear();

        $this->pdo->beginTransaction();

        try {
            $categoryIds = $this->insertCategories($categories);
            $this->insertArticles($articles, $categoryIds);
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return ['categories' => count($categories), 'articles' => count($articles)];
    }

    private function clear(): void
    {
        // Удаляем в порядке от дочерних таблиц к родительским — без отключения проверки внешних ключей.
        $this->pdo->exec('DELETE FROM article_category');
        $this->pdo->exec('DELETE FROM articles');
        $this->pdo->exec('DELETE FROM categories');
    }

    /**
     * @return array<string, int> slug => id
     */
    private function insertCategories(array $categories): array
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)'
        );

        $ids = [];
        foreach ($categories as $category) {
            $stmt->execute([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => $category['description'],
            ]);
            $ids[$category['slug']] = (int) $this->pdo->lastInsertId();
        }

        return $ids;
    }

    /**
     * @param array<string, int> $categoryIds slug => id
     */
    private function insertArticles(array $articles, array $categoryIds): void
    {
        $insertArticle = $this->pdo->prepare(
            'INSERT INTO articles (title, slug, description, content, image, views_count, published_at)
             VALUES (:title, :slug, :description, :content, :image, :views_count, :published_at)'
        );
        $linkCategory = $this->pdo->prepare(
            'INSERT INTO article_category (article_id, category_id) VALUES (:article_id, :category_id)'
        );

        foreach ($articles as $article) {
            $insertArticle->execute([
                'title' => $article['title'],
                'slug' => $article['slug'],
                'description' => $article['description'],
                'content' => $article['content'],
                'image' => $article['image'],
                'views_count' => $article['views_count'],
                'published_at' => $article['published_at'],
            ]);
            $articleId = (int) $this->pdo->lastInsertId();

            foreach ($article['categories'] as $slug) {
                if (!isset($categoryIds[$slug])) {
                    throw new RuntimeException("Неизвестная категория в сидере статей: {$slug}");
                }

                $linkCategory->execute([
                    'article_id' => $articleId,
                    'category_id' => $categoryIds[$slug],
                ]);
            }
        }
    }
}
