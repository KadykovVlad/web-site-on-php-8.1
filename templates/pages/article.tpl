{include file='partials/header.tpl'}
<article>
<h1 class="text-3xl font-bold serif-font mb-2">{$article.title}</h1>
<p class="text-xs text-gray-400">{$article.published_at|date_format:"%d.%m.%Y"}</p>
</article>
{include file='partials/footer.tpl'}
