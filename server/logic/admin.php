<?php

include_once('../db.php');
class admin extends DB{

    private $user;


    public function __construct($userID){
        parent::__construct();
        $this->user = $userID;
    }



    public function getUsersGames(){
        $output = "";
        $data = $this->playersGames($this->user);

        

        return $output;
    }

}

