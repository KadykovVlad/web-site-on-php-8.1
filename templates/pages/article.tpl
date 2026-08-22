{include file='partials/header.tpl'}
<article>
{if $article.image}
<img alt="{$article.title}" class="w-full max-h-96 object-cover rounded-lg mb-8" src="{$article.image}">
{/if}

<div class="flex flex-wrap items-center gap-3 text-xs text-text-muted mb-4">
{foreach $categories as $category}
    <a class="uppercase tracking-widest font-semibold hover:text-brand-dark" href="/category/{$category.slug}">{$category.name}</a>
{/foreach}
</div>

<h1 class="text-4xl font-bold serif-font mb-3">{$article.title}</h1>

<div class="flex items-center gap-4 text-xs text-gray-400 mb-8">
<span>{$article.published_at|date_format:"%d.%m.%Y"}</span>
<span>{$article.views_count} просмотров</span>
</div>

{if $article.description}
<p class="text-lg text-text-muted leading-relaxed mb-8">{$article.description}</p>
{/if}

<div class="text-gray-800 leading-relaxed space-y-4">{$article.content|escape|nl2br}</div>
</article>

{if $similar}
<section>
<div class="border-b border-gray-200 pb-2 mb-8">
<h2 class="text-sm font-semibold tracking-widest uppercase text-gray-900">Похожие статьи</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
{foreach $similar as $item}
    {include file='partials/article-card.tpl' article=$item}
{/foreach}
</div>
</section>
{/if}
{include file='partials/footer.tpl'}
