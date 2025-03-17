<?php
require_once __DIR__ . '/../src/render.php'; // Cargar el archivo de renderizado
require_once __DIR__ . '/../src/controller/DatabaseController.php'; // Cargar el controlador de la base de datos

$db = new DatabaseController(); // Crear una instancia del controlador de la base de datos
$matches = $db->getAllMatches(); // Método que obtenga los partidos
$news = getLatestNews(); // Obtener las últimas noticias
$team_logos = getTeamLogo(); // Obtener los logos de los equipos
$teams_count = getTeamsCountByLeague(); // Obtener el conteo de equipos por liga
render('match_data.html.twig', ['matches' => $matches, 'news' => $news, 'team_logos' => $team_logos, 'teams_count' => $teams_count]); // Renderizar la plantilla con los datos obtenidos
