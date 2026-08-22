<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Окружение готово</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 4rem auto; color: #2c4b5a; }
        h1 { margin-bottom: .5rem; }
        ul { line-height: 1.8; }
        code { background: #f1f1f1; padding: .1rem .4rem; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Окружение настроено ✔</h1>
    <p>Этап 0: базовая структура проекта, Docker-окружение, Composer и Smarty подключены.</p>
    <ul>
        <li>PHP: <code>{$phpVersion}</code></li>
        <li>Smarty: <code>{$smartyVersion}</code></li>
        <li>MySQL: <code>{$dbStatus}</code></li>
    </ul>
    <p>Роутинг, контроллеры и реальные страницы блога добавлю дальше.</p>
</body>
</html>
