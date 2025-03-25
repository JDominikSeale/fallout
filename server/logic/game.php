<?php

use function PHPSTORM_META\type;

include_once __DIR__ . '/../db.php';


class gameWorld{

    private $hour;
    private $minute;
    private $playersList; // get the most recent player based on the log ignoring "world" and try and remove repetition, holds 4 players
    private $db;
    private $timeFormatted;
    private $world;

    public function __construct(){
        $this->hour = null;
        $this->minute = null;
        $this->playersList = [];
        $this->db = new DB;
        $this->timeFormatted;
        $this->world = 3;
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
            // print_r($d);
            // print_r($statusList);
            // exit();
            $this->playersList[] = new characters($d, $statusList);
        }

    }

    public function returnWorldTime(){
        return $this->timeFormatted;
    }

    private function worldTime(){
        $timeText = $this->db->lastWorldTime();
        $timeLst = explode(":", $timeText["game_time"]);
        $this->hour = (int) $timeLst[0];
        $this->minute = (int) $timeLst[1];
        $this->timeFormatted = $timeText; 
    }

    private function addTime($h, $m, $timeAction){
        $this->minute += $m;
        $this->hour += $h;
        while($this->minute >= 60){
            $this->hour += 1;
            $this->minute -= 60;
        }

        if($this->hour >= 24){
            $this->hour -= 24;
        }

        $nMin = $this->minute;
        if($this->minute < 10){
            $nMin = "0" . $this->minute;
        }
        $nTime = $this->hour . ":" . $nMin;
        // new game time
        // WORLD player id
        // action to add time
        $this->timeFormatted = $nTime;
        $this->db->addLog($nTime, $this->world, $timeAction);

    }

    public function returnLogs(){
        return $this->db->getLog();
    }

    public function playerCharacters($pID){
        return $this->db->getPlayersCharaters($pID);
    }

    public function setCharacterList($listCharacters){
        $this->playersList = $listCharacters;
    }

    public function addGameTime($h, $m, $skipID, $groupRest=false, $trackUpdate=true){
        foreach($this->playersList as $character){
            if($trackUpdate){
                $character->timeElapse($h, $m);
            }
            // Food
            $this->db->updateCharacterStatAndTime("food", $character->returnCharacterFoodID(), $character->foodElapsed(), $character->returnCharacterID(), $character->returnCharacterFatigue());
            // Water
            $this->db->updateCharacterStatAndTime("water", $character->returnCharacterWaterID(), $character->thirstElapsed(), $character->returnCharacterID(), $character->returnCharacterFatigue());
            // Sleep
            $this->db->updateCharacterStatAndTime("sleep", $character->returnCharacterSleepID(), $character->sleepElapsed(), $character->returnCharacterID(), $character->returnCharacterFatigue());

        }
        if($groupRest == false){
            $this->addTime($h, $m, $skipID);
        }
    }

    public function resetCurrentCharacters(){
        foreach($this->playersList as $character){
            $this->db->resetCharacter($character->returnCharacterID());
        }
        $this->db->addLog($this->timeFormatted["game_time"], $this->world, 9);
    }

    public function singleCharacterConsume($tag, $characterID){
        $this->db->updateStat($tag, $characterID);
    }

}


class characters{

    private $id;
    private $playerID;
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
        $this->playerID = $d["playerID"];
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

    public function shortRest(){
        if($this->sleep > 2){
            $this->sleep -= 1;
        }
    }

    public function setSleep($v){
        $this->sleep = $v;
    }

    public function setSleepElapsed($v){
        $this->sleepTimeElapsed = $v;
    }

    public function setFatigue($v){
        $this->fatigue = $v;
    }

    public function sleepElapsed(){
        return $this->sleepTimeElapsed;
    }
    public function foodElapsed(){
        return $this->hungerTimeElapsed;
    }

    public function thirstElapsed(){
        return $this->thirstTimeElapsed;
    }

    public function showCharacterPlayerID(){
        return $this->playerID;
    }

    public function showCharacterPlayerName(){
        return $this->playerName;
    }

    public function returnCharacterID(){
        return $this->id;
    }

    public function returnCharacterFoodID(){
        return $this->hunger;
    }

    public function returnCharacterWaterID(){
        return $this->thirst;
    }

    public function returnCharacterSleepID(){
        return $this->sleep;
    }

    public function returnCharacterFood(){
        foreach($this->statusList["food"] as $stat){
            if($stat['id'] == $this->hunger){
                return $stat['status'];
            }
        }
    }

    public function returnCharacterThirst(){
        foreach($this->statusList["water"] as $stat){
            if($stat['id'] == $this->thirst){
                return $stat['status'];
            }
        }
    }

    public function returnCharacterSleep(){
        foreach($this->statusList["sleep"] as $stat){
            if($stat['id'] == $this->sleep){
                return $stat['status'];
            }
        }
    }

