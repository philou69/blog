<?php


namespace App\Manager;


use App\Entity\PDO;
use App\Entity\Status;

class StatusManager
{
    protected $db;

    function __construct()
    {
        $this->db = PDO::get();
    }

    public function findOneById($id){
        $query = $this->db->prepare("SELECT id, status FROM Status WHERE id = :id");
        $query->bindValue(":id", $id, \PDO::PARAM_INT);
        $query->execute();
        if($query->rowCount() == 0){
            return false;
        }

        return $query->fetchObject(Status::class);
    }

}