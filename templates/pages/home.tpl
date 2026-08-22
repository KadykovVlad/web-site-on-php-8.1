{include file='partials/header.tpl'}
{foreach $sections as $section}
<section data-purpose="category-section">
<div class="category-section__head">
<h2 class="category-section__title">{$section.category.name}</h2>
<a class="category-section__link" href="/category/{$section.category.slug}">Все статьи</a>
</div>
<div class="card-grid">
{foreach $section.articles as $article}
    {include file='partials/article-card.tpl' article=$article}
{/foreach}
</div>
</section>
{foreachelse}
<p class="empty-message">Пока нет ни одной категории со статьями.</p>
{/foreach}
{include file='partials/footer.tpl'}
