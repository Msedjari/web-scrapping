<?php
require_once __DIR__ . '/../src/render.php';
require_once __DIR__ . '/../src/controller/DatabaseController.php';

$db = new DatabaseController();
$matches = $db->getAllMatches(); // Método que obtenga los partidos
$news = getLatestNews();
$team_logos = getTeamLogo();
$teams_count = getTeamsCountByLeague();
render('match_data.html.twig', ['matches' => $matches, 'news' => $news, 'team_logos' => $team_logos, 'teams_count' => $teams_count]);

