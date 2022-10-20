<?php
require_once "config.php";
function connection(){
    try{
      $c = new PDO("mysql:host=".Config::HOST.";dbname=".Config::DB, Config::USER, Config::PASS); 
      return $c;
    }catch(PDOException $e){
        exit($e->getMessage());
    }
}