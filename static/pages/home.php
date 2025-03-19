<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include_once("../../server/logic/home.php");
    $home = new home();
    $_SESSION["player_list"] = [];
?>

<html>
    <head>

    </head>
    <body>
        <div class='group-info-container'>
            <div class='group-button-container'>
                <form method='post' action='../../server/logic/characterButton.php'>
                    <div class='1-hour-container'>
                        <input type="submit" value="1 Hour" name="time_skip"></input>
                    </div>
                    <div class='30-min-container'>
                        <input type="submit" value="30 Minute" name="time_skip"></input>
                    </div>
                    <div class='10-min-container'>
                        <input type="submit" value="10 Minute" name="time_skip"></input>
                    </div>
                    <div class='5-min-container'>
                        <input type="submit" value="5 Minute" name="time_skip"></input>
                    </div>
                    <div class='group-long-rest-container'>
                        <input type="submit" value="Long Rest" name="long_rest"></input>
                    </div>
                    <div class='group-short-rest-container'>
                        <input type="submit" value="Short Rest" name="short_rest"></input>
                    </div>
                    <div class='group-reset-contaier'>
                        <input type="submit" value="Group Reset" name="group_reset"></input>
                    </div>
                </form>
            </div>
            <div class="GM-info-container">
                <div class="game-time-logs-container">
                    <h1><?php echo $home->returnWorldTime()["game_time"]; ?></h1>
                </div>
                <div class="game-logs-container">
                    <?php echo $home->showLogs(); ?>
                </div>
            </div>
        </div>
        <div class="player-container">
            <?php echo $home->showPlayers();?>
        </div>
    </body>
</html>