<?php

include_once("game.php");


class home extends gameWorld{

    public function __construct(){
        parent::__construct();
    }

    public function showPlayers(){
        $d = $this->allPlayers(); // List of player characters
        $output = "";
        foreach ($d as $p){
            $output .= "
            <div class='player-card' value={$p->returnPlayerID()}>
                <div class='player-name'>
                    <h1>{$p->showPlayerName()}</h1>
                </div>
                <div class='player-character-options'>
                    <form method='post'>
                        <select name='Player_Characters' onchange='this.form.submit()'>
                            {$this->playerCharactersOptions($p->returnPlayerID())}
                        </select>
                    </form>
                </div>
                <div class='player-character-stats'>
                    <form method='post'>
                        <div class='food-container'>
                            <H2>Food: <form method='post'><input for='food' value={$p->returnCharacterFood()}></form></H2>
                        </div>
                        <div class='water-container'>
                            <H2>Water: <form method='post'><input for='water' value={$p->returnCharacterThirst()}></form></H2>
                        </div>
                        <div class='sleep-container'>
                            <H2>Sleep: <form method='post'><input for='sleep' value={$p->returnCharacterSleep()}></form></H2>
                        </div
                        <div class='fatigue-container'>
                            <H2>Fatigue: <form method='post'><input for='fatigue' value={$p->returnCharacterFatigue()}></form></H2>
                        </div
                    </form>
                </div>
            </div>
            ";
        }
        return $output;
    }

    public function playerCharactersOptions($pID){
        // characters.id
        // characters.name
        print($pID);
        $d = $this->playerCharacters($pID);
        print_r($d);
        exit();
        $totalCharacters = count($d);
        $output = "";
        for ($i = 0; $i <= $totalCharacters; $i++){
            $output .= "<option value='{$d[$i]["id"]}'>{$d[$i]["name"]}</option>";
        }
        return $output;
    }


}



?>