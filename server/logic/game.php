<?php


include_once __DIR__ . '/../db.php';


class gameWorld{

    private $hour;
    private $minute;
    private $playersList; // get the most recent player based on the log ignoring "world" and try and remove repetition, holds 4 players
    private $db;

    public function __construct(){
        $this->hour = null;
        $this->minute = null;
        $this->playersList = [];
        $this->db = new DB;
        $this->initFunctions();
    }

    private function initFunctions(){
        $this->worldTime();
        $this->recentCharacters();
    }

    public function allPlayers(){
        return $this->playersList;
    }

    private function recentCharacters(){
        echo "<pre>";
        
        $d = $this->db->getRecentPlayerCharacters();

        $totalPlayerCount = count($d);
        $startIndex = $totalPlayerCount - 4;

        $d = array_slice($d, $startIndex, $totalPlayerCount);

        $pcID = [];
        foreach($d as $uPC){
            $pcID[] = $uPC["player_character"];
        }

        $playersIDL = $pcID;

        $statusList = [];
        $statusList["food"] = $this->db->getStatuses("food");
        $statusList["water"] = $this->db->getStatuses("water");
        $statusList["sleep"] = $this->db->getStatuses("sleep");

    

        foreach($playersIDL as $pID){
            $d =  $this->db->characterData($pID);
            $this->playersList[] = new characters($d, $statusList);
        }

    }

    private function worldTime(){
        $timeText = $this->db->lastWorldTime();
        $timeLst = explode(":", $timeText["game_time"]);
        $this->hour = (int) $timeLst[0];
        $this->minute = (int) $timeLst[1];
    }

    public function addTime($h, $m, $timeAction){
        $this->minute += $m;
        $this->hour += $h;
        while($this->minute >= 60){
            $this->hour += 1;
            $this->minute -= 1;
        }

        if($this->hour > 24){
            $this->hour = 0;
        }

        $nTime = $this->hour . ":" . $this->minute;
        // new game time
        // WORLD player id
        // action to add time
        $this->db->addLog($nTime, 4, $timeAction);

    }

    public function playerCharacters($pID){
        return $this->db->getPlayersCharaters($pID);
    }

}


class characters{

    private $id;
    private $playerName;
    private $characterName;
    private $hunger;
    private $thirst;
    private $sleep;
    private $fatigue;
    private $hungerTimeElapsed;
    private $thirstTimeElapsed;
    private $sleepTimeElapsed;
    private $fatigueTimeElapsed;
    private $statusList;

    public function __construct($detail, $statusLst){
        $d = $detail[0];
        $this->id = $d["id"];
        $this->playerName = $d["playerName"];
        $this->characterName = $d["characterName"];
        $this->hunger = $d["food"];
        $this->thirst = $d["water"];
        $this->sleep = $d["sleep"];
        $this->fatigue = $d["fatigue"];
        $this->statusList = $statusLst;

        //Elapsed time using 60 min
        $this->hungerTimeElapsed = $d["food_value"];
        $this->thirstTimeElapsed = $d["water_value"];
        $this->sleepTimeElapsed = $d["sleep_value"];
        $this->fatigueTimeElapsed = $d["fatigue_value"];
    }

    public function showPlayerName(){
        return $this->playerName;
    }

    public function returnPlayerID(){
        return $this->id;
    }

    public function returnCharacterFood(){
        return $this->statusList["food"][$this->hunger]["status"];
    }

    public function returnCharacterThirst(){
        return $this->statusList["water"][$this->thirst]["status"];
    }

    public function returnCharacterSleep(){
        return $this->statusList["sleep"][$this->sleep]["status"];
    }

    public function returnCharacterFatigue(){
        return $this->fatigue;
    }

}

