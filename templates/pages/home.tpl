{include file='partials/header.tpl'}
<section>
<h1 class="text-3xl font-bold serif-font mb-8">Категории</h1>
<ul class="divide-y divide-gray-200">
{foreach $categories as $category}
    <li class="py-6 flex justify-between items-start gap-6">
        <div>
            <a class="text-xl font-semibold serif-font hover:text-brand-dark" href="/category/{$category.slug}">{$category.name}</a>
            {if $category.description}
                <p class="text-sm text-text-muted mt-1">{$category.description}</p>
            {/if}
        </div>
        <a class="text-xs font-medium text-gray-500 hover:text-gray-900 underline underline-offset-4 whitespace-nowrap" href="/category/{$category.slug}">Все статьи</a>
    </li>
{foreachelse}
    <li class="py-6 text-text-muted">Пока нет ни одной категории со статьями.</li>
{/foreach}
</ul>
</section>
{include file='partials/footer.tpl'}
