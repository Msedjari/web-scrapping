<?php
require 'DatabaseController.php';
require 'twigController.php';

$db = new DatabaseController();
$news = $db->query("SELECT title, text FROM news_data");

echo $twig->render('news_data.html', ['news' => $news]);
