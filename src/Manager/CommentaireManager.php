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
        if ($commentaire->getCommentaireParent() != null) {
            $q = $this->db->prepare(
                "INSERT INTO Commentaire(commentaire, id_chapitre, id_user, id_parent, signaled, banished, created_at, place) VALUES (:commentaire, :id_chapitre, :id_user, :id_parent, :signaled, :banished, :created_at, :place)"
            );
            $q->bindValue(":id_parent", $commentaire->getCommentaireParent()->getId(), \PDO::PARAM_INT);
        } else {
            $q = $this->db->prepare(
                "INSERT INTO Commentaire(commentaire, id_chapitre, id_user, id_parent, signaled, banished, created_at, place) VALUES (:commentaire, :id_chapitre, :id_user, null, :signaled, :banished, :created_at, :place)"
            );
        }
        $q->bindValue(":commentaire", $commentaire->getCommentaire(), \PDO::PARAM_STR);
        $q->bindValue(":id_chapitre", $commentaire->getChapitre()->getId(), \PDO::PARAM_STR);
        $q->bindValue(":id_user", $commentaire->getUser()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":signaled", $commentaire->isSignaled(), \PDO::PARAM_BOOL);
        $q->bindValue(":banished", $commentaire->isBanished(), \PDO::PARAM_BOOL);
        $q->bindValue(":created_at", $commentaire->getCreatedAt(), \PDO::PARAM_STR);
        $q->bindValue(":place", $commentaire->getPlace(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function update(Commentaire $commentaire)
    {
        // Fonction pour mettre à jour un commentaire
        if ($commentaire->getCommentaireParent()->getId() != null) {
            $q = $this->db->prepare(
                "UPDATE Commentaire SET commentaire = :commentaire, id_chapitre = :id_chapitre, id_user = :id_user, id_parent = :id_parent, signaled = :signaled, banished = :banished, created_at = :created_at, place = :place WHERE id = :id"
            );
            $q->bindValue(":id_parent", $commentaire->getCommentaireParent()->getId(), \PDO::PARAM_INT);
        } else {
            $q = $this->db->prepare(
                "UPDATE Commentaire SET commentaire = :commentaire, id_chapitre = :id_chapitre, id_user = :id_user, signaled = :signaled, banished = :banished, created_at = :created_at, place = :place WHERE id = :id"
            );
        }
        $q->bindValue(":commentaire", $commentaire->getCommentaire(), \PDO::PARAM_STR);
        $q->bindValue(":id_chapitre", $commentaire->getChapitre()->getId(), \PDO::PARAM_STR);
        $q->bindValue(":id_user", $commentaire->getUser()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":signaled", $commentaire->isSignaled(), \PDO::PARAM_BOOL);
        $q->bindValue(":banished", $commentaire->isBanished(), \PDO::PARAM_BOOL);
        $q->bindValue(":created_at", $commentaire->getCreatedAt(), \PDO::PARAM_STR);
        $q->bindValue(":place", $commentaire->getPlace(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function bannish(Commentaire $commentaire)
    {
        // Fonction mettant à jour le status banished

        $q = $this->db->prepare("UPDATE Commentaire SET banished = :banished WHERE id = :id");
        $q->bindValue(":banished", $commentaire->isBanished(), \PDO::PARAM_BOOL);
        $q->bindValue(":id", $commentaire->getId(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function signaled(Commentaire $commentaire)
    {
        // Fonction mettant à jour le status de signaled
        $q = $this->db->prepare("UPDATE Commentaire SET signaled = :signaled WHERE id = :id");
        $q->bindValue(":signaled", $commentaire->isSignaled(), \PDO::PARAM_BOOL);
        $q->bindValue(":id", $commentaire->getId(), \PDO::PARAM_INT);
        $q->execute();
    }


    public function findOneById($id)
    {
        // Fonction cherchant un commentaire par son id
        $q = $this->db->prepare(
            "SELECT id, commentaire, id_chapitre, id_parent, id_user, signaled, banished, created_at, place, lastChild FROM Commentaire WHERE  id = :id"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie le nombre d'entrée renvoyé
        if ($q->rowCount() == 0) {
            return false;
        }
        $data = $q->fetch(\PDO::FETCH_ASSOC);
        // On va ajouté l'objet chapitre et user correspondant
        $data['chapitre'] = $this->addChapitre($data['id_chapitre']);
        $data['user'] = $this->addUser($data['id_user']);
        // un commentaire a un parent si sa place n'est pas 1
        if ($data['place'] != 1) {
            $commentaireParent = new Commentaire();
            $commentaireParent->setId($data['id_parent']);
            $data['commentaireParent'] = $commentaireParent;
        } elseif ($data['place'] != 3) {
            // Si la place n'est pas 3 le commetnaire peut avoir des enfants
            $commentairesEnfant = $this->findChildrenForOneCommentaire($data['id']);
            $data["commentaires"] = $commentairesEnfant;
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
            "SELECT id,commentaire, id_chapitre, id_parent, id_user, signaled, banished, created_at, place FROM Commentaire "
        );
        // On vérifie avoir au moins une entrée
        if ($q->rowCount() == 0) {
            return false;
        }
        // On boucle sur les entrées
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // On ajoute le user et le chapitre
            $data['chapitre'] = $this->addChapitre($data['id_chapitre']);
            $data['user'] = $this->addUser($data['id_user']);
            if ($data['place'] != 1) {
                // si la place est 1, le commentaire n'a pas de parent
                $commentaireParent = new Commentaire();
                $commentaireParent->setId($data['id_parent']);
                $data['commentaireParent'] = $commentaireParent;
            } elseif ($data['place'] != 3) {
                // Si la place est 3, on a pas de commentaire enfants
                $commentairesEnfants = $this->findChildrenForOneCommentaire($data['id']);
                $data['commentaires'] = $commentairesEnfants;
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
            "SELECT id, commentaire, id_user, signaled, banished, created_at, id_parent, place FROM Commentaire WHERE id_chapitre = :id AND place = 1"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie avoir au moins une entrée
        if ($q->rowCount() == 0) {
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // Création d'un objet user passer dans le tableau data  ainsi qu'un objet chapitre
            $data['user'] = $this->addUser($data['id_user']);
            $data['chapitre'] = $this->addChapitre($id);
            $commentairesEnfants = $this->findChildrenForOneCommentaire($data['id']);
            if($commentairesEnfants){
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
            "SELECT id, commentaire, id_user, banished, signaled, id_chapitre FROM Commentaire WHERE id_parent = :id AND place = 2"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie s'il y a des entrées
        if ($q->rowCount() < 1) {
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // On ajoute le chapitre et l'user
            $data['chapitre'] = $this->addChapitre($data['id_chapitre']);
            $data['user'] = $this->addUser($data['id_user']);
            $commentairesEnfants = [];
            $nq = $this->db->prepare(
                "SELECT id, commentaire, id_user, place, banished, signaled, id_chapitre FROM Commentaire WHERE id_parent = :id AND place = 3"
            );
            $nq->bindValue(":id", $data['id'], \PDO::PARAM_INT);
            $nq->execute();
            // S'il y a au minimum 1 entrée on ajoute le(s) commentaire(s)
            if ($nq->rowCount() > 0) {
                while ($dataEnfants = $nq->fetch(\PDO::FETCH_ASSOC)) {
                    // On ajoute le chapitre et le user
                    $dataEnfants['chapitre'] = $this->addChapitre($dataEnfants['id_chapitre']);
                    $dataEnfants['user'] = $this->addUser($dataEnfants['id_user']);
                    $commentairesEnfants[] = new Commentaire($dataEnfants);
                }
                $data['commentaires'] = $commentairesEnfants;
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