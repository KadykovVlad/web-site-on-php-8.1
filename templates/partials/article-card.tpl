<article class="article-card">
{if $article.image}
<img alt="{$article.title}" class="article-card__image" src="{$article.image}">
{/if}
<h3 class="article-card__title serif">{$article.title}</h3>
<p class="article-card__date">{$article.published_at|date_format:"%d.%m.%Y"}</p>
<p class="article-card__excerpt">{$article.description}</p>
<a class="article-card__link" href="/article/{$article.slug}">Читать дальше</a>
</article>
