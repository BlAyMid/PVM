<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Главная</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
<main>
    <section>
        <h1>Здравствуйте, <?= htmlspecialchars(($user && isset($user->name)) ? $user->name : 'Гость', ENT_QUOTES, 'UTF-8') ?>!</h1>
        <form method="post" action="/logout">
            <button type="submit">Выйти</button>
        </form>
    </section>

    <section>
        <h2>REST API</h2>
        <p>Для работы API используйте заголовок <code>X-API-KEY</code> со значением из .env.</p>
        <pre>
            GET /api/users
            GET /api/users/{id}
            POST /api/users {"name":"...","email":"...","password":"..."}
            PUT/PATCH /api/users/{id} {"name":"...","email":"...","password":"..."}
            DELETE /api/users/{id}
        </pre>
    </section>
</main>

</body>
</html>
