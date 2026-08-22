<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Config\Database;
use App\Database\Seeder;
use App\Support\Env;

Env::load(dirname(__DIR__) . '/.env');

$categories = require dirname(__DIR__) . '/database/seeds/categories.php';
$articles = require dirname(__DIR__) . '/database/seeds/articles.php';

$result = (new Seeder(Database::connection()))->run($categories, $articles);

printf("Готово: %d категорий, %d статей.\n", $result['categories'], $result['articles']);
