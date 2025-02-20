<?php
require 'DatabaseController.php';
require 'twigController.php';

$db = new DatabaseController();
$teams = $db->query("SELECT club_name, league, president, coach, foundation_year, stadium_name, stadium_capacity FROM team_info");

echo $twig->render('team_info.html', ['teams' => $teams]);
