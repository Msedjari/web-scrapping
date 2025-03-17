<?php
//print_r($_SESSION); // Imprimir la sesión para depuración
//Cargamos el archivo twig

// Verificación de que el usuario esté conectado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST["username"]); // Sanitizar el nombre de usuario
    $password = htmlspecialchars($_POST["password"]); // Sanitizar la contraseña

    $sessionController = new SessionController(); // Crear una instancia del controlador de sesión sin argumentos

    if ($sessionController->userLogIn($username, $password)) { // Intentar iniciar sesión
        redirect("/"); // Redirigir a la página principal si el login es exitoso
    } else {
        // Si el login falla, establecer un error en la sesión
        $_SESSION['error'] = "Nombre de usuario o contraseña incorrectos."; // Mensaje de error
    }
}

// ---- Renderizar plantilla ----
echo $twig->render('login.html.twig'); // Renderizar la plantilla de Twig