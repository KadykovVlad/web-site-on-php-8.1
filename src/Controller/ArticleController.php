<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArticleRepository;
use Smarty;

final class ArticleController
{
    private const SIMILAR_LIMIT = 3;

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

        $this->articles->incrementViews((int) $article['id']);

        $categories = $this->articles->categoriesForArticle((int) $article['id']);
        $categoryIds = array_column($categories, 'id');
        $similar = $this->articles->similar((int) $article['id'], $categoryIds, self::SIMILAR_LIMIT);

        $this->view->assign('article', $article);
        $this->view->assign('categories', $categories);
        $this->view->assign('similar', $similar);
        $this->view->display('pages/article.tpl');
    }
}
