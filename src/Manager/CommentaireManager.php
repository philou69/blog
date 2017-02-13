<?php


namespace App\Manager;


use App\Entity\Chapitre;
use App\Entity\Commentaire;
use App\Entity\PDO;
use App\Entity\User;

class CommentaireManager
{
    private $db;

    function __construct()
    {
        $this->db = PDO::get();
    }

    public function add(Commentaire $commentaire)
    {
        // Fonction pour ajouter un commentaire
        if($commentaire->getCommentaireParent() != null){
        $q = $this->db->prepare(
            "INSERT INTO Commentaire(commentaire, id_chapitre, id_user, id_parent, signaled, banished, created_at, lastChild) VALUES (:commentaire, :id_chapitre, :id_user, :id_parent, :signaled, :banished, :created_at, :lastChild)"
        );
        $q->bindValue(":id_parent", $commentaire->getCommentaireParent()->getId(), \PDO::PARAM_INT);
        }else {
            $q = $this->db->prepare(
                "INSERT INTO Commentaire(commentaire, id_chapitre, id_user, id_parent, signaled, banished, created_at, lastChild) VALUES (:commentaire, :id_chapitre, :id_user, null, :signaled, :banished, :created_at, :lastChild)"
            );
        }
        $q->bindValue(":commentaire", $commentaire->getCommentaire(), \PDO::PARAM_STR);
        $q->bindValue(":id_chapitre", $commentaire->getChapitre()->getId(), \PDO::PARAM_STR);
        $q->bindValue(":id_user", $commentaire->getUser()->getId(), \PDO::PARAM_INT);


        $q->bindValue(":signaled", $commentaire->isSignaled(), \PDO::PARAM_BOOL);
        $q->bindValue(":banished", $commentaire->isBanished(), \PDO::PARAM_BOOL);
        $q->bindValue(":created_at",$commentaire->getCreatedAt(), \PDO::PARAM_STR);
        $q->bindValue(":lastChild", $commentaire->isLastChild(), \PDO::PARAM_BOOL);
        $q->execute();
    }

    public function update(Commentaire $commentaire)
    {
        // Fonction pour mettre à jour un commentaire
        if($commentaire->getCommentaireParent()->getId() != null){
            $q = $this->db->prepare(
                "UPDATE Commentaire SET commentaire = :commentaire, id_chapitre = :id_chapitre, id_user = :id_user, id_parent = :id_parent, signaled = :signaled, banished = :banished, created_at = :created_at WHERE id = :id"
            );
            $q->bindValue(":id_parent", $commentaire->getCommentaireParent()->getId(), \PDO::PARAM_INT);
        } else {
            $q = $this->db->prepare(
                "UPDATE Commentaire SET commentaire = :commentaire, id_chapitre = :id_chapitre, id_user = :id_user, signaled = :signaled, banished = :banished, created_at = :created_at WHERE id = :id"
            );
        }
        $q->bindValue(":commentaire", $commentaire->getCommentaire(), \PDO::PARAM_STR);
        $q->bindValue(":id_chapitre", $commentaire->getChapitre()->getId(), \PDO::PARAM_STR);
        $q->bindValue(":id_user", $commentaire->getUser()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":signaled", $commentaire->isSignaled(), \PDO::PARAM_BOOL);
        $q->bindValue(":banished", $commentaire->isBanished(), \PDO::PARAM_BOOL);
        $q->bindValue(":created_at", $commentaire->getCreatedAt(), \PDO::PARAM_STR);
        $q->bindValue(":lastChild", $commentaire->isLastChild(), \PDO::PARAM_BOOL);
        $q->execute();
    }

