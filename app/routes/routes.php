<?php
use App\core\Router;
use App\controllers\HomeController;
use App\controllers\AuthController;
use App\controllers\UserApiController;

$router = new Router();

// веб-маршруты
$router->add('GET', '/', [new HomeController(), 'index']);
$router->add('GET', '/register', [new AuthController(), 'showRegister']);
$router->add('POST', '/register', [new AuthController(), 'register']);
$router->add('GET', '/login', [new AuthController(), 'showLogin']);
$router->add('POST', '/login', [new AuthController(), 'login']);
$router->add('POST', '/logout', [new AuthController(), 'logout']);

// api-маршруты
$api = new UserApiController();
$router->add('GET', '/api/users', [$api, 'index']);
$router->add('GET', '/api/users/{id}', [$api, 'show']);
$router->add('POST', '/api/users', [$api, 'create']);
$router->add('PUT', '/api/users/{id}', [$api, 'update']);
$router->add('PATCH', '/api/users/{id}', [$api, 'update']);
$router->add('DELETE', '/api/users/{id}', [$api, 'delete']);

return $router;
