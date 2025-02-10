<?php

namespace App\Controller;

use PDO;

class DatabaseController {
    private $twig;

    public function __construct() {
        $this->twig = require __DIR__ . '/../render.php';
    }

    public function home() {
        echo $this->twig->render('home.html.twig', [
            'title' => 'Inicio'
        ]);
    }
}
