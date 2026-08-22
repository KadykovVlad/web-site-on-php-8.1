{include file='partials/header.tpl'}
<section>
<div class="border-b border-gray-200 pb-6 mb-8">
<h1 class="text-3xl font-bold serif-font">{$category.name}</h1>
{if $category.description}
    <p class="text-text-muted mt-2">{$category.description}</p>
{/if}
</div>

<div class="flex justify-end items-center gap-4 mb-8 text-sm">
<span class="text-text-muted">Сортировка:</span>
<a class="{if $sort == 'date'}font-semibold text-brand-dark{else}text-gray-500 hover:text-gray-900{/if}" href="/category/{$category.slug}?sort=date">По дате</a>
<a class="{if $sort == 'views'}font-semibold text-brand-dark{else}text-gray-500 hover:text-gray-900{/if}" href="/category/{$category.slug}?sort=views">По просмотрам</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
{foreach $articles as $article}
    {include file='partials/article-card.tpl' article=$article}
{foreachelse}
    <p class="text-text-muted col-span-full">В этой категории пока нет статей.</p>
{/foreach}
</div>

{if $totalPages > 1}
<nav class="flex justify-center gap-2 mt-12 text-sm">
{for $p=1 to $totalPages}
    <a class="px-3 py-1 rounded {if $p == $page}bg-brand-dark text-white{else}text-gray-500 hover:text-gray-900{/if}" href="/category/{$category.slug}?sort={$sort}&page={$p}">{$p}</a>
{/for}
</nav>
{/if}
</section>
{include file='partials/footer.tpl'}
