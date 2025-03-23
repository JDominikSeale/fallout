<?php

    session_start();
    $userID = $_SESSION['userID'] = 1;
    if(!($userID)){
        header("Location: ../../index.php");
        exit();
    }

    include_once("../../server/logic/admin.php");

    $admin = new admin($userID);

    $usersGames = $admin->getUsersGames();


?>

<html>
    <head>

    </head>
    <body>
        <div class="user-games-container">
            <?php echo $usersGames; ?>
        </div>
    </body>
</html>



