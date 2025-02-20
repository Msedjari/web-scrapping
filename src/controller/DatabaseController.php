<?php

namespace Controller;

use PDO;
use PDOException;

class DatabaseController {
    private $conn;

    public function __construct() {
        $host = "localhost";
        $dbname = "Fastscore";
        $username = "mouad";
        $password = "MOUAD1234";

        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

    public function getNews($limit = 5) {
        $stmt = $this->conn->prepare("SELECT * FROM news_data ORDER BY id DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeams($limit = 10) {
        $stmt = $this->conn->prepare("SELECT * FROM team_info LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
