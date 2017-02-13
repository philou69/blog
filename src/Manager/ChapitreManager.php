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
        // Fonction pour ajouter un chapitre
        // On s'assure que le chapitre passer en paramètre est bien remplie
        if($chapitre->getTitle() == null || $chapitre->getChapitre() == null || $chapitre->getPublished_at() == null || $chapitre->isPublished() == null){
            return false;
        }
        $q = $this->db->prepare(
            'INSERT INTO Chapitre(title, chapitre, published_at,published) VALUES(:title, :chapitre, :published_at, :published)'
        );
        $q->bindValue(':title', $chapitre->getTitle(), \PDO::PARAM_STR);
        $q->bindValue(':chapitre', $chapitre->getChapitre(), \PDO::PARAM_STR);
        $q->bindValue(':published_at', date("Y-m-d H:i:m", strtotime($chapitre->getPublishedAt())), \PDO::PARAM_STR);
        $q->bindValue(':published', $chapitre->isPublished(), \PDO::PARAM_BOOL);
        $q->execute();

    }

    public function update(Chapitre $chapitre)
    {
        // Fonction pour mettre à jour un chapitre
        // On s'assure que le chapitre passer en paramètre est bien remplie
        if($chapitre->getTitle() == null || $chapitre->getChapitre() == null || $chapitre->getPublished_at() == null || $chapitre->isPublished() == null){
            return false;
        }
        $q = $this->db->prepare(
            "UPDATE Chapitre SET title = :title, chapitre = :chapitre, published_at = :published_at, published = :published WHERE id = :id"
        );
        $q->bindValue(':title', $chapitre->getTitle(), \PDO::PARAM_STR);
        $q->bindValue(':chapitre', $chapitre->getChapitre(), \PDO::PARAM_STR);
        $q->bindValue(':published_at', date("Y-m-d H:i:s", strtotime($chapitre->getPublishedAt())), \PDO::PARAM_STR);
        $q->bindValue(':published', $chapitre->isPublished(), \PDO::PARAM_BOOL);
        $q->execute();

    }

    public function findOneById($id)
    {
        // Fonction  cherchant un chapitre par son identifiant
        $q = $this->db->prepare("SELECT id, title, chapitre, published_at, published FROM Chapitre WHERE id = :id");
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // vérification du nombre d'entrée retourné
        if ($q->rowCount() != 1) {
            return false;
        }
        // On récupere le tous dans un tableau qu'on passe en paramètre d'une instance de chapitre
        $data = $q->fetch(\PDO::FETCH_ASSOC);

        // On revoie l'objet Chapitre
        return new Chapitre($data);
    }

    public function findAll()
    {
        // Fonction cherchant tous les chapitres
        // Tableau prévue pour contenir tous les chapitres
        $chapitres = [];
        $q = $this->db->query(
            'SELECT id, title, chapitre, published_at, published FROM Chapitre ORDER BY published_at DESC '
        );
        if ($q->rowCount() < 1) {
            return false;
        }
        // On boucle autant que possible pour ajouter les objets au tableau
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            $chapitres[] = new Chapitre($data);
        }

        // On retourne le tableau
        return $chapitres;
    }

    public function findPublished()
    {
        // Fonction cherchant  tous les chapitres marqué publié
        // Tableau prévue pour contenir les chapitres
        $chapitres = [];
        $q = $this->db->query(
            "SELECT id , title, chapitre, published_at FROM Chapitre WHERE published = 1 ORDER BY published_at DESC"
        );
        // On vérifie le nombre d'entrées retourné
        if ($q->rowCount() < 1) {
            return false;
        }
        // On boucle autant que possible pour ajoute les objets au tableau
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            $chapitres[] = new Chapitre($data);
        }

        // On retourne le tableau de chapitres
        return $chapitres;
    }

}