<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include_once("../../server/logic/home.php");
    $home = new home();
?>

<html>
    <head>

    </head>
    <body>
        <div class="player-container">
            <?php echo $home->showPlayers();?>
        </div>
    </body>
</html>