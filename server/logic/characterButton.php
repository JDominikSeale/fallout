<?php
if(isset($_POST)){

    include_once("game.php");

    $homeLogic = new gameWorld;

    $acceptedTags = ["food", "water", "sleep"];
    $characterList = $_SESSION["character_list"];

    if(isset($_POST["player-character-button-value"])){
        $cID = $_POST["characterID"];
        $tag = $_POST["player-character-button-value"];
        $tag = strtolower($tag);

        if(in_array($tag, $acceptedTags)){
            $homeLogic->singleCharacterConsume($tag, $cID);
        }
    }
    elseif(isset($_POST["time_skip"])){
        $skips = ["1 Hour", "30 Minute", "10 Minute", "5 Minute"];
        $skip = $_POST["time_skip"];
        $hour = 0;
        $min = 0;
        switch ($skip){
            case "1 Hour":
                $hour = 1;
                $skipID = 4;
                break;
            case "30 Minute":
                $min = 30;
                $skipID = 3;
                break;
            case "10 Minute":
                $min = 10;
                $skipID = 2;
                break;
            case "5 Minute":
                $min = 5;
                $skipID = 1;
                break;
        }
        
        if(in_array($skip, $skips)){
            $homeLogic->addGameTime($hour, $min, $skipID);
        }

    }elseif(isset($_POST["group_reset"])){
        $homeLogic->resetCurrentCharacters();
        
    }elseif(isset($_POST["long_rest"])){
        $restTime = 8;
        $rest = true;
        $restID = 6;
        $trackUpdate = false;
        foreach($characterList as $character){
            for($i = 0; $i<$restTime; $i++){
                $character->timeElapse(0, 30);
            }
            // Reset Sleep

            $character->setSleep(1);
            $character->setSleepElapsed(0.00);
            $character->setFatigue(0);

            //Update Characters
            $homeLogic->setCharacterList($characterList);
            $homeLogic->addGameTime($restTime, 0, $restID, $rest, $trackUpdate);
        }
        // Update Entire Game
        $homeLogic->setCharacterList($characterList);
        $homeLogic->addGameTime($restTime, 0, $restID, false,$trackUpdate);

    }elseif(isset($_POST["short_rest"])){
        $restTime = 6;
        $rest = true;
        $restID = 5;
        $trackUpdate = false;
        foreach($characterList as $character){
            for($i = 0; $i<$restTime; $i++){
                $character->timeElapse(0, 30);
            }
            // Reset Sleep

            $character->setSleep(1);
            $character->setSleepElapsed(0.00);
            $character->setFatigue(0);

            //Update Characters
            $homeLogic->setCharacterList($characterList);
            $homeLogic->addGameTime($restTime, 0, $restID, $rest, $trackUpdate);
        }
        // Update Entire Game
        $homeLogic->setCharacterList($characterList);
        $homeLogic->addGameTime($restTime, 0, $restID, false,$trackUpdate);
    }
}
header('Location: ../../static/pages/home.php');
