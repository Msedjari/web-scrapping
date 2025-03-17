<?php

namespace Controller;

require_once "vendor/autoload.php";
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HomeController {
    public function index() {
        $db = new DatabaseController();
        $matches = $db->getMatches();
        $news = $db->getNews();
        $teams = $db->getTeams();

        // Configurar Twig
        $loader = new FilesystemLoader('templates');
        $twig = new Environment($loader);

        echo $twig->render('home.html.twig', [
            'matches' => $matches,
            'news' => $news,
            'teams' => $teams
        ]);
    }
}
?>
