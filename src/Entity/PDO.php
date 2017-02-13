<?php


namespace App\Entity;

require_once 'app/parameters.php';
class PDO
{
    private static $instance = null;

    public static function get(){
        try{
            $instance = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_BASE.";charset=utf8", DB_USER, DB_PASSWORD, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
        }catch (\Exception $e){
            die('Erreur : '.$e->getMessage());
        }
        return $instance;

    }

    private function __destruct()
    {
        if($this->instance != null){
            $instance = null;
        }
    }
}