# PVM - PHP View Model
Простое веб‑приложение на PHP (MVC) с MySQL и REST API.

## Стек
- PHP 8.2 + Apache (mod_rewrite, AllowOverride All)
- MySQL 8
- Composer (PSR-4 автозагрузка), vlucas/phpdotenv

## Быстрый старт
1) Переменные окружения:
Скопируйте файл с примером настроек в рабочий `.env`:
```
cp .env.example .env
```

2) Соберите и запустите контейнеры:
```
docker compose up --build
```

3) Откройте приложение:
- http://localhost:8080
- Прямой заход на `/index.php` автоматически нормализуется в `/`. Если вы не авторизованы — произойдёт редирект на `/login`.

## Роуты
- GET /register — форма регистрации (требуется подтверждение пароля; минимальная длина пароля — 9 символов)
- POST /register — создание пользователя
- GET /login — форма входа
- POST /login — аутентификация
- POST /logout — выход
- GET / — главная с приветствием (требуется вход; иначе редирект на /login)

## REST API
- Базовый URL: `http://localhost:8080`
- Авторизация: заголовок `X-API-KEY: <ваш_ключ>` (см. `.env: API_KEY`)
- Формат: `application/json`

Эндпойнты:
- GET /api/users — список пользователей
- GET /api/users/{id} — получить пользователя по id
- POST /api/users — создать пользователя `{ name, email, password }` (минимальная длина `password` — 9 символов)
- PUT /api/users/{id} — полное обновление `{ name?, email?, password? }` (если передаёте `password`, минимум 9 символов)
- PATCH /api/users/{id} — частичное обновление `{ name?, email?, password? }` (если передаёте `password`, минимум 9 символов)
- DELETE /api/users/{id} — удалить пользователя

Подробная документация с примерами запросов/ответов: см. `docs/API.md`.

## Postman‑коллекция
Готовая коллекция находится в `scr/postman/PVM.postman.json`.

- Переменные:
  - `baseUrl` — по умолчанию `http://localhost:8080`
  - `apiKey` — по умолчанию `dev-secret-key` (приведите в соответствие с `.env`)
  - `userId` — подставляется автоматически тестами после создания пользователя

Как использовать:
1) В Postman нажмите Import → выберите `scr/postman/PVM.postman.json`.
2) Откройте коллекцию → вкладка Variables → при необходимости поправьте `baseUrl` и `apiKey`.
3) Выполните цепочку запросов:
   - List Users → Create User → Get User by ID → Update (PUT) → Partial Update (PATCH) → Delete
4) Встроенные тесты проверяют коды ответов и сохраняют `userId` между шагами.

## Переменные окружения
Файл `.env` (значения по умолчанию для локальной разработки):
- `API_KEY=dev-secret-key` — ключ для REST API (заголовок `X-API-KEY`)
- `DB_HOST=db`, `DB_PORT=3306`, `DB_DATABASE=pvm`, `DB_USERNAME=pvm`, `DB_PASSWORD=pvm`, `DB_CHARSET=utf8mb4`

## База данных
- Начальная схема: `scr/db/init.sql` (применяется автоматически при первом старте MySQL)
- Данные сохраняются в volume, смонтированный в `/var/lib/mysql`
