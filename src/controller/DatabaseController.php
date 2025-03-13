<?php

namespace App\Controller;

use PDO;
use PDOException;

class DatabaseController {
    private $conn;
    private static $host = "localhost";
    private static $dbname = "Fastscore";
    private static $username = "mouad";
    private static $password = "MOUAD1234";

    private static $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );

    private static $instance = null;    
    private function __construct() {}

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DatabaseController();
        }
        return self::$instance;
    }

    public function connect() {
        try {
            $this->conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname, self::$username, self::$password, self::$options);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getMatches($limit = 10) {
        $stmt = $this->conn->prepare("SELECT * FROM match_data ORDER BY id DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function getLatestMatches() {
        global $conn;
        $stmt = $conn->prepare("SELECT league_name, team_home, team_away, score_home, score_away, match_time FROM match_data ORDER BY match_time DESC LIMIT 3");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNews($limit = 5) {
        $stmt = $this->conn->prepare("SELECT * FROM news_data ORDER BY id DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getLatestNews() {
        global $conn;
        $stmt = $conn->prepare("SELECT title, text FROM news_data ORDER BY title DESC LIMIT 3 "); // Cambiamos LIMIT 5 por LIMIT 3
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeams($limit = 10) {
        $stmt = $this->conn->prepare("SELECT * FROM team_info LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getTeamLogo() {
        global $conn;
        $stmt = $conn->prepare("SELECT team_name, league_name, logo_path FROM team_logos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function getLeagueLogo($league_name) {
        global $conn;
        $stmt = $conn->prepare("SELECT logo_path FROM league_logos WHERE league_name = :league_name");
        $stmt->bindParam(':league_name', $league_name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Función para obtener un artículo por ID
    function getArticleById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT title, text, content, date FROM news_data WHERE id = $id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>
