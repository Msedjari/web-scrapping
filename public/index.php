<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/render.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

// Configuración de la base de datos
$servername = "localhost";
$username = "mouad";
$password = "MOUAD1234"; 
$database = "Fastscore";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener la ruta actual
    $request_uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($request_uri, PHP_URL_PATH);

    // Enrutamiento
    switch ($path) {
        case '/':
            // Página de inicio
            echo $twig->render('home.html.twig', [
                'matches' => getLatestMatches(),
                'news' => getLatestNews(),
                'team_stats' => getTeamStats()
            ]);
            break;

        case '/matches':
            // Página de partidos
            echo $twig->render('matches.html.twig', [
                'matches' => getAllMatches()
            ]);
            break;

        case '/news':
            // Página de noticias
            echo $twig->render('news.html.twig', [
                'news' => getAllNews()
            ]);
            break;

        case '/teams':
            // Página de equipos
            echo $twig->render('teams.html.twig', [
                'teams' => getAllTeams()
            ]);
            break;

        case '/statistics':
            // Página de estadísticas
            echo $twig->render('statistics.html.twig', [
                'players' => getPlayerStats(),
                'team_stats' => getTeamStats()
            ]);
            break;

        default:
            // Página 404
            header("HTTP/1.0 404 Not Found");
            echo $twig->render('404.html.twig');
            break;
    }

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Funciones auxiliares para obtener datos (deberás implementarlas según tu base de datos)
function getLatestMatches() {
    // Implementar la lógica para obtener los últimos partidos
}

function getAllMatches() {
    // Implementar la lógica para obtener todos los partidos
}

function getLatestNews() {
    // Implementar la lógica para obtener las últimas noticias
}

function getAllNews() {
    // Implementar la lógica para obtener todas las noticias
}

function getAllTeams() {
    // Implementar la lógica para obtener todos los equipos
}

function getTeamStats() {
    // Implementar la lógica para obtener las estadísticas de los equipos
}

function getPlayerStats() {
    // Implementar la lógica para obtener las estadísticas de los jugadores
}
?>
