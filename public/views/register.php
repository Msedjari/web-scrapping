<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\DatabaseController;
use App\Service\User;

// Iniciar la conexión a la base de datos
$db = DatabaseController::getInstance();
$db->connect();
$pdo = $db->connect();

// Instancia del servicio de usuario
$userService = new User($pdo);

$error = "";

// Manejo de formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validaciones básicas
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } elseif (strlen($password) < 3) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Intentar registrar al usuario
        $success = $userService->register($username, $email, $password);
        
        if ($success) {
            header("Location: /login");
            exit();
        } else {
            $error = "Error al registrar el usuario.";
        }
    }
}

// Cargar la plantilla Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('register.html.twig', ['error' => $error]);

?>
