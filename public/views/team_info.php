<?php
require 'DatabaseController.php'; // Requiere el controlador de la base de datos
require 'twigController.php'; // Requiere el controlador de Twig

$db = new DatabaseController(); // Crea una nueva instancia del controlador de la base de datos
$teams = $db->query("SELECT club_name, league, president, coach, foundation_year, stadium_name, stadium_capacity FROM team_info"); // Consulta la información de los equipos

echo $twig->render('team_info.html', ['teams' => $teams]); // Renderiza la plantilla de Twig con la información de los equipos