    public function bannish(Commentaire $commentaire)
    {
        // Fonction mettant à jour le status banished
        if($commentaire->getId() == null || $commentaire->isBanished() == false){
            return false;
        }
        $q = $this->db->prepare("UPDATE Commentaire SET banished = :banished WHERE id = :id");
        $q->bindValue(":banished", $commentaire->isBanished(), \PDO::PARAM_BOOL);
        $q->bindValue(":id", $commentaire->getId(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function signaled(Commentaire $commentaire)
    {
        // Fonction mettant à jour le status de signaled
        if($commentaire->isSignaled() == null || $commentaire->getId() == null){
            return false;
        }
        $q = $this->db->prepare("UPDATE Commentaire SET signaled = :signaled WHERE id = :id");
        $q->bindValue(":signaled", $commentaire->isSignaled(), \PDO::PARAM_BOOL);
        $q->bindValue(":id", $commentaire->getId(), \PDO::PARAM_INT);
        $q->execute();
    }


    public function updateParent(Commentaire $commentaire)
    {
        $q = $this->db->prepare("UPDATE Commentaire SET parent = :parent WHERE id = :id");
        $q->execute(
            array(
                ":parent",
                $commentaire->isParent(),
                ":id",
                $commentaire->getId(),
            )
        );
    }

    public function findOneById($id)
    {
        // Fonction cherchant un commentaire par son id
        $q = $this->db->prepare(
            "SELECT id, commentaire, id_chapitre, id_parent, id_user, signaled, banished, created_at, parent, lastChild FROM Commentaire WHERE  id = :id"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie le nombre d'entrée renvoyé
        if ($q->rowCount() != 1) {
            return false;
        }
        $data = $q->fetch(\PDO::FETCH_ASSOC);
        // On va ajouté l'objet chapitre et user correspondant
        $data['chapitre'] = $this->addChapitre($data['id_chapitre']);
        $data['user'] = $this->addUser($data['id_user']);
        // S'il possède un id_parent on ajoute l'objet CommentaireParent
        if ($data['id_parent'] != null) {
            $nq = $this->db->prepare(
                "SELECT id, commentaire, id_chapitre, id_parent, id_user, signaled, banished, created_at, parent FROM Commentaire WHERE  id = :id"
            );
            $nq->bindValue(":id", $data["id_parent"], \PDO::PARAM_INT);
            $nq->execute();
            // On vérifie qu'il y a bien qu'une entrée
            if($nq->rowCount() == 1){
                $dataParent = $nq->fetch(\PDO::FETCH_ASSOC);
                $data['commentaireParent'] = new Commentaire($dataParent);
            }
        }
        // On retourne le commentaire
        return new Commentaire($data);
    }


    public function findAll()
    {
        // Fonction retournant tous les commentaires
        // Tableau contenant les commentaires
        $commentaires = [];
        $q = $this->db->query(
            "SELECT id,commentaire, id_chapitre, id_parent, id_user, signaled, banished, created_at FROM Commentaire "
        );
        // On vérifie avoir au moins une entrée
        if($q->rowCount() < 1){
            return false;
        }
        // On boucle sur les entrées
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // On ajoute le user et le chapitre
            $data['chapitre'] = $this->addChapitre($data['id_chapitre']);
            $data['user'] = $this->addUser($data['id_user']);
            if($data['id_parent'] != null){
                $data['commetnaireParent'] = new Commentaire(array('id' => $data['id_parent']));
            }
            $commentaires[] = new Commentaire($data);
        }
        // On renvoie le tableau de commentaires
        return $commentaires;
    }
    public function findAllForAChapitre($id)
    {
        // Fonction cherchant les commentaires d'un chapitre
        // On selectionne uniquement les commentaires premiers
        // Tableau contenant les commentaire
        $commentaires = [];

        $q = $this->db->prepare(
            "SELECT id, commentaire, id_user, signaled, banished, created_at, parent, id_parent FROM Commentaire WHERE id_chapitre = :id AND id_parent IS NULL"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie avoir au moins une entrée
        if($q->rowCount() < 1){
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // Création d'un objet user passer dans le tableau data  ainsi qu'un objet chapitre
            $data['user'] = $this->addUser($data['id_user']);
            $data['chapitre'] = $this->addChapitre($id);
            // On vérifie s'il le commentaire à des reponses
            if ($data['parent'] == 1) {
                $commentairesEnfants = $this->findChildrenForOneCommentaire($data['id']);
                $data['commentaires'] = $commentairesEnfants;
            }
            $commentaires[] = new Commentaire($data);
        }
        // On retourne le tableau de commentaires
        return $commentaires;
    }

    public function findChildrenForOneCommentaire($id)
    {
        // Fonction cherchant les enfants d'un commentaire
        // Tableau contenant les commentaires
        $commentaires = [];
        $q = $this->db->prepare(
            "SELECT id, commentaire, id_user, parent, banished, signaled, id_chapitre FROM Commentaire WHERE id_parent = :id"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie s'il y a des entrées
        if($q->rowCount() < 1){
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // On ajoute le chapitre et l'user
            $data['chapitre'] = $this->addChapitre($data['id_chapitre']);
            $data['user'] = $this->addUser($data['id_user']);
            // On vérifie si le commentaire est parent
            if ($data['parent'] == 1) {
                $commentairesEnfants = [];
                $nq = $this->db->prepare(
                    "SELECT id, commentaire, id_user, id_chapitre FROM Commentaire WHERE id_parent = :id"
                );
                $nq->bindValue(":id", $id, \PDO::PARAM_INT);
                $nq->execute();
                if($nq->rowCount() >= 1){
                    while ($dataEnfants = $nq->fetch(\PDO::FETCH_ASSOC)) {
                        // On ajoute le chapitre et le user
                        $dataEnfants['chapitre'] = $this->addChapitre($dataEnfants['id_chapitre']);
                        $dataEnfants['user'] = $this->addUser($dataEnfants['id_user']);
                        $commentairesEnfants[] = new Commentaire($dataEnfants);
                    }
                    $data['commentaires'] = $commentairesEnfants;
                }
            }
            $commentaires[] = new Commentaire($data);
        }
        // On retourne le tableau de commentaire.
        return $commentaires;
    }

    public function addUser($id)
    {
        $userManager = new UserManager();
        $user = $userManager->findOneById($id);

        return $user;
    }
    public function addChapitre($id)
    {
        $chapitreManager = new ChapitreManager();
        $chapitre = $chapitreManager->findOneById($id);

        return $chapitre;
    }
}