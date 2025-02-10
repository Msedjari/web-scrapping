<?php

namespace App\Controller;

use Twig\Environment;

class AuthController {
    private $twig;

    public function __construct() {
        $this->twig = require __DIR__ . '/../render.php';
    }

    public function login() {
        echo $this->twig->render('login.html.twig', ['error' => null]);
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /');
    }
}
