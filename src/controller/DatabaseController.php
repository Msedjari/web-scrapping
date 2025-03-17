<?php

namespace App\Controller; // Definimos el namespace para la clase DatabaseController

use PDO; // Importamos la clase PDO para la conexión a la base de datos
use PDOException; // Importamos la clase PDOException para manejar excepciones de PDO

class DatabaseController { // Definimos la clase DatabaseController
    private $conn; // Declaramos una propiedad privada para la conexión
    private static $host = "localhost"; // Definimos el host de la base de datos
    private static $dbname = "Fastscore"; // Definimos el nombre de la base de datos
    private static $username = "mouad"; // Definimos el nombre de usuario para la base de datos
    private static $password = "MOUAD1234"; // Definimos la contraseña para la base de datos

    private static $options = array( // Definimos las opciones para la conexión PDO
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Modo de error: excepciones
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" // Comando inicial para establecer el juego de caracteres
    );

    private static $instance = null; // Declaramos una propiedad estática para la instancia única

    private $connection = null; // Declaramos una propiedad privada para almacenar la conexión
    private function __construct() { // Definimos el constructor de la clase
         $this->connection = $this->connect(); // Asignamos la conexión al inicializar la clase
    }

    public static function getInstance() { // Método para obtener la instancia única de la clase
        if (self::$instance == null) { // Si no existe una instancia, la creamos
            self::$instance = new DatabaseController(); // Creamos una nueva instancia de DatabaseController
        }
        return self::$instance; // Retornamos la instancia única
    }

    public function connect() { // Método para conectar a la base de datos
        try {
            $this->connection = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname, self::$username, self::$password, self::$options); // Creamos una nueva conexión PDO
            return $this->connection; // Retornamos la conexión
        } catch (PDOException $e) { // Capturamos cualquier excepción de PDO
            die("Error de conexión: " . $e->getMessage()); // Mostramos un mensaje de error y detenemos la ejecución
        }
    }

    // Obtiene la conexión actual.
    public function getConnection() { // Método para obtener la conexión actual
        return $this->connection; // Retorna la conexión a la base de datos.
    }

    public function getMatches($limit = 10) { // Método para obtener partidos con un límite
        $stmt = $this->conn->prepare("SELECT * FROM match_data ORDER BY id DESC LIMIT :limit"); // Preparamos la consulta SQL
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Asignamos el valor del límite
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retornamos los resultados como un array asociativo
    }

    function getLatestMatches() { // Método para obtener los últimos partidos
        global $conn; // Usamos la variable global $conn
        $stmt = $conn->prepare("SELECT league_name, team_home, team_away, score_home, score_away, match_time FROM match_data ORDER BY match_time DESC LIMIT 3"); // Preparamos la consulta SQL
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retornamos los resultados como un array asociativo
    }

    public function getNews($limit = 5) { // Método para obtener noticias con un límite
        $stmt = $this->conn->prepare("SELECT * FROM news_data ORDER BY id DESC LIMIT :limit"); // Preparamos la consulta SQL
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Asignamos el valor del límite
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retornamos los resultados como un array asociativo
    }

    function getLatestNews() { // Método para obtener las últimas noticias
        global $conn; // Usamos la variable global $conn
        $stmt = $conn->prepare("SELECT title, text FROM news_data ORDER BY title DESC LIMIT 3 "); // Preparamos la consulta SQL
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retornamos los resultados como un array asociativo
    }

    public function getTeams($limit = 10) { // Método para obtener equipos con un límite
        $stmt = $this->conn->prepare("SELECT * FROM team_info LIMIT :limit"); // Preparamos la consulta SQL
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Asignamos el valor del límite
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retornamos los resultados como un array asociativo
    }

    function getTeamLogo() { // Método para obtener los logos de los equipos
        global $conn; // Usamos la variable global $conn
        $stmt = $conn->prepare("SELECT team_name, league_name, logo_path FROM team_logos"); // Preparamos la consulta SQL
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retornamos los resultados como un array asociativo
    }
    
    function getLeagueLogo($league_name) { // Método para obtener el logo de una liga
        global $conn; // Usamos la variable global $conn
        $stmt = $conn->prepare("SELECT logo_path FROM league_logos WHERE league_name = :league_name"); // Preparamos la consulta SQL
        $stmt->bindParam(':league_name', $league_name, PDO::PARAM_STR); // Asignamos el valor del nombre de la liga
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retornamos el resultado como un array asociativo
    }

    // Función para obtener un artículo por ID
    function getArticleById($id) { // Método para obtener un artículo por su ID
        global $conn; // Usamos la variable global $conn
        $stmt = $conn->prepare("SELECT title, text, content, date FROM news_data WHERE id = $id"); // Preparamos la consulta SQL
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Asignamos el valor del ID
        $stmt->execute(); // Ejecutamos la consulta
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retornamos el resultado como un array asociativo
    }

    public function editTeam($teamId, $teamName) { // Método para editar un equipo
        $stmt = $this->conn->prepare("UPDATE team_info SET name = :name WHERE id = :id"); // Preparamos la consulta SQL
        $stmt->bindParam(':name', $teamName, PDO::PARAM_STR); // Asignamos el valor del nombre del equipo
        $stmt->bindParam(':id', $teamId, PDO::PARAM_INT); // Asignamos el valor del ID del equipo
        return $stmt->execute(); // Ejecutamos la consulta y retornamos el resultado
    }

    public function deleteTeam($teamId) { // Método para eliminar un equipo
        $stmt = $this->conn->prepare("DELETE FROM team_info WHERE id = :id"); // Preparamos la consulta SQL
        $stmt->bindParam(':id', $teamId, PDO::PARAM_INT); // Asignamos el valor del ID del equipo
        return $stmt->execute(); // Ejecutamos la consulta y retornamos el resultado
    }
}
?>
