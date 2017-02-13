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

    public function findByTitle($title)
    {
        // Fonction cherchant un contenu par son titre
        $q = $this->bd->prepare("SELECT id, title, content, page FROM Content WHERE title = :title");
        $q->bindValue(":title", $title, \PDO::PARAM_STR);
        $q->execute();
        if ($q->rowCount() < 1) {
            return false;
        }
        $data = $q->fetch(\PDO::FETCH_ASSOC);

        return new Content($data);
    }

    public function findAll()
    {
        // Fonction cherchant tous les contenus
        $contents = [];
        $q = $this->bd->query("SELECT id, title, content, page FROM Content");
        if ($q->rowCount() < 1) {
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            $contents[] = new Content($data);
        }

        return $contents;
    }

    public function findAllPerPage($page)
    {
        // Fonction cherchant les contenus d'une page
        $contents = [];
        $q = $this->bd->prepare("SELECT id, title, content, page FROM Content WHERE page = :page");
        $q->bindValue(":page", $page, \PDO::PARAM_STR);
        $q->execute();

        if ($q->rowCount() < 1) {
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            $contents[] = new Content($data);
        }

        return $contents;

    }

}