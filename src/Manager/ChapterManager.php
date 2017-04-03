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

    public function create(Chapter $chapter)
    {
        // Fonction pour ajouter un chapter

        $q = $this->db->prepare(
            'INSERT INTO Chapter(title, chapter, publishedAt,published) VALUES(:title, :chapter, :publishedAt, :published)'
        );
        $q->bindValue(':title', $chapter->getTitle(), \PDO::PARAM_STR);
        $q->bindValue(':chapter', $chapter->getChapter(), \PDO::PARAM_STR);
        $q->bindValue(':publishedAt', $chapter->getPublishedAt()->format("Y-m-d"), \PDO::PARAM_STR);
        $q->bindValue(':published', $chapter->isPublished());
        $q->execute();

    }

    public function update(Chapter $chapter)
    {
        // Fonction pour mettre à jour un chapter
        // On s'assure que le chapter passer en paramètre est bien remplie

        $q = $this->db->prepare(
            "UPDATE Chapter SET title = :title, chapter = :chapter, publishedAt = :publishedAt, published = :published WHERE id = :id"
        );
        $q->bindValue(':title', $chapter->getTitle(), \PDO::PARAM_STR);
        $q->bindValue(':chapter', $chapter->getChapter(), \PDO::PARAM_STR);
        $q->bindValue(':publishedAt', $chapter->getPublishedAt()->format("Y-m-d"), \PDO::PARAM_STR);
        $q->bindValue(':published', $chapter->isPublished());
        $q->bindValue(':id', $chapter->getId(), \PDO::PARAM_INT);
        $q->execute();

    }

    public function delete(Chapter $chapter)
    {
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

        // On retourne une instance de Chapter
        return $q->fetchObject(Chapter::class);
    }

    public function findAll()
    {
        // Fonction cherchant tous les chapters
        // Tableau prévue pour contenir tous les chapters
        $chapters = [];
        $q = $this->db->query(
            'SELECT id, title, chapter, publishedAt, published FROM Chapter ORDER BY publishedAt DESC'
        );
        if ($q->rowCount() < 1) {
            return false;
        }
        // On boucle autant que possible pour ajouter les objets au tableau
        while ($chapter = $q->fetchObject(Chapter::class)) {
//            var_dump($chapter);
//            exit;
            $chapters[] = $chapter;
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
            "SELECT id , title, chapter, publishedAt, published FROM Chapter WHERE published = true and publishedAt < NOW() ORDER BY publishedAt DESC"
        );

        // On vérifie le nombre d'entrées retourné
        if ($q->rowCount() < 1) {
            return false;
        }
        // On boucle autant que possible pour ajoute les objets au tableau
        while ($chapter = $q->fetchObject(Chapter::class)) {
            $chapters[] = $chapter;
        }

        // On retourne le tableau de chapters
        return $chapters;
    }

    public function findLastPublished()
    {
        $q = $this->db->query(
            "SELECT id,title, chapter, published, publishedAt FROM Chapter WHERE published = true and publishedAt < NOW() ORDER BY publishedAt DESC  LIMIT 0, 1"
        );
        if ($q->rowCount() != 1) {
            return false;
        }

        return $q->fetchObject(Chapter::class);
    }

    public function findAllDraft()
    {
        $q = $this->db->query('SELECT id, title, chapter, published, publishedAt FROM Chapter WHERE published = false');
        if ($q->rowCount() == 0) {
            return false;
        }
        $chapters = [];
        while ($chapter = $q->fetchObject(Chapter::class)) {
            $chapters[] = $chapter;
        }

        return $chapters;
    }

    public function findAllPublished()
    {
        $q = $this->db->query('SELECT id, title, chapter, published, publishedAt FROM Chapter WHERE published = true');
        if ($q->rowCount() == 0) {
            return false;
        }
        $chapters = [];
        while ($chapter = $q->fetchObject(Chapter::class)) {
            $chapters[] = $chapter;
        }

        return $chapters;
    }

    public function istOtherTitleChapter($title)
    {
        $q = $this->db->prepare("SELECT id FROM Chapter WHERE title = :title");
        $q->bindValue(':title', $title, \PDO::PARAM_STR);
        $q->execute();
        if (is_bool($q->fetch())) {
            return true;
        }

        return false;
    }

    public function findByPage($limit, $offset)
    {
        $chapters = [];
        $query = $this->db->prepare(
            'SELECT id, title, chapter, published, publishedAt FROM Chapter WHERE published = true and publishedAt < NOW() ORDER BY publishedAt LIMIT :limit, :offset'
        );
        $query->bindValue('limit', $limit, \PDO::PARAM_INT);
        $query->bindValue('offset', $offset, \PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() == 0) {
            return false;
        }
        while ($chapter = $query->fetchObject(Chapter::class)) {
            $chapters[] = $chapter;
        }

        return $chapters;
    }

    public function findPageNumber()
    {
        $query = $this->db->query(
            'SELECT id, title, chapter, published, publishedAt FROM Chapter WHERE published = true and publishedAt < NOW() ORDER BY publishedAt '
        );

        return $query->rowCount();
    }
}