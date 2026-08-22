<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Smarty;

final class CategoryController
{
    private const PER_PAGE = 6;
    private const SORT_OPTIONS = ['views', 'date'];

    public function __construct(
        private readonly Smarty $view,
        private readonly CategoryRepository $categories,
        private readonly ArticleRepository $articles,
    ) {
    }

    /**
     * @param array{slug: string} $params
     */
    public function show(array $params): void
    {
        $category = $this->categories->findBySlug($params['slug']);

        if ($category === null) {
            http_response_code(404);
            $this->view->display('404.tpl');

            return;
        }

        $sort = in_array($_GET['sort'] ?? null, self::SORT_OPTIONS, true) ? $_GET['sort'] : 'date';
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $this->articles->paginateByCategory((int) $category['id'], $sort, $page, self::PER_PAGE);

        $this->view->assign('category', $category);
        $this->view->assign('articles', $result['items']);
        $this->view->assign('sort', $sort);
        $this->view->assign('page', $page);
        $this->view->assign('totalPages', (int) ceil($result['total'] / self::PER_PAGE));
        $this->view->display('pages/category.tpl');
    }
}
