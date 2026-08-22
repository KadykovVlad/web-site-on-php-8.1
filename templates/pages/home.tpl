{include file='partials/header.tpl'}
{foreach $sections as $section}
<section data-purpose="category-section">
<div class="flex justify-between items-center mb-8 border-b border-gray-200 pb-2">
<h2 class="text-sm font-semibold tracking-widest uppercase text-gray-900">{$section.category.name}</h2>
<a class="text-xs font-medium text-gray-500 hover:text-gray-900 underline underline-offset-4" href="/category/{$section.category.slug}">Все статьи</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
{foreach $section.articles as $article}
<article class="flex flex-col">
{if $article.image}
<img alt="{$article.title}" class="w-full h-48 object-cover rounded-lg mb-4" src="{$article.image}">
{/if}
<h3 class="text-lg font-bold text-gray-900 mb-1 serif-font">{$article.title}</h3>
<p class="text-xs text-gray-400 mb-3">{$article.published_at|date_format:"%d.%m.%Y"}</p>
<p class="text-sm text-text-muted leading-relaxed mb-4 flex-grow">{$article.description}</p>
<a class="text-sm font-semibold text-gray-900 underline underline-offset-4 hover:text-brand-dark" href="/article/{$article.slug}">Читать дальше</a>
</article>
{/foreach}
</div>
</section>
{foreachelse}
<p class="text-text-muted">Пока нет ни одной категории со статьями.</p>
{/foreach}
{include file='partials/footer.tpl'}
