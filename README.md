# Blogy — тестовое задание

Простой блог с категориями и статьями на чистом PHP (без фреймворков), MySQL и шаблонизаторе
Smarty.

## Стек

- PHP 8.1+
- MySQL 8
- Smarty 4
- SCSS (компилируется в CSS через scssphp)
- Docker / docker-compose
- PHPUnit (тесты)

## Структура проекта

```
├── database/          SQL-схема (применяется автоматически при первом старте контейнера db)
├── docker/             Dockerfile для PHP-FPM и конфиг Nginx
├── public/             Публичный каталог (единственная точка входа — index.php, public/css/app.css)
├── resources/scss/     Исходники стилей (SCSS), компилируются в public/css/app.css
├── src/                Код приложения (namespace App\)
│   ├── Config/          Подключение к БД и т.п.
│   └── Support/         Вспомогательные классы (загрузка .env и т.д.)
├── templates/          Smarty-шаблоны (.tpl)
├── templates_c/        Скомпилированные Smarty-шаблоны (кэш, не в git)
├── cache/               Кэш Smarty (не в git)
├── tests/               PHPUnit-тесты
├── docker-compose.yml
├── composer.json
└── phpunit.xml
```

## Запуск через Docker

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
```

Сайт будет доступен на `http://localhost:8080` (порт настраивается через `APP_PORT` в `.env`).
Adminer (просмотр БД) — `http://localhost:8081`.

Схема БД (`database/schema.sql`) применяется автоматически при первом запуске контейнера `db`.
Если нужно применить её заново — удалите volume `db_data` (`docker compose down -v`).

## Запуск без Docker

Нужны локально PHP 8.1+ с расширениями `pdo_mysql`, `mbstring`, Composer и MySQL 8.

```bash
cp .env.example .env      # и поправьте DB_HOST/DB_PORT/... под свою БД
composer install
mysql -u root -p < database/schema.sql
php -S localhost:8080 -t public
```

## Роутинг

Весь трафик идёт через `public/index.php` (см. `docker/nginx/default.conf`). Маршруты:

- `/` — главная
- `/category/{slug}` — страница категории
- `/article/{slug}` — страница статьи

Несуществующий маршрут или slug — 404.

## Сидинг

Наполняет БД тестовыми категориями и статьями (данные — в `database/seeds/`). Скрипт можно
запускать повторно: перед вставкой он полностью очищает таблицы `categories`, `articles`,
`article_category`.

```bash
docker compose exec app composer seed
# или без Docker:
composer seed
```

## Стили

Вёрстка на чистом SCSS без CSS-фреймворков. Исходники — в `resources/scss/` (переменные,
частичные файлы по секциям страниц), собираются в один `public/css/app.css` через `scssphp`
(PHP-библиотека, компиляция без Node.js).

Скомпилированный `public/css/app.css` уже лежит в репозитории — сайт работает из коробки без
сборки стилей. Пересобрать после правок в `resources/scss/` нужно только если менялись сами
SCSS-файлы:

```bash
docker compose exec app composer build-css
# или без Docker:
composer build-css
```

## Тесты

```bash
composer install
composer test
# или
vendor/bin/phpunit
```

## Roadmap

- [x] Структура проекта, Docker-окружение, Composer, Smarty, конфигурация БД и тестов
- [x] Репозитории категорий и статей
- [x] Сидинг категорий и статей
- [x] Роутинг и базовые Smarty-шаблоны
- [x] Главная страница
- [x] Страница категории (сортировка, пагинация)
- [x] Страница статьи (просмотры, похожие статьи)
- [x] Стили (SCSS)
- [ ] Финальная проверка по чек-листу задания

## Использование ИИ

Кратко, для чего именно использовался ИИ по этапам (подробное честное описание — в
[AI_USAGE.md](./AI_USAGE.md)):

1. Каркас проекта — Docker, Composer, Smarty, PDO-обёртка, конфигурация тестов.
2. Репозитории категорий и статей (запросы под конкретные страницы задания).
3. Сидинг — класс `Seeder`, тестовые данные, CLI-скрипт.
4. Роутинг и базовые Smarty-шаблоны, главная страница.
5. Страница категории — сортировка и пагинация.
6. Страница статьи — полный контент, счётчик просмотров, похожие статьи.
7. Вёрстка на SCSS вместо Tailwind CDN — переменные, частичные файлы, сборка через scssphp.

Архитектурные решения (структура БД, подход к коду без ORM, разбивка на этапы) принимал я сам;
реализацию по этим решениям генерировал ИИ, весь код перед принятием проверен и понятен мне.
