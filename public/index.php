<?php
use App\core\Router;

require __DIR__ . '/../vendor/autoload.php';

// загружаем переменные окружения из .env (если доступен dotenv)
$dotenvPath = dirname(__DIR__);
if (file_exists($dotenvPath . '/.env')) {
    if (class_exists('Dotenv\\Dotenv')) {
        Dotenv\Dotenv::createImmutable($dotenvPath)->load();
    }
}

// запускаем сессию для веб-аутентификации
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// подключаем таблицу маршрутов и выполняем диспетчеризацию
// @var Router $router
$router = require __DIR__ . '/../app/routes/routes.php';
$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

// нормализуем прямой доступ к index.php до корня '/'
$path = parse_url($uri, PHP_URL_PATH);
if ($path === false || $path === null) { $path = '/'; }
if (basename($path) === 'index.php') {
    $uri = '/';
}

$router->dispatch($method, $uri);
