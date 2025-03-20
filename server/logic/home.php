<?php

include_once("game.php");


class home extends gameWorld{

    public function __construct(){
        parent::__construct();
    }


    public function showPlayers(){
        $d = $this->allPlayers(); // List of player characters
        $output = "";
        $_SESSION["character_list"] = $d;
        $c = 0;
        foreach ($d as $p){
            $characterID = $p->returnCharacterID();
            $pList[] = $characterID;
            $output .= "
            <div class='player-card' value={$characterID}>
                <div class='player-name'>
                    <h1>{$p->showCharacterPlayerName()}</h1>
                </div>
                <div class='player-character-options'>
                    <form method='post'>
                        <select name='Player_Characters' onchange='this.form.submit()'>
                            {$this->playerCharactersOptions($p->showCharacterPlayerID())}
                        </select>
                    </form>
                </div>
                <div class='player-character-stats'>
                    <form method='post'>
                        <div class='food-container'>
                            <H2>Food: <form method='post'><input for='food' value={$p->returnCharacterFood()}></form></H2>
                            <H3>Time Elapsed: <form method='post'><input type='text' for='last_food_time' value={$p->foodElapsed()}></form></H3>
                        </div>
                        <div class='water-container'>
                            <H2>Water: <form method='post'><input for='water' value={$p->returnCharacterThirst()}></form></H2>
                            <H3>Time Elapsed: <form method='post'><input type='text' for='last_food_time' value={$p->thirstElapsed()}></form></H3>
                        </div>
                        <div class='sleep-container'>
                            <H2>Sleep: <form method='post'><input for='sleep' value={$p->returnCharacterSleep()}></form></H2>
                            <H3>Time Elapsed: <form method='post'><input type='text' for='last_food_time' value={$p->sleepElapsed()}></form></H3>
                            </div>
                            <div class='fatigue-container'>
                                <H2>Fatigue: <form method='post'><input type='text' for='fatigue' value={$p->returnCharacterFatigue()}></form></H2>
                            </div>
                        </div>
                    </form>
                    <form method='post' action='../../server/logic/characterButton.php'>
                    <input type='hidden' name='characterID' value={$characterID}></input>
                    <div class='button-container'>
                        <div class='food-container'>
                            <input type='submit' value='Food' name='player-character-button-value'></input>
                        </div>
                        <div class='water-container'>
                            <input type='submit' value='Water' name='player-character-button-value'></input>
                        </div>
                        <!--
                        <div class='sleep-container'>
                            <input type='submit' value='Sleep' name='player-character-button-value'></input>
                        </div>
                        -->
                    </div>
                </form>
                </div>
            ";
        }
        return $output;
    }

    public function showLogs(){
        $logData = $this->returnLogs();
        $output = "<table>";
        $output .= "<tr>";
        $output .= "<th>Time</th>";
        $output .= "<th>Character</th>";
        $output .= "<th>Action</th>";
        foreach($logData as $data){
            $output .= "<tr>";
            $output .= "<td>{$data["game_time"]}</td>";
            $output .= "<td>{$data["name"]}</td>";
            $output .= "<td>{$data["action"]}</td>";
            $output .= "</tr>";

        }

        $output .= "</table>";

        return $output;
    }


    public function playerCharactersOptions($pID){
        // characters.id
        // characters.name
        $d = $this->playerCharacters($pID);
        // print_r($d);
        $totalCharacters = count($d);
        $output = "";
        //exit();
        for ($i = 0; $i < $totalCharacters; $i++){
            $output .= "<option value='{$d[$i]["id"]}'>{$d[$i]["name"]}</option>";
        }
        return $output;
    }
}



?>