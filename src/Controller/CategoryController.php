<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use Smarty;

final class CategoryController
{
    public function __construct(
        private readonly Smarty $view,
        private readonly CategoryRepository $categories,
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

        $this->view->assign('category', $category);
        $this->view->display('pages/category.tpl');
    }
}
