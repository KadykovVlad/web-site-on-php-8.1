{include file='partials/header.tpl'}
<section>
<div class="category-header">
<h1 class="category-header__title serif">{$category.name}</h1>
{if $category.description}
    <p class="category-header__description">{$category.description}</p>
{/if}
</div>

<div class="sort-bar">
<span class="sort-bar__label">Сортировка:</span>
<a class="sort-link{if $sort == 'date'} sort-link--active{/if}" href="/category/{$category.slug}?sort=date">По дате</a>
<a class="sort-link{if $sort == 'views'} sort-link--active{/if}" href="/category/{$category.slug}?sort=views">По просмотрам</a>
</div>

<div class="card-grid">
{foreach $articles as $article}
    {include file='partials/article-card.tpl' article=$article}
{foreachelse}
    <p class="empty-message">В этой категории пока нет статей.</p>
{/foreach}
</div>

{if $totalPages > 1}
<nav class="pagination">
{for $p=1 to $totalPages}
    <a class="pagination__link{if $p == $page} pagination__link--active{/if}" href="/category/{$category.slug}?sort={$sort}&page={$p}">{$p}</a>
{/for}
</nav>
{/if}
</section>
{include file='partials/footer.tpl'}
