<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ArticleRepository
{
    private const SORTABLE_COLUMNS = [
        'views' => 'views_count',
        'date' => 'published_at',
    ];

    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * N последних статей категории по дате публикации (для главной страницы).
     */
    public function latestByCategory(int $categoryId, int $limit = 3): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.* FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id = :categoryId
             ORDER BY a.published_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Статьи категории с сортировкой (views|date) и пагинацией.
     *
     * @return array{items: array, total: int}
     */
    public function paginateByCategory(int $categoryId, string $sort, int $page, int $perPage): array
    {
        $column = self::SORTABLE_COLUMNS[$sort] ?? self::SORTABLE_COLUMNS['date'];
        $offset = max(0, $page - 1) * $perPage;

        $countStmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id = :categoryId'
        );
        $countStmt->execute(['categoryId' => $categoryId]);
        $total = (int) $countStmt->fetchColumn();

        // Колонка сортировки не приходит от пользователя напрямую — только через
        // SORTABLE_COLUMNS, поэтому подстановку в SQL можно считать безопасной.
        $itemsStmt = $this->pdo->prepare(
            "SELECT a.* FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id = :categoryId
             ORDER BY a.{$column} DESC
             LIMIT :limit OFFSET :offset"
        );
        $itemsStmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $itemsStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $itemsStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $itemsStmt->execute();

        return ['items' => $itemsStmt->fetchAll(), 'total' => $total];
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM articles WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Категории, к которым привязана статья.
     */
    public function categoriesForArticle(int $articleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id, c.name, c.slug FROM categories c
             INNER JOIN article_category ac ON ac.category_id = c.id
             WHERE ac.article_id = :articleId
             ORDER BY c.name'
        );
        $stmt->execute(['articleId' => $articleId]);

        return $stmt->fetchAll();
    }

    /**
     * Похожие статьи: из тех же категорий, сама статья исключается.
     */
    public function similar(int $articleId, array $categoryIds, int $limit = 3): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT DISTINCT a.* FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id IN ({$placeholders}) AND a.id != ?
             ORDER BY a.published_at DESC
             LIMIT ?"
        );

        $position = 1;
        foreach ($categoryIds as $categoryId) {
            $stmt->bindValue($position++, $categoryId, PDO::PARAM_INT);
        }
        $stmt->bindValue($position++, $articleId, PDO::PARAM_INT);
        $stmt->bindValue($position, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function incrementViews(int $articleId): void
    {
        $stmt = $this->pdo->prepare('UPDATE articles SET views_count = views_count + 1 WHERE id = :id');
        $stmt->execute(['id' => $articleId]);
    }
}
