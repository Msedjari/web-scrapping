<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/render.php';
// Incluir el ruteador principal
require_once __DIR__ . '/routes.php';
use App\Controller\DatabaseController;

// Configuración de rutas
function route($path) {
    $routes = [
        '/' => [DatabaseController::class, 'home'],
    ];

    if (isset($routes[$path])) {
        [$controller, $method] = $routes[$path];
        (new $controller())->$method();
    } else {
        http_response_code(404);
        echo "Página no encontrada.";
    }
}

// Inicia el ruteo
$routePath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
route($routePath);
