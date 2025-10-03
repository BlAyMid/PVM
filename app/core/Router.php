<?php
namespace App\core;

// класс маршрутизатора для регистрации и обработки http-маршрутов
class Router
{
    // @var array ассоциативный массив зарегистрированных маршрутов по методам
    private $routes = array();

    // регистрирует обработчик маршрута
    public function add($method, $path, $handler)
    {
        $method = strtoupper($method);
        $normalized = $this->normalize($path);
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = array();
        }
        $this->routes[$method][$normalized] = $handler;
    }

    // выполняет поиск подходящего маршрута и вызывает обработчик
    public function dispatch($method, $uri)
    {
        $method = strtoupper($method);
        $parsed = parse_url($uri, PHP_URL_PATH);
        $path = $this->normalize($parsed !== null ? $parsed : '/');

        // точное совпадение маршрута
        if (isset($this->routes[$method][$path])) {
            return call_user_func($this->routes[$method][$path]);
        }

        // поддержка параметров пути вида /users/{id}
        $methodRoutes = isset($this->routes[$method]) ? $this->routes[$method] : array();
        foreach ($methodRoutes as $route => $handler) {
            $pattern = preg_replace('#\{[^/]+}#', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                return call_user_func_array($handler, $matches);
            }
        }

        // если маршрут не найден, отдаём 404 в формате json
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(array('error' => 'Not found'));
        return null;
    }

    // нормализует путь (гарантирует ведущий слэш, убирает лишние)
    private function normalize($path)
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
