<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main>
    <section>
        <h1>Регистрация</h1>
        <?php if (!empty($error)): ?>
            <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </section>
    <section>
        <form method="post" action="/register">
            <label>Имя<br>
                <input type="text" name="name" required>
            </label><br>
            <label>Email<br>
                <input type="email" name="email" required>
            </label><br>
            <label>Пароль<br>
                <input type="password" name="password" minlength="9" required>
            </label><br>
            <label>Повторите пароль<br>
                <input type="password" name="password_confirm" minlength="9" required>
            </label><br>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <p>Уже есть аккаунт? <a href="/login">Войти</a></p>
    </section>
</main>
</body>
</html>
