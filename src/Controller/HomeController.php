<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use Smarty;

final class HomeController
{
    public function __construct(
        private readonly Smarty $view,
        private readonly CategoryRepository $categories,
    ) {
    }

    public function index(array $params = []): void
    {
        $this->view->assign('categories', $this->categories->findWithArticles());
        $this->view->display('pages/home.tpl');
    }
}
