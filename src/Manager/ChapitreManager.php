<?php


namespace App\Manager;

use App\Entity\Chapitre;
use App\Entity\PDO;


class ChapitreManager
{
    private $db;

    function __construct()
    {
        $this->db = PDO::get();
    }

    public function add(Chapitre $chapitre)
    {
        $q = $this->db->prepare(
            'INSERT INTO Chapitre(title, chapitre, published_at,published) VALUES(:title, :chapitre, :published_at, :published)'
        );
        $q->execute(
            array(
                ':title' => $chapitre->getTitle(),
                ':chapitre' => $chapitre->getChapitre(),
                ':published_at' => $chapitre->getPublishedAt(),
                ':published' => $chapitre->isPublished(),
            )
        );

    }

    public function getOne($id)
    {
        $q = $this->db->prepare("SELECT id, title, chapitre, published_at, published FROM Chapitre WHERE id = :id");
        $q->execute(array(':id' => $id));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);

        return new Chapitre($donnees);
    }

    public function getAll()
    {
        $chapitres = [];
        $q = $this->db->query(
            'SELECT id, title, chapitre, published_at, published FROM Chapitre ORDER BY published_at DESC '
        );
        while ($donnees = $q->fetch(\PDO::FETCH_ASSOC)) {
            $chapitres[] = new Chapitre($donnees);
        }

        return $chapitres;
    }

    public function update(Chapitre $chapitre)
    {
        $q = $this->db->prepare(
            "UPDATE Chapitre SET title = :title, chapitre = :chapitre, published_at = :published_at, published = :published WHERE id = :id"
        );
        $q->execute(
            array(
                ':title' => $chapitre->getTitle(),
                ':chapitre' => $chapitre->getChapitre(),
                ':published_at' => $chapitre->getPublishedAt(),
                ':published' => $chapitre->isPublished(),
            )
        );

    }

    public function findPublished(){
        $chapitres = [];
        $q = $this->db->query("SELECT id , title, chapitre, published_at FROM Chapitre WHERE published = 1 ORDER BY published_at DESC");
        while ($donnees = $q->fetch(\PDO::FETCH_ASSOC)){
            $chapitres[] = new Chapitre($donnees);
        }
        return $chapitres;
    }

}