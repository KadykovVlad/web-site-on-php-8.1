<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArticleRepository;
use Smarty;

final class ArticleController
{
    public function __construct(
        private readonly Smarty $view,
        private readonly ArticleRepository $articles,
    ) {
    }

    /**
     * @param array{slug: string} $params
     */
    public function show(array $params): void
    {
        $article = $this->articles->findBySlug($params['slug']);

        if ($article === null) {
            http_response_code(404);
            $this->view->display('404.tpl');

            return;
        }

        $this->view->assign('article', $article);
        $this->view->display('pages/article.tpl');
    }
}
