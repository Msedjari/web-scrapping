<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Models\User;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/render.php';
require_once __DIR__ . '/../public/views/User.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);
$viewDir = '/views/';

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

    $error = null;

    
    // Enrutamiento
    switch ($path) {
        case '/':
            // Página de inicio
            echo $twig->render('home.html.twig', [
                'matches' => getLatestMatches(),
                'news' => getLatestNews(),
                'team_logos' => getTeamLogo(),
                'teams_count' => getTeamsCountByLeague(),
                
            ]);
            break;

        case '/matches':
            
            echo $twig->render('matches.html.twig', [
                'matches' => getAllMatches(),
                'team_logos' => getTeamLogo()
                
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
            
        case '/login':
            // Página de inicio de sesión
            echo $twig->render('login.html.twig');
            break;
            case '/register':
                require __DIR__ . $viewDir . 'register.php';
                    break;
                

        default:
            // Página 404
            header("HTTP/1.0 404 Not Found");
            echo $twig->render('404.html');
            break;
    }

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Funciones auxiliares para obtener datos (deberás implementarlas según tu base de datos)
function getLatestMatches() {
    //la lógica para obtener los últimos partidos
    global $conn;
    $stmt = $conn->prepare("SELECT league_name, team_home, team_away, score_home, score_away, match_time FROM match_data ORDER BY match_time DESC LIMIT 3");                                       
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllMatches() {
    global $conn;
    $stmt = $conn->prepare("SELECT league_name, team_home, team_away, score_home, score_away, match_time FROM match_data ORDER BY league_name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLatestNews() {
    global $conn;
    $stmt = $conn->prepare("SELECT title, text FROM news_data WHERE news_data.content IS NOT NULL ORDER BY LENGTH(content) Desc LIMIT 3"); // Cambiamos LIMIT 5 por LIMIT 3
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllNews() {
    global $conn;
    $stmt = $conn->prepare("SELECT title, text, content FROM news_data WHERE news_data.content IS NOT NULL ORDER BY LENGTH(content)");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllTeams() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM team_info where team_info.club_name is not null"); // Consulta para obtener información de equipos
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getTeamLogo() {
    global $conn;
    $stmt = $conn->prepare("SELECT team_name, league_name, logo_path FROM team_logos");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLeagueLogo($league_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT logo_path FROM league_logos WHERE league_name = :league_name");
    $stmt->bindParam(':league_name', $league_name, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// Función para obtener un artículo por ID
function getArticleById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT title, text, content, date FROM news_data WHERE id = $id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getTeamsCountByLeague() {
    global $conn;
    $stmt = $conn->prepare("SELECT league_name, COUNT(*) as team_count FROM team_logos WHERE team_name IS NOT NULL GROUP BY league_name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}
?>