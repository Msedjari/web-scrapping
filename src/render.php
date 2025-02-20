<?php
require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false, // Cambiar a __DIR__ . '/../cache' en producción
]);

function render($template, $params = []) {
    global $twig;
    echo $twig->render($template, $params);
}
