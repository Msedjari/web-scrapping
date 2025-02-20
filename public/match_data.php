<?php
require_once __DIR__ . '/../src/render.php';
require_once __DIR__ . '/../src/controller/DatabaseController.php';

$db = new DatabaseController();
$matches = $db->getMatches(); // Método que obtenga los partidos

render('match_data.html.twig', ['matches' => $matches]);
