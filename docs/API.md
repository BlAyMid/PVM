# Документация REST API

Эта документация описывает REST API пользовательского сервиса.

- Базовый URL: `http://localhost:8080`
- Авторизация: заголовок `X-API-KEY: <ваш_ключ>` (значение хранится в `.env`, переменная `API_KEY`)
- Формат данных: `application/json`

## Заголовки
- `X-API-KEY: <ваш_ключ>` — обязателен для всех запросов
- `Content-Type: application/json` — обязателен для методов с телом запроса (POST/PUT/PATCH)

## Формат ошибок
Все ошибки возвращаются в формате JSON:
```
{
  "error": "Сообщение об ошибке"
}
```
Коды ошибок:
- 400 — неверный запрос (в частных случаях)
- 401 — неавторизовано (неверный или отсутствующий API ключ)
- 404 — ресурс не найден
- 409 — конфликт (например, email уже существует)
- 422 — некорректные данные (валидация не пройдена)
- 500 — внутренняя ошибка сервера

Примечание: подтверждение пароля (второе поле) требуется только в веб‑форме регистрации. В REST API передаётся одно поле `password`.

## Модель пользователя (User)
Пример объекта пользователя в ответе:
```
{
  "id": 1,
  "name": "Alice",
  "email": "alice@example.com",
  "created_at": "2025-10-03 10:20:30"
}
```

## Эндпойнты
### 1) Получить список пользователей
GET `/api/users`

Пример запроса:
```
curl -H "X-API-KEY: dev-secret-key" \
  http://localhost:8080/api/users
```

Успешный ответ (200):
```
[
  { "id": 2, "name": "Bob", "email": "bob@example.com", "created_at": "..." },
  { "id": 1, "name": "Alice", "email": "alice@example.com", "created_at": "..." }
]
```

### 2) Получить пользователя по id
GET `/api/users/{id}`
Пример запроса:
```
curl -H "X-API-KEY: dev-secret-key" \
  http://localhost:8080/api/users/1
```

Ответ (200): объект пользователя. Ошибка (404):
```
{ "error": "Not found" }
```

### 3) Создать пользователя
POST `/api/users`
Тело запроса:
```
{
  "name": "Alice",
  "email": "alice@example.com",
  "password": "secret123"
}
```
Требования:
- name — непустая строка
- email — валидный email, уникален
- password — минимум 9 символов

Пример запроса:
```
curl -X POST -H "Content-Type: application/json" -H "X-API-KEY: dev-secret-key" \
  -d '{"name":"Alice","email":"alice@example.com","password":"secret123"}' \
  http://localhost:8080/api/users
```

Ответ (201): созданный пользователь. Ошибки:
- 409 — `{ "error": "Email already exists" }`
- 422 — `{ "error": "Invalid payload" }`

### 4) Обновить пользователя (PUT)
PUT `/api/users/{id}`
Тело запроса (передавайте только изменяемые поля):
```
{
  "name": "New Name",
  "email": "new@example.com",
  "password": "newpassword"
}
```

Пример запроса:
```
curl -X PUT -H "Content-Type: application/json" -H "X-API-KEY: dev-secret-key" \
  -d '{"name":"New Name","email":"new@example.com","password":"secret456"}' \
  http://localhost:8080/api/users/1
```

Ответ (200): обновлённый пользователь. Ошибки:
- 404 — `{ "error": "Not found" }`
- 422 — `{ "error": "Invalid email" }` (если email указан, но невалиден)
- 422 — `{ "error": "Password too short (min 9)" }` (если указан `password`, но короче 9 символов)

Примечание: в данной реализации PUT и PATCH работают одинаково (частичное обновление) — можно передавать только те поля, которые нужно изменить.

### 5) Частичное обновление (PATCH)
PATCH `/api/users/{id}`
Тело запроса (пример):
```
{ "email": "partial-update@example.com" }
```

Пример запроса:
```
curl -X PATCH -H "Content-Type: application/json" -H "X-API-KEY: dev-secret-key" \
  -d '{"email":"partial-update@example.com"}' \
  http://localhost:8080/api/users/1
```

Ответ (200): обновлённый пользователь. Ошибки:
- 404 — `{ "error": "Not found" }`
- 422 — `{ "error": "Invalid email" }` (если email указан, но невалиден)
- 422 — `{ "error": "Password too short (min 9)" }` (если указан `password`, но короче 9 символов)

### 6) Удалить пользователя
DELETE `/api/users/{id}`
Пример запроса:
```
curl -X DELETE -H "X-API-KEY: dev-secret-key" \
  http://localhost:8080/api/users/1
```

Ответ (200):
```
{ "status": "deleted" }
```
Ошибка (404): `{ "error": "Not found" }`.

## Postman
Готовая коллекция находится здесь: `scr/postman/PVM.postman_collection.json`.

Быстрый старт:
1) Импортируйте коллекцию в Postman.
2) Проверьте переменные `baseUrl` и `apiKey` в коллекции.
3) Выполните последовательность запросов: List → Create → Get → Update (PUT) → Patch → Delete.

## Советы
- Если получаете 401 — проверьте заголовок `X-API-KEY` и его значение в `.env`.
- Для массовой проверки используйте Runner в Postman.
- Во избежание конфликтов email при тестировании POST добавляйте случайность (в коллекции используется `{{$timestamp}}`).
