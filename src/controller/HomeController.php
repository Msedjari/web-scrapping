<?php

namespace Controller; // Definir el namespace para la clase HomeController

require_once "vendor/autoload.php"; // Cargar el autoloader de Composer
use Twig\Environment; // Importar la clase Environment de Twig
use Twig\Loader\FilesystemLoader; // Importar la clase FilesystemLoader de Twig

class HomeController { // Definir la clase HomeController
    public function index() { // Definir el método index
        $db = new DatabaseController(); // Crear una instancia del controlador de la base de datos
        $matches = $db->getMatches(); // Obtener los partidos desde la base de datos
        $news = $db->getNews(); // Obtener las noticias desde la base de datos
        $teams = $db->getTeams(); // Obtener los equipos desde la base de datos

        // Configurar Twig
        $loader = new FilesystemLoader('templates'); // Crear un cargador de archivos para Twig
        $twig = new Environment($loader); // Crear una instancia de Twig con el cargador de archivos

        echo $twig->render('home.html.twig', [ // Renderizar la plantilla home.html.twig con Twig
            'matches' => $matches, // Pasar los partidos a la plantilla
            'news' => $news, // Pasar las noticias a la plantilla
            'teams' => $teams // Pasar los equipos a la plantilla
        ]);
    }
}
?>
