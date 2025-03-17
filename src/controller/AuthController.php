<?php

namespace App\Controller; // Definir el namespace para la clase AuthController

use Twig\Environment; // Importar la clase Environment de Twig

class AuthController { // Definir la clase AuthController
    private $twig; // Declarar una propiedad privada para almacenar la instancia de Twig

    public function __construct() { // Definir el constructor de la clase
        $this->twig = require __DIR__ . '/../render.php'; // Cargar y asignar la instancia de Twig desde el archivo render.php
    }

    public function login() { // Definir el método login
        echo $this->twig->render('login.html.twig', ['error' => null]); // Renderizar la plantilla de login con Twig y pasar un parámetro de error nulo
    }

    public function logout() { // Definir el método logout
        session_start(); // Iniciar la sesión
        session_destroy(); // Destruir la sesión
        header('Location: /login'); // Redirigir a la página de login
    }
}
?>