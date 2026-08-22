{include file='partials/header.tpl'}
<article class="article">
<a class="article__back" href="/" onclick="history.back(); return false;">&larr; Назад</a>
{if $article.image}
<img alt="{$article.title}" class="article__image" src="{$article.image}">
{/if}

<div class="article__categories">
{foreach $categories as $category}
    <a class="article__category-link" href="/category/{$category.slug}">{$category.name}</a>
{/foreach}
</div>

<h1 class="article__title serif">{$article.title}</h1>

<div class="article__meta">
<span>{$article.published_at|date_format:"%d.%m.%Y"}</span>
<span>{$article.views_count} просмотров</span>
</div>

{if $article.description}
<p class="article__description">{$article.description}</p>
{/if}

<div class="article__content">{$article.content|escape|nl2br}</div>
</article>

{if $similar}
<section>
<div class="category-section__head">
<h2 class="category-section__title">Похожие статьи</h2>
</div>
<div class="card-grid">
{foreach $similar as $item}
    {include file='partials/article-card.tpl' article=$item}
{/foreach}
</div>
</section>
{/if}
{include file='partials/footer.tpl'}
