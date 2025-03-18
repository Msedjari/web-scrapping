<?php

require_once '../src/controller/LanguageController.php';

use App\Controller\LanguageController;
use Twig\TwigFunction;
// Initialize LangController
$langController = new LanguageController();
$translations = $langController->getTranslations();

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

$locale = $langController->detectUserLocale();

// Add translation function to Twig
$twig->addFunction(new TwigFunction('trans', function ($key) use ($translations) {
    return $translations[$key] ?? $key;
}));




// Devolver la instancia de Twig con el locale
return $twig;