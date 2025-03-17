<?php

ini_set('display_errors', 1); // Mostrar errores
ini_set('display_startup_errors', 1); // Mostrar errores de inicio
error_reporting(E_ALL); // Reportar todos los errores

use App\Models\User;

require_once __DIR__ . '/../vendor/autoload.php'; // Cargar autoload de Composer
require_once __DIR__ . '/../src/render.php'; // Cargar archivo de renderizado

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates'); // Configurar Twig para cargar plantillas
$twig = new \Twig\Environment($loader); // Crear instancia de Twig
$viewDir = '/views/'; // Directorio de vistas

// Configuración de la base de datos
$servername = "localhost"; // Nombre del servidor
$username = "mouad"; // Nombre de usuario
$password = "MOUAD1234"; // Contraseña
$database = "Fastscore"; // Nombre de la base de datos

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password); // Conexión a la base de datos
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configurar modo de errores

    // Obtener la ruta actual
    $request_uri = $_SERVER['REQUEST_URI']; // URI de la solicitud
    $path = parse_url($request_uri, PHP_URL_PATH); // Ruta de la solicitud

    $error = null; // Inicializar variable de error

    // API Routes - Process API requests first
    if (strpos($request_uri, '/api') === 0) {
        header('Content-Type: application/json');
        
        // Include the API routes
        require_once __DIR__ . '/api-routes.php';
        
        // Handle the API request and output the response
        $response = handleApiRequest($request_uri);
        echo json_encode($response);
        exit;
    }

    // Regular web routes (only processed if the request wasn't an API request)
    switch ($path) {
        case '/login':
            if (SessionController::isLoggedIn()) {
                redirect("/adminDashboard"); // Redirigir a la página de administración si está logueado
                break;
            } else {
                require __DIR__ . $viewDir . 'login.php'; // Cargar vista de login
                break;
            }

        case '/logout':
            require __DIR__ . $viewDir . 'logout.php'; // Cargar vista de logout
            break;

        case '/register':
            if (SessionController::isLoggedIn()) {
                redirect("/adminDashboard"); // Redirigir a la página de administración si está logueado
                break;
            } else {
                require __DIR__ . $viewDir . 'register.php'; // Cargar vista de registro
                break;
            }
            case '/':
                if (!SessionController::isLoggedIn()) { // Verificar si no está logueado
                    redirect("/login"); // Redirigir a la página de inicio de sesión
                    break;
                }
                // Página de inicio
                echo $twig->render('home.html.twig', [
                    'matches' => getLatestMatches(), // Obtener últimos partidos
                    'news' => getLatestNews(), // Obtener últimas noticias
                    'team_logos' => getTeamLogo(), // Obtener logos de equipos
                    'teams_count' => getTeamsCountByLeague(), // Obtener cantidad de equipos por liga
                ]);
                break;

        case '/matches':
            // Página de partidos
            echo $twig->render('matches.html.twig', [
                'matches' => getAllMatches(), // Obtener todos los partidos
                'team_logos' => getTeamLogo() // Obtener logos de equipos
            ]);
            break;

        case '/news':
            // Página de noticias
            echo $twig->render('news.html.twig', [
                'news' => getAllNews() // Obtener todas las noticias
            ]);
            break;

        case '/teams':
            // Página de equipos
            echo $twig->render('teams.html.twig', [
                'teams' => getAllTeams() // Obtener todos los equipos
            ]);
            break;
            
        case '/adminDashboard':
            // Página de administración
            echo $twig->render('adminDashboard.html.twig', [
                'teams' => getAllTeams() // Obtener todos los equipos
            ]);
            break;    
            
        

        case '/adminEquipos':
            // Página de administración de equipos
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = $_POST['id'];
                $club_name = $_POST['club_name'];
                $league_name = $_POST['league_name'];
                $stadium = $_POST['stadium'];
                $coach = $_POST['coach'];
                addTeam($id, $club_name, $league_name, $stadium, $coach); // Agregar equipo
                header("Location: /adminEquipos"); // Redirigir a la página de administración de equipos
                exit();
            } else {
                echo $twig->render('adminEquipos.html.twig', [
                    'teams' => getAllTeams() // Obtener todos los equipos
                ]);
            }
            break;

        case (preg_match('/\/adminEquiposId\/(details|edit|delete)\/(\d+)/', $_SERVER['REQUEST_URI'], $matches) ? true : false):
            $action = $matches[1]; // Acción (detalles, editar, eliminar)
            $id = $matches[2]; // ID del equipo
            
            switch ($action) {
                case 'details':
                    echo $twig->render('adminEquiposIdDetails.html.twig', [
                        'team' => getTeamById($id), // Obtener equipo por ID
                    ]);
                    break;
                case 'edit':
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $club_name = $_POST['club_name'];
                        $league_name = $_POST['league_name'];
                        $stadium = $_POST['stadium'];
                        $coach = $_POST['coach'];
                        updateTeam($id, $club_name, $league_name, $stadium, $coach); // Actualizar equipo
                        header("Location: /adminEquiposId/details/$id"); // Redirigir a la página de detalles del equipo
                        exit();
                    } else {
                        echo $twig->render('adminEquiposIdEdit.html.twig', [
                            'team' => getTeamById($id) // Obtener equipo por ID
                        ]);
                    }
                    break;
                case 'delete':
                    deleteTeamById($id); // Eliminar equipo por ID
                    header("Location: /adminEquipos/delete/$id"); // Redirigir a la página de administración de equipos
                    break;
                default:
                    header("HTTP/1.0 404 Not Found"); // Página no encontrada
                    echo $twig->render('404.html'); // Renderizar vista 404
                    break;
            }
            break;

        default:
            // Página 404
            header("HTTP/1.0 404 Not Found"); // Página no encontrada
            echo $twig->render('404.html'); // Renderizar vista 404
            break;
    }

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage()); // Manejar error de conexión
}

// Funciones auxiliares para obtener datos (deberás implementarlas según tu base de datos)
function getLatestMatches() {
    // Lógica para obtener los últimos partidos
    global $conn;
    $stmt = $conn->prepare("SELECT league_name, team_home, team_away, score_home, score_away, match_time FROM match_data ORDER BY match_time DESC LIMIT 3"); // Consulta para obtener los últimos partidos
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver resultados
}

function getAllMatches() {
    global $conn;
    $stmt = $conn->prepare("SELECT league_name, team_home, team_away, score_home, score_away, match_time FROM match_data ORDER BY league_name"); // Consulta para obtener todos los partidos
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver resultados
}

function getLatestNews() {
    global $conn;
    $stmt = $conn->prepare("SELECT title, text FROM news_data WHERE news_data.content IS NOT NULL ORDER BY LENGTH(content) Desc LIMIT 3"); // Consulta para obtener las últimas noticias
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver resultados
}

function getAllNews() {
    global $conn;
    $stmt = $conn->prepare("SELECT title, text, content FROM news_data WHERE news_data.content IS NOT NULL ORDER BY LENGTH(content)"); // Consulta para obtener todas las noticias
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver resultados
}

function getAllTeams() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM team_info where team_info.club_name is not null"); // Consulta para obtener información de equipos
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver resultados
}

function addTeam($id, $club_name, $league_name, $stadium, $coach) {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO team_info (id, club_name, league_name, stadium, coach) VALUES (:id, :club_name, :league_name, :stadium, :coach)"); // Consulta para agregar equipo
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Vincular parámetro ID
        $stmt->bindParam(':club_name', $club_name, PDO::PARAM_STR); // Vincular parámetro nombre del club
        $stmt->bindParam(':league_name', $league_name, PDO::PARAM_STR); // Vincular parámetro nombre de la liga
        $stmt->bindParam(':stadium', $stadium, PDO::PARAM_STR); // Vincular parámetro estadio
        $stmt->bindParam(':coach', $coach, PDO::PARAM_STR); // Vincular parámetro entrenador
        $stmt->execute(); // Ejecutar consulta
        return true; // Devolver verdadero si se agregó correctamente
    } catch (PDOException $e) {
        error_log("Error adding team: " . $e->getMessage()); // Registrar error
        return false; // Devolver falso si hubo un error
    }
}

function updateTeam($id, $club_name, $league_name, $stadium, $coach) {
    global $conn;
    try {
        $stmt = $conn->prepare("UPDATE team_info SET club_name = :club_name, league_name = :league_name, stadium = :stadium, coach = :coach WHERE id = :id AND logo_url IS NOT NULL"); // Consulta para actualizar equipo
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Vincular parámetro ID
        $stmt->bindParam(':club_name', $club_name, PDO::PARAM_STR); // Vincular parámetro nombre del club
        $stmt->bindParam(':league_name', $league_name, PDO::PARAM_STR); // Vincular parámetro nombre de la liga
        $stmt->bindParam(':stadium', $stadium, PDO::PARAM_STR); // Vincular parámetro estadio
        $stmt->bindParam(':coach', $coach, PDO::PARAM_STR); // Vincular parámetro entrenador
        $stmt->execute(); // Ejecutar consulta
        return true; // Devolver verdadero si se actualizó correctamente
    } catch (PDOException $e) {
        error_log("Error updating team: " . $e->getMessage()); // Registrar error
        return false; // Devolver falso si hubo un error
    }
}

function deleteTeamById($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM team_info WHERE id = :id"); // Consulta para eliminar equipo por ID
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Vincular parámetro ID
    return $stmt->execute(); // Ejecutar consulta y devolver resultado
}

function getTeamById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM team_info WHERE id = :id"); // Consulta para obtener equipo por ID
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Vincular parámetro ID
    $stmt->execute(); // Ejecutar consulta
    return $stmt->fetch(PDO::FETCH_ASSOC); // Devolver resultado
}

function getTeamLogo() {
    global $conn;
    $stmt = $conn->prepare("SELECT team_name, league_name, logo_path FROM team_logos"); // Consulta para obtener logos de equipos
    $stmt->execute(); // Ejecutar consulta
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver resultados
}

function getLeagueLogo($league_name) {
    global $conn;
    $stmt = $conn->prepare("SELECT logo_path FROM league_logos WHERE league_name = :league_name"); // Consulta para obtener logo de liga
    $stmt->bindParam(':league_name', $league_name, PDO::PARAM_STR); // Vincular parámetro nombre de la liga
    $stmt->execute(); // Ejecutar consulta
    return $stmt->fetch(PDO::FETCH_ASSOC); // Devolver resultado
}

// Función para obtener un artículo por ID
function getArticleById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT title, text, content, date FROM news_data WHERE id = :id"); // Consulta para obtener artículo por ID
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Vincular parámetro ID
    $stmt->execute(); // Ejecutar consulta
    return $stmt->fetch(PDO::FETCH_ASSOC); // Devolver resultado
}

function getTeamsCountByLeague() {
    global $conn;
    $stmt = $conn->prepare("SELECT league_name, COUNT(*) as team_count FROM team_logos WHERE team_name IS NOT NULL GROUP BY league_name"); // Consulta para obtener cantidad de equipos por liga
    $stmt->execute(); // Ejecutar consulta
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devolver resultados
}

// Función para redirigir
function redirect($url) {
    header("Location: $url"); // Redirigir a la URL especificada
    exit(); // Salir del script
}