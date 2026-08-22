<article class="flex flex-col">
{if $article.image}
<img alt="{$article.title}" class="w-full h-48 object-cover rounded-lg mb-4" src="{$article.image}">
{/if}
<h3 class="text-lg font-bold text-gray-900 mb-1 serif-font">{$article.title}</h3>
<p class="text-xs text-gray-400 mb-3">{$article.published_at|date_format:"%d.%m.%Y"}</p>
<p class="text-sm text-text-muted leading-relaxed mb-4 flex-grow">{$article.description}</p>
<a class="text-sm font-semibold text-gray-900 underline underline-offset-4 hover:text-brand-dark" href="/article/{$article.slug}">Читать дальше</a>
</article>
