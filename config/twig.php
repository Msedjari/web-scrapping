<?php
// Iniciamos la sesión
session_start();

// Cargamos Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Configuración de Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../public/templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false, // Desactivar caché en desarrollo (en producción usa una ruta de caché)
    'autoescape' => 'html', // Escapar automáticamente para seguridad (recomendado)
    'debug' => true, // Activar modo debug para desarrollo
]);

// Añadir extensión de depuración (solo en desarrollo)
$twig->addExtension(new \Twig\Extension\DebugExtension());

// Filtro para usar gettext {{ "Texto a traducir"|gettext }}
class GettextExtension extends \Twig\Extension\AbstractExtension {
    public function getFilters() {
        return [
            new \Twig\TwigFilter('gettext', 'gettext'),
        ];
    }
}

// Añadir el filtro de gettext a Twig
$twig->addExtension(new GettextExtension());

// Pasar las variables de sesión al entorno de Twig
$twig->addGlobal('session', $_SESSION);

// Limpiar las variables de sesión después de cargarlas
function clearSessionMessages() {
    unset($_SESSION['error'], $_SESSION['success']);
}
clearSessionMessages();

// Devolver la instancia de Twig
return $twig;