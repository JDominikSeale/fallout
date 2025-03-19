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

    public function addLog($time, $pID, $action){
        print_r($time . "<br>". $pID . "<br>" . $action);
        $query = "INSERT INTO world_log(game_time, player_character, action) VALUES(?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sii", $time, $pID, $action);
        $stmt->execute();
        $stmt->close();
    }

    public function getLog($excWorld = false){
        $query = "SELECT
    *
FROM
    (
    SELECT
        world_log.id AS logging_id,
        game_time,
        characters.name,
        characters.id,
        world_log.player_character,
        actions.action
    FROM
        world_log
    JOIN actions ON world_log.action = actions.id
    JOIN characters ON world_log.player_character = characters.id
    ORDER BY
        world_log.id
    DESC
LIMIT 20
) AS last_20_records
ORDER BY
    logging_id ASC";
        if($excWorld){
            $query = "SELECT * FROM (SELECT world_log.id AS logging_id, game_time, characters.name, characters.id, world_log.player_character FROM world_log JOIN characters ON world_log.player_character = characters.id WHERE characters.id <> 3ORDER BY world_log.id DESC LIMIT 20) AS last_20_records ORDER BY logging_id ASC";
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
        $query = "SELECT game_time FROM world_log WHERE id=(SELECT max(id) FROM world_log)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_assoc();
        $stmt->close();
        return $result;
    }

    public function updatePlayers($clause, $value, $type, $id){
        $setClause = "$clause = ?";
        $query = "UPDATE characters SET $setClause WHERE ID = ?";
        print_r($query);
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param($type . "i", $value, $id);
        $stmt->execute();
        $stmt->close();
    }

    // I would not have made this func if I realised the player class didn't return properly
    // I would have used updateStat() instead
    public function updateCharacterStatAndTime($key, $idValue, $timeElapsed, $id, $fatigue){
        $q = "UPDATE characters SET {$key}='{$idValue}', {$key}_value={$timeElapsed}, fatigue={$fatigue} WHERE id = ?";
        $stmt = $this->connection->prepare($q);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    public function characterData($id){
        $query = 'SELECT characters.id, players.id as "playerID", players.name as "playerName", characters.name as "characterName", food, water, sleep, fatigue, food_value, water_value, sleep_value, fatigue_value FROM characters JOIN players ON players.id = player_id WHERE characters.id = ?';
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

    private function getSingleStat($stat, $id){
        $q = "SELECT {$stat} FROM characters WHERE id = ?";
        $stmt = $this->connection->prepare($q);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_assoc();
        $stmt->close();
        return $result;
    }

    public function updateStat($stat, $id){
        $statValue = $this->getSingleStat($stat, $id);
        if($statValue[$stat] > 1){
            $q = "UPDATE characters SET $stat=$stat-1, {$stat}_value='0.00' WHERE id = ?";
            $stmt = $this->connection->prepare($q);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function resetCharacter($id){
        $q = "UPDATE characters SET food=1, water=1, sleep=1, fatigue=0, food_value=0.00, water_value=0.00, sleep_value=0.00, fatigue_value=0.00 WHERE id = ?";
        $st = $this->connection->prepare($q);
        $st->bind_param("i", $id);
        $st->execute();
        $st->close();
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}