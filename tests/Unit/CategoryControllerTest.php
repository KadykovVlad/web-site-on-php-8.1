<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controller\CategoryController;
use App\Database\Seeder;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use PDO;
use PHPUnit\Framework\TestCase;
use Smarty;
use Tests\Support\TestDatabase;

final class CategoryControllerTest extends TestCase
{
    private array $originalGet = [];

    protected function setUp(): void
    {
        $this->originalGet = $_GET;
    }

    protected function tearDown(): void
    {
        $_GET = $this->originalGet;
    }

    public function testShowSortsByDateByDefault(): void
    {
        [$view, $controller] = $this->makeController($this->seededPdo());

        $_GET = [];
        ob_start();
        $controller->show(['slug' => 'php']);
        $html = ob_get_clean();

        $this->assertSame('date', $view->getTemplateVars('sort'));
        $articles = $view->getTemplateVars('articles');
        $this->assertCount(4, $articles);
        $this->assertSame('pdo-vs-mysqli-2026', $articles[0]['slug']);
        $this->assertStringContainsString('PHP', $html);
    }

    public function testShowSortsByViewsWhenRequested(): void
    {
        [$view, $controller] = $this->makeController($this->seededPdo());

        $_GET = ['sort' => 'views'];
        ob_start();
        $controller->show(['slug' => 'mysql']);
        ob_end_clean();

        $articles = $view->getTemplateVars('articles');
        $this->assertSame('pdo-vs-mysqli-2026', $articles[0]['slug']);
    }

    public function testShowIgnoresUnknownSortValue(): void
    {
        [$view, $controller] = $this->makeController($this->seededPdo());

        $_GET = ['sort' => 'random-garbage'];
        ob_start();
        $controller->show(['slug' => 'php']);
        ob_end_clean();

        $this->assertSame('date', $view->getTemplateVars('sort'));
    }

    public function testShowReturns404ForUnknownCategory(): void
    {
        [$view, $controller] = $this->makeController($this->seededPdo());

        ob_start();
        $controller->show(['slug' => 'does-not-exist']);
        ob_end_clean();

        $this->assertNull($view->getTemplateVars('category'));
    }

    public function testShowHandlesCategoryWithoutArticles(): void
    {
        [$view, $controller] = $this->makeController($this->seededPdo());

        ob_start();
        $controller->show(['slug' => 'devops']);
        ob_end_clean();

        $this->assertSame([], $view->getTemplateVars('articles'));
        $this->assertSame(0, $view->getTemplateVars('totalPages'));
    }

    public function testShowRespectsPageParameter(): void
    {
        $pdo = TestDatabase::create();
        $pdo->prepare('INSERT INTO categories (id, name, slug) VALUES (?, ?, ?)')->execute([1, 'PHP', 'php']);

        for ($i = 1; $i <= 8; $i++) {
            $pdo->prepare('INSERT INTO articles (id, title, slug, content, published_at) VALUES (?, ?, ?, ?, ?)')
                ->execute([$i, "Article {$i}", "article-{$i}", 'content', sprintf('2026-01-%02d 00:00:00', $i)]);
            $pdo->prepare('INSERT INTO article_category (article_id, category_id) VALUES (?, ?)')->execute([$i, 1]);
        }

        [$view, $controller] = $this->makeController($pdo);

        $_GET = ['page' => '2'];
        ob_start();
        $controller->show(['slug' => 'php']);
        ob_end_clean();

        // 8 статей, по 6 на страницу (PER_PAGE) -> 2 страницы, на второй — 2 самые старые.
        $this->assertSame(2, $view->getTemplateVars('totalPages'));
        $articles = $view->getTemplateVars('articles');
        $this->assertCount(2, $articles);
        $this->assertSame('article-2', $articles[0]['slug']);
    }

    /**
     * @return array{0: Smarty, 1: CategoryController}
     */
    private function makeController(PDO $pdo): array
    {
        $view = new Smarty();
        $view->setTemplateDir(dirname(__DIR__, 2) . '/templates');
        $view->setCompileDir(sys_get_temp_dir());
        $view->caching = false;

        $controller = new CategoryController($view, new CategoryRepository($pdo), new ArticleRepository($pdo));

        return [$view, $controller];
    }

    private function seededPdo(): PDO
    {
        $pdo = TestDatabase::create();
        $categories = require dirname(__DIR__, 2) . '/database/seeds/categories.php';
        $articles = require dirname(__DIR__, 2) . '/database/seeds/articles.php';
        (new Seeder($pdo))->run($categories, $articles);

        return $pdo;
    }
}
