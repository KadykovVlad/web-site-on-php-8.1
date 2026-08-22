{include file='partials/header.tpl'}
{foreach $sections as $section}
<section data-purpose="category-section">
<div class="flex justify-between items-center mb-8 border-b border-gray-200 pb-2">
<h2 class="text-sm font-semibold tracking-widest uppercase text-gray-900">{$section.category.name}</h2>
<a class="text-xs font-medium text-gray-500 hover:text-gray-900 underline underline-offset-4" href="/category/{$section.category.slug}">Все статьи</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
{foreach $section.articles as $article}
    {include file='partials/article-card.tpl' article=$article}
{/foreach}
</div>
</section>
{foreachelse}
<p class="text-text-muted">Пока нет ни одной категории со статьями.</p>
{/foreach}
{include file='partials/footer.tpl'}