    public function returnCharacterFatigue(){
        return $this->fatigue;
    }

    public function timeElapse($hour, $min){
        $timeToAdd = $this->hourCalc($hour, $min);
        $this->sleepTimeElapseFunc($timeToAdd);
        $this->foodTimeElapsedFun($timeToAdd);
        $this->thirstTimeElapsedFunc($timeToAdd);
    }

    private function hourCalc($hour, $min){
        while($min >= 60){
            $min -= 60;
            $hour += 1;
        }
        $stMin = (string) $min;
        if(strlen($stMin) < 2){
            $min = "0" . $stMin;
        }
        $output = (double) "$hour.$min";
        return $output;
    }

    private function floatHourCalc($time){
        $time = (string) number_format($time, 2);
        $sepTime = explode(".", $time);
        $hour = $sepTime[0];
        $min = $sepTime[1];
        if(strlen($min) == 1){
            $min .= '0';
        }
        return $this->hourCalc($hour, $min);
    }

    private function thirstTimeElapsedFunc($timeToAdd){
        $this->thirstTimeElapsed += $timeToAdd;
        $this->thirstTimeElapsed = $this->floatHourCalc($this->thirstTimeElapsed);
        echo "<br>";
        print_r($this->thirstTimeElapsed);
        if($this->thirst == 1){
            //Quenched -> Hydrated
            if($this->thirstTimeElapsed >= 1.00){
                $this->thirstTimeElapsed -= 1.00;
                $this->thirst = 2;
            }
        }elseif($this->thirst == 2){
            //Hydrated -> Thirsty
            if($this->thirstTimeElapsed >= 2.00){
                $this->thirstTimeElapsed -= 2.00;
                $this->thirst = 3;
            }
        }elseif($this->thirst == 3){
            //Thirsty -> Dehydrated
            if($this->thirstTimeElapsed >= 4.00){
                $this->thirstTimeElapsed -= 4.00;
                $this->thirst = 4;
            }
        }elseif($this->thirst == 4){
            //Dehydrated
            if($this->thirstTimeElapsed >= 8.00){
                $this->thirstTimeElapsed -= 8.00;
                $this->fatigue += 1;
            }
        }

        $this->thirstTimeElapsed = $this->floatHourCalc($this->thirstTimeElapsed);
    }

    private function foodTimeElapsedFun($timeToAdd){
        $this->hungerTimeElapsed += $timeToAdd;
        $this->hungerTimeElapsed = $this->floatHourCalc($this->hungerTimeElapsed);
        if($this->hunger == 1){
            //Full -> Sated
            if($this->hungerTimeElapsed >= 1.00){
                $this->hungerTimeElapsed -= 1.00;
                $this->hunger = 2;
            }
        }elseif($this->hunger == 2){
            //Sated -> Peckish
            if($this->hungerTimeElapsed >= 4.00){
                $this->hungerTimeElapsed-= 4.00;
                $this->hunger = 3;
            }
        }elseif($this->hunger == 3){
            //Peckish -> Hungry
            if($this->hungerTimeElapsed >= 8.00){
                $this->hungerTimeElapsed -= 8.00;
                $this->hunger = 4;
            }
        }elseif($this->hunger == 4){
            //Hungry -> Starving
            if($this->hungerTimeElapsed >= 16.00){
                $this->hungerTimeElapsed -= 16.00;
                $this->fatigue += 1;
                $this->hunger = 5;
            }
            // Starving
        }elseif($this->hunger == 5){
            if($this->hungerTimeElapsed >= 24.00){
                $this->hungerTimeElapsed -= 24.00;
                $this->fatigue += 1;
            }
        }
        $this->hungerTimeElapsed = $this->floatHourCalc($this->hungerTimeElapsed);
    }

    private function sleepTimeElapseFunc($timeToAdd){
        $this->sleepTimeElapsed += $timeToAdd;
        $this->sleepTimeElapsed = $this->floatHourCalc($this->sleepTimeElapsed);
        if($this->sleep == 1){
            if($this->sleepTimeElapsed >= 8.00){
                $this->sleepTimeElapsed -= 8.00;
                $this->sleep = 2;
                $this->fatigue += 1;
            }
        }elseif($this->sleep == 2){
            if($this->sleepTimeElapsed >= 8.00){
                $this->sleepTimeElapsed -= 8.00;
                $this->sleep = 3;
                $this->fatigue += 1;
            }
        }elseif($this->sleep == 3){
            if($this->sleepTimeElapsed >= 8.00){
                $this->sleepTimeElapsed -= 8.00;
                $this->sleep = 4;
                $this->fatigue += 1;
            }
        }elseif($this->sleep == 4){
            if($this->sleepTimeElapsed >= 4.00){
                $this->sleepTimeElapsed -= 4.00;
                $this->fatigue += 1;
            }
        }
        $this->sleepTimeElapsed = $this->floatHourCalc($this->sleepTimeElapsed);
    }

}

