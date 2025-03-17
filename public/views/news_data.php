<?php
require 'DatabaseController.php'; // Cargar el controlador de la base de datos
require 'twigController.php'; // Cargar el controlador de Twig

$db = new DatabaseController(); // Crear una instancia del controlador de la base de datos
$news = $db->query("SELECT title, text FROM news_data"); // Consultar los datos de noticias desde la base de datos

echo $twig->render('news_data.html', ['news' => $news]); // Renderizar la plantilla de Twig con los datos de noticias
