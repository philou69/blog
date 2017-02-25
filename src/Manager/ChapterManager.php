<?php


namespace App\Manager;

use App\Entity\Chapter;
use App\Entity\PDO;


class ChapterManager
{
    private $db;

    function __construct()
    {
        $this->db = PDO::get();
    }

    public function add(Chapter $chapter)
    {
        // Fonction pour ajouter un chapter
        // On s'assure que le chapter passer en paramètre est bien remplie
        if($chapter->getTitle() == null || $chapter->getChapter() == null || $chapter->getPublishedAt() == null || $chapter->isPublished() == null){
            return false;
        }
        $q = $this->db->prepare(
            'INSERT INTO Chapter(title, chapter, publishedAt,published) VALUES(:title, :chapter, :publishedAt, :published)'
        );
        $q->bindValue(':title', $chapter->getTitle(), \PDO::PARAM_STR);
        $q->bindValue(':chapter', $chapter->getChapter(), \PDO::PARAM_STR);
        $q->bindValue(':publishedAt', $chapter->getPublishedAt()->format("Y-m-d"), \PDO::PARAM_STR);
        $q->bindValue(':published', $chapter->isPublished(), \PDO::PARAM_BOOL);
        $q->execute();

    }

    public function update(Chapter $chapter)
    {
        // Fonction pour mettre à jour un chapter
        // On s'assure que le chapter passer en paramètre est bien remplie
        if($chapter->getTitle() == null || $chapter->getChapter() == null || $chapter->getPublishedAt() == null || $chapter->isPublished() == null){
            return false;
        }
        $q = $this->db->prepare(
            "UPDATE Chapter SET title = :title, chapter = :chapter, publishedAt = :publishedAt, published = :published WHERE id = :id"
        );
        $q->bindValue(':title', $chapter->getTitle(), \PDO::PARAM_STR);
        $q->bindValue(':chapter', $chapter->getChapter(), \PDO::PARAM_STR);
        $q->bindValue(':publishedAt', $chapter->getPublishedAt()->format("Y-m-d"), \PDO::PARAM_STR);
        $q->bindValue(':published', $chapter->isPublished(), \PDO::PARAM_BOOL);
        $q->bindValue(':id', $chapter->getId(), \PDO::PARAM_INT);
        $q->execute();

    }
    public function delete(Chapter $chapter){
        $q = $this->db->prepare("DELETE FROM Chapter WHERE id = :id");
        $q->bindValue(":id", $chapter->getId(), \PDO::PARAM_INT);
        $q->execute();
    }
    public function findOneById($id)
    {
        // Fonction  cherchant un chapter par son identifiant
        $q = $this->db->prepare("SELECT id, title, chapter, publishedAt, published FROM Chapter WHERE id = :id");
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // vérification du nombre d'entrée retourné
        if ($q->rowCount() != 1) {
            return false;
        }
        // On récupere le tous dans un tableau qu'on passe en paramètre d'une instance de chapter
        $data = $q->fetch(\PDO::FETCH_ASSOC);

        // On revoie l'objet Chapter
        return new Chapter($data);
    }

    public function findAll()
    {
        // Fonction cherchant tous les chapters
        // Tableau prévue pour contenir tous les chapters
        $chapters = [];
        $q = $this->db->query(
            'SELECT id, title, chapter, publishedAt, published FROM Chapter ORDER BY publishedAt DESC '
        );
        if ($q->rowCount() < 1) {
            return false;
        }
        // On boucle autant que possible pour ajouter les objets au tableau
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            $chapters[] = new Chapter($data);
        }

        // On retourne le tableau
        return $chapters;
    }

    public function findPublished()
    {
        // Fonction cherchant  tous les chapters marqué publié
        // Tableau prévue pour contenir les chapters
        $chapters = [];
        $q = $this->db->query(
            "SELECT id , title, chapter, publishedAt FROM Chapter WHERE published = 1 ORDER BY publishedAt DESC"
        );

        // On vérifie le nombre d'entrées retourné
        if ($q->rowCount() < 1) {
            return false;
        }
        // On boucle autant que possible pour ajoute les objets au tableau
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            $chapters[] = new Chapter($data);
        }

        // On retourne le tableau de chapters
        return $chapters;
    }

    public function findLastPublished(){
        $q = $this->db->query("SELECT id,title, chapter, publishedAt FROM Chapter WHERE published = true ORDER BY publishedAt DESC  LIMIT 0, 1");
        if($q->rowCount() != 1){
            return false;
        }
        $data = $q->fetch(\PDO::FETCH_ASSOC);

        return new Chapter($data);
    }

}