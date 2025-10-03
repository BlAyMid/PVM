<?php
namespace App\controllers;

use App\models\User;

// контроллер главной страницы: вывод приветствия авторизованному пользователю
class HomeController
{
    // рендерит указанный шаблон с данными
    private function render($view, $data = array())
    {
        if (!is_array($data)) { $data = array(); }
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }

    // отображает главную страницу или перенаправляет на /login, если пользователь не вошёл
    public function index()
    {
        $user = null;
        if (!empty($_SESSION['user_id'])) {
            $user = User::findById((int)$_SESSION['user_id']);
        }
        if (!$user) {
            header('Location: /login');
            return;
        }
        $this->render('home', array('user' => $user));
    }
}
