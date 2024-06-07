<?php

class Route {
    private static $routes = [];

    public static function post($route, $controller, $method) {
        self::$routes['POST'][$route] = ['controller' => $controller, 'method' => $method];
    }

    public static function get($route, $controller, $method) {
        self::$routes['GET'][$route] = ['controller' => $controller, 'method' => $method];
    }

    public static function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = strtok($_SERVER['REQUEST_URI'], '?'); // Убираем query string из URI

        if (isset(self::$routes[$requestMethod][$requestUri])) {
            $route = self::$routes[$requestMethod][$requestUri];
            $controllerName = $route['controller'];
            $methodName = $route['method'];

            if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
                $controller = new $controllerName();
                $controller->$methodName();
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "Controller or method not found!";
            }
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Route not found!";
        }
    }
}
?>
