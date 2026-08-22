# Blogy — тестовое задание

Простой блог с категориями и статьями на чистом PHP (без фреймворков), MySQL и шаблонизаторе
Smarty.

## Стек

- PHP 8.1+
- MySQL 8
- Smarty 4
- Docker / docker-compose
- PHPUnit (тесты)

## Структура проекта

```
├── database/          SQL-схема (применяется автоматически при первом старте контейнера db)
├── docker/             Dockerfile для PHP-FPM и конфиг Nginx
├── public/             Публичный каталог (единственная точка входа — index.php)
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

Нужны локально PHP 8.1+ с расширениями `pdo_mysql`, `mbstring`, `intl`, Composer и MySQL 8.

```bash
cp .env.example .env      # и поправьте DB_HOST/DB_PORT/... под свою БД
composer install
mysql -u root -p < database/schema.sql
php -S localhost:8080 -t public
```

## Сидинг

Наполняет БД тестовыми категориями и статьями (данные — в `database/seeds/`). Скрипт можно
запускать повторно: перед вставкой он полностью очищает таблицы `categories`, `articles`,
`article_category`.

```bash
docker compose exec app composer seed
# или без Docker:
composer seed
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
- [ ] Роутинг и базовые Smarty-шаблоны
- [ ] Главная страница
- [ ] Страница категории (сортировка, пагинация)
- [ ] Страница статьи (просмотры, похожие статьи)
- [ ] Стили (SCSS)
- [ ] Финальная проверка по чек-листу задания

## Использование ИИ

См. [AI_USAGE.md](./AI_USAGE.md).
