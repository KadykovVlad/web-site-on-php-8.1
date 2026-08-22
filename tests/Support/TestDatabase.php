<?php

declare(strict_types=1);

namespace Tests\Support;

use PDO;

final class TestDatabase
{
    public static function create(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $pdo->exec('CREATE TABLE categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT
        )');

        $pdo->exec('CREATE TABLE articles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            content TEXT NOT NULL,
            image TEXT,
            views_count INTEGER NOT NULL DEFAULT 0,
            published_at TEXT NOT NULL
        )');

        $pdo->exec('CREATE TABLE article_category (
            article_id INTEGER NOT NULL,
            category_id INTEGER NOT NULL,
            PRIMARY KEY (article_id, category_id)
        )');

        return $pdo;
    }
}
