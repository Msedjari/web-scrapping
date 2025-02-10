<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . './public/routing/bootstrap.php';

$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
route($route);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carga la configuración del proyecto
require_once __DIR__ . '/../routing/bootstrap.php';