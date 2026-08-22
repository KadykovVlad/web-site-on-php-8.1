<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class CategoryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Категории, в которых есть хотя бы одна статья (для главной страницы).
     */
    public function findWithArticles(): array
    {
        $sql = 'SELECT DISTINCT c.id, c.name, c.slug, c.description
                FROM categories c
                INNER JOIN article_category ac ON ac.category_id = c.id
                ORDER BY c.name';

        return $this->pdo->query($sql)->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, slug, description FROM categories WHERE slug = :slug LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }
}
