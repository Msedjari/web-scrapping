<?php

ini_set('display_errors', 1); // Configurar para mostrar errores
ini_set('display_startup_errors', 1); // Configurar para mostrar errores de inicio
error_reporting(E_ALL); // Reportar todos los errores
//Cargamos el archivo twig
require_once __DIR__ . '../../../config/twig.php'; // Cargar la configuración de Twig

// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username = $_POST["username"]; // Obtener el nombre de usuario del formulario
    $email = $_POST["email"]; // Obtener el email del formulario
    $password = $_POST["password"]; // Obtener la contraseña del formulario
    $confirm_password = $_POST["confirm_password"]; // Obtener la confirmación de la contraseña

    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        die("Las contraseñas no coinciden. Por favor, vuelve e intenta de nuevo."); // Terminar si las contraseñas no coinciden
    }else{

        // Llamar a la función userSignUp
        if( SessionController::userSignUp($username, $email, $password)){ // Intentar registrar al usuario
            header("Location: /login"); // Redirigir al login si el registro es exitoso
        }else {
            // Si el login falla, establecer un error en la sesión
            $_SESSION['error'] = "Registro fallido"; // Establecer mensaje de error en la sesión
        }

    }
    
}

// ---- Renderizar plantilla ----
echo $twig->render('register.html.twig'); // Renderizar la plantilla de registro