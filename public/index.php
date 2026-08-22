<?php

declare(strict_types=1);

use App\Config\Database;
use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Http\Router;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Support\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');

$smarty = new Smarty();
$smarty->setTemplateDir(dirname(__DIR__) . '/templates');
$smarty->setCompileDir(dirname(__DIR__) . '/templates_c');
$smarty->setCacheDir(dirname(__DIR__) . '/cache');
$smarty->caching = false;

try {
    $pdo = Database::connection();
} catch (\Throwable $e) {
    http_response_code(500);
    echo 'Не удалось подключиться к базе данных.';
    exit;
}

$categoryRepository = new CategoryRepository($pdo);
$articleRepository = new ArticleRepository($pdo);

$homeController = new HomeController($smarty, $categoryRepository, $articleRepository);
$categoryController = new CategoryController($smarty, $categoryRepository, $articleRepository);
$articleController = new ArticleController($smarty, $articleRepository);

$router = new Router();
$router->get('/', [$homeController, 'index']);
$router->get('/category/{slug}', [$categoryController, 'show']);
$router->get('/article/{slug}', [$articleController, 'show']);

$match = $router->match($_SERVER['REQUEST_URI'] ?? '/');

if ($match === null) {
    http_response_code(404);
    $smarty->display('404.tpl');
    exit;
}

[$handler, $params] = $match;
$handler($params);
