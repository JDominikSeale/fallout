<?php
include_once "config.php";

class DB {
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    
    public function __construct() {
        $this->host = DB_HOST_NAME;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->database = DB_HOST_NAME_FALLOUT;
        $this->port = DB_PORT;
        $this->connection();
        session_start();
    }
    
    public function connection() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
        if ($this->connection->connect_error) {
            return False;
        }
        return $this->connection;
    }

    public function checkUserLogin($username, $password) {
        if (!$this->connection) {
            $this->connection();
        }
        if (!$this->connection) {
            return False;
        }
        $query = "SELECT id, password FROM users WHERE username = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashPassword);
        $stmt->fetch();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        if ($num_rows > 0) {
            if (password_verify($password, $hashPassword)) {
                return $id;
            } else {
                $this->connection->close();
                return False;
            }
        } else {
            $this->connection->close();
            return False;
        }
    }

    public function registerUser($username, $password) {
        if (!$this->connection) {
            $this->connection();
        }
        if (!$this->connection) {
            return False;
        }
        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->close();
        return $this->checkUserLogin($username, $password);
    }

    public function getStatuses($statusLink){
        $query = "SELECT * FROM {$statusLink}";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function addLog($t, $pID, $a){
        $query = "INSERT INTO world_log(game_time, player_character, action) VALUES(???)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sii", $t, $pID, $a);
        $stmt->execute();
        $stmt->close();
    }

    public function getLog($excWorld = false){
        $query = "SELECT game_time, characters.name, characters.id, world_log.player_character FROM world_log JOIN characters ON world_log.player_character = characters.id ORDER BY world_log.id LIMIT 20";
        if($excWorld){
            $query = "SELECT game_time, characters.name, characters.id, world_log.player_character FROM world_log JOIN characters ON world_log.player_character = characters.id WHERE NOT characters.id = 3 ORDER BY world_log.id LIMIT 20";
        }
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function getRecentPlayerCharacters(){
        $query = "SELECT characters.id as 'player_character', characters.name, players.name as 'playerName' FROM characters JOIN players ON players.id = characters.player_id WHERE players.id != 4 ORDER BY characters.id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function lastWorldTime(){
        $query = "SELECT game_time FROM world_log ORDER BY id LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_assoc();
        $stmt->close();
        return $result;
    }

    public function updatePlayers($clause, $value, $type, $id){
        $setClause = "$clause = ?";
        $query = "UPDATE players SET $setClause WHERE ID = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param($type . "i", $value, $id);
        $stmt->execute();
        $stmt->close();
    }

    public function characterData($id){
        $query = 'SELECT characters.id, players.name as "playerName", characters.name as "characterName", food, water, sleep, fatigue, food_value, water_value, sleep_value, fatigue_value FROM characters JOIN players ON players.id = player_id WHERE characters.id = ?';
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function getPlayersCharaters($id){
        $query = "SELECT characters.id, characters.name FROM characters JOIN players ON players.id = characters.player_id WHERE players.id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}