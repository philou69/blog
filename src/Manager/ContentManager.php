<?php


namespace App\Manager;


use App\Entity\Content;
use App\Entity\PDO;

class ContentManager
{
    private $bd;

    function __construct()
    {
        $this->bd = PDO::get();
    }

    public function findByTitle($title){
        $q = $this->bd->prepare("SELECT id, title, content FROM Content WHERE title = :title");
        $q->execute(array(":title" => $title));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);

        return new Content($donnees);
    }

    public function findAll(){
        $contents  = [];
        $q = $this->bd->query("SELECT id, title, content FROM Content");
        while($donnees = $q->fetch(\PDO::FETCH_ASSOC)){
            $contents[] = new Content($donnees);
        }
        return $contents;
    }


}