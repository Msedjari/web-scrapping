<?php
require '../config.php';
require '../src/controller/AuthController.php';

$auth = new AuthController($twig, $pdo);
$auth->login();
?>
