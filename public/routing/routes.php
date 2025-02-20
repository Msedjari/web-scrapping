<?php

use Controller\HomeController;

require_once __DIR__ . '/../vendor/autoload.php';

$home = new HomeController();
$home->index();

?>
