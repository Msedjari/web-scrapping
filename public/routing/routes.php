<?php

use App\Controller\DatabaseController;
use App\Controller\AuthController;

// Función para gestionar rutas
function route($path) {
    $routes = [
        '/' => [DatabaseController::class, 'home'],
        '/login' => [AuthController::class, 'login'],
    ];

    if (isset($routes[$path])) {
        [$controller, $method] = $routes[$path];
        (new $controller())->$method();
    } else {
        http_response_code(404);
        echo "Página no encontrada.";
    }
}

// Llamar a la función de ruteo
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
route($requestPath);
