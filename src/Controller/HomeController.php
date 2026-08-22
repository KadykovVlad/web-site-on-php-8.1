<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Smarty;

final class HomeController
{
    private const LATEST_PER_CATEGORY = 3;

    public function __construct(
        private readonly Smarty $view,
        private readonly CategoryRepository $categories,
        private readonly ArticleRepository $articles,
    ) {
    }

    public function index(array $params = []): void
    {
        $sections = [];

        foreach ($this->categories->findWithArticles() as $category) {
            $sections[] = [
                'category' => $category,
                'articles' => $this->articles->latestByCategory((int) $category['id'], self::LATEST_PER_CATEGORY),
            ];
        }

        $this->view->assign('sections', $sections);
        $this->view->display('pages/home.tpl');
    }
}
