<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controller\ArticleController;
use App\Database\Seeder;
use App\Repository\ArticleRepository;
use PDO;
use PHPUnit\Framework\TestCase;
use Smarty;
use Tests\Support\TestDatabase;

final class ArticleControllerTest extends TestCase
{
    public function testShowRendersArticleWithCategoriesAndSimilar(): void
    {
        $pdo = $this->seededPdo();
        [$view, $controller] = $this->makeController($pdo);

        ob_start();
        $controller->show(['slug' => 'pdo-vs-mysqli-2026']);
        $html = ob_get_clean();

        $article = $view->getTemplateVars('article');
        $this->assertSame('pdo-vs-mysqli-2026', $article['slug']);

        // Статья привязана к двум категориям: php и mysql.
        $categories = $view->getTemplateVars('categories');
        $this->assertCount(2, $categories);

        $similar = $view->getTemplateVars('similar');
        $this->assertNotEmpty($similar);
        $this->assertNotContains('pdo-vs-mysqli-2026', array_column($similar, 'slug'));

        $this->assertStringContainsString('PDO vs mysqli', $html);
    }

    public function testShowIncrementsViewsCount(): void
    {
        $pdo = $this->seededPdo();
        $before = (int) $pdo->query("SELECT views_count FROM articles WHERE slug = 'osnovy-pdo-v-php'")->fetchColumn();

        [, $controller] = $this->makeController($pdo);
        ob_start();
        $controller->show(['slug' => 'osnovy-pdo-v-php']);
        ob_end_clean();

        $after = (int) $pdo->query("SELECT views_count FROM articles WHERE slug = 'osnovy-pdo-v-php'")->fetchColumn();
        $this->assertSame($before + 1, $after);
    }

    public function testShowReturns404ForUnknownSlug(): void
    {
        [$view, $controller] = $this->makeController($this->seededPdo());

        ob_start();
        $controller->show(['slug' => 'does-not-exist']);
        ob_end_clean();

        $this->assertNull($view->getTemplateVars('article'));
    }

    /**
     * @return array{0: Smarty, 1: ArticleController}
     */
    private function makeController(PDO $pdo): array
    {
        $view = new Smarty();
        $view->setTemplateDir(dirname(__DIR__, 2) . '/templates');
        $view->setCompileDir(sys_get_temp_dir());
        $view->caching = false;

        $controller = new ArticleController($view, new ArticleRepository($pdo));

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
