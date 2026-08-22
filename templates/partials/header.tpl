<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>{$pageTitle|default:'Blogy'}</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style data-purpose="custom-fonts">
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500&display=swap');

    body {
      font-family: 'Inter', sans-serif;
    }

    h1, h2, h3, .serif-font {
      font-family: 'Playfair Display', serif;
    }
</style>
<script data-purpose="tailwind-config">
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'brand-dark': '#2c4b5a',
            'text-muted': '#6b7280',
          }
        }
      }
    }
</script>
</head>
<body class="bg-white text-gray-800 antialiased">
<header class="bg-brand-dark text-white py-4 px-6 md:px-12 w-full sticky top-0 z-50">
<div class="max-w-7xl mx-auto flex items-center justify-start">
<a class="text-2xl font-bold tracking-tight" href="/">Blogy.</a>
</div>
</header>
<main class="max-w-7xl mx-auto px-6 md:px-12 py-12 space-y-20">
