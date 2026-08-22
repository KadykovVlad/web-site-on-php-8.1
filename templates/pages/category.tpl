{include file='partials/header.tpl'}
<section>
<div class="border-b border-gray-200 pb-6 mb-8">
    <h1 class="text-3xl font-bold serif-font">{$category.name}</h1>
    {if $category.description}
        <p class="text-text-muted mt-2">{$category.description}</p>
    {/if}
</div>
</section>
{include file='partials/footer.tpl'}
