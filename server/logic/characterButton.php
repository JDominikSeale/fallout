<?php
echo "<pre>";
print_r($_POST);

if(isset($_POST)){

    include_once("../db.php");

    $database = new DB;

    $keys = ["food", "water", "sleep"];

    foreach($keys as $key){
        if(key_exists($key, $_POST)){
            $k = $key;
        }
    }

    $cID = $_POST["characterID"];
    $database->updateStat($key, $cID);                  

}

exit();
header('Location: ../../static/pages/home.php');
