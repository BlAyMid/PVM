<?php
namespace App\controllers;

use App\models\User;

// контроллер аутентификации: регистрация, вход и выход
class AuthController
{
    // рендерит указанный шаблон с данными
    private function render($view, $data = array())
    {
        if (!is_array($data)) { $data = array(); }
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }

    // показывает форму регистрации
    public function showRegister()
    {
        $this->render('register');
    }

    // обрабатывает отправку формы регистрации
    public function register()
    {
        $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
        $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 9 || $password !== $passwordConfirm) {
            $this->render('register', array('error' => 'Введите корректные данные: пароль от 9 символов и совпадает с подтверждением.'));
            return;
        }
        if (User::findByEmail($email)) {
            $this->render('register', array('error' => 'Email уже зарегистрирован.'));
            return;
        }
        $user = User::create($name, $email, $password);
        $_SESSION['user_id'] = $user->id;
        header('Location: /');
    }

    // показывает форму входа
    public function showLogin()
    {
        $this->render('login');
    }

    // обрабатывает вход пользователя
    public function login()
    {
        $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user->password_hash)) {
            $this->render('login', array('error' => 'Неверный логин или пароль.'));
            return;
        }
        $_SESSION['user_id'] = $user->id;
        header('Location: /');
    }

    // завершает сессию и перенаправляет на страницу входа
    // @return void
    public function logout()
    {
        session_destroy();
        header('Location: /login');
    }
}
