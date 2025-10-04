<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Вход</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main>
    <section>
        <section>
            <h1>Вход</h1>
            <?php if (!empty($error)): ?>
                <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </section>
        <section>
            <form method="post" action="/login">
                <label>Email<br>
                    <input type="email" name="email" required>
                </label><br>
                <label>Пароль<br>
                    <input type="password" name="password" required>
                </label><br>
                <button type="submit">Войти</button>
            </form>
            <p>Нет аккаунта? <a href="/register">Зарегистрируйтесь</a></p>
        </section>
    </section>
</main>

</body>
</html>
