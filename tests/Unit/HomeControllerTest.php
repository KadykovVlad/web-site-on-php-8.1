<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controller\HomeController;
use App\Database\Seeder;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;
use Smarty;
use Tests\Support\TestDatabase;

final class HomeControllerTest extends TestCase
{
    public function testIndexGroupsLatestArticlesByCategoryAndRenders(): void
    {
        $pdo = TestDatabase::create();
        $categories = require dirname(__DIR__, 2) . '/database/seeds/categories.php';
        $articles = require dirname(__DIR__, 2) . '/database/seeds/articles.php';
        (new Seeder($pdo))->run($categories, $articles);

        $view = new Smarty();
        $view->setTemplateDir(dirname(__DIR__, 2) . '/templates');
        $view->setCompileDir(sys_get_temp_dir());
        $view->caching = false;

        $controller = new HomeController($view, new CategoryRepository($pdo), new ArticleRepository($pdo));

        ob_start();
        $controller->index();
        $html = ob_get_clean();

        $sections = $view->getTemplateVars('sections');

        // Категория devops без единой статьи на главную попадать не должна.
        $this->assertCount(3, $sections);

        $phpSection = $this->findSection($sections, 'php');
        $this->assertNotNull($phpSection);
        $this->assertCount(3, $phpSection['articles']);
        $this->assertSame('pdo-vs-mysqli-2026', $phpSection['articles'][0]['slug']);

        $this->assertStringContainsString('PDO vs mysqli', $html);
        $this->assertStringContainsString('Все статьи', $html);
        $this->assertStringNotContainsString('DevOps', $html);
    }

    private function findSection(array $sections, string $categorySlug): ?array
    {
        foreach ($sections as $section) {
            if ($section['category']['slug'] === $categorySlug) {
                return $section;
            }
        }

        return null;
    }
}
