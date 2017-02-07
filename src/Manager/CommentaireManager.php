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
        $q = $this->db->prepare(
            "INSERT INTO Commentaire(commentaire, id_chapitre, id_user, id_parent, signaled, banished, created_at) VALUES (:commentaire, :id_chapitre, :id_user, :id_parent, :signaled, :banished, :created_at)"
        );
        $q->execute(
            array(
                ":commentaire" => $commentaire->getText(),
                ":id_chapitre" => $commentaire->getChapitre()->getId(),
                ":id_user" => $commentaire->getUser()->getId(),
                ":id_parent" => $commentaire->getCommentaireParent()->getId(),
                ":signaled" => $commentaire->isSignaled(),
                ":banished" => $commentaire->isBanished(),
                ":created_at" => $commentaire->getCreatedAt(),
            )
        );
    }

    public function update(Commentaire $commentaire)
    {
        $q = $this->db->prepare(
            "UPDATE Commentaire SET commentaire = :commentaire, id_chapitre = :id_chapitre, id_user = :id_user, id_parent = :id_parent, signaled = :signaled, banished = :banished, created_at = :created_at WHERE id = :id"
        );
        $q->execute(
            array(
                ":commentaire" => $commentaire->getText(),
                ":id_chapitre" => $commentaire->getChapitre()->getId(),
                ":id_user" => $commentaire->getUser()->getId(),
                ":id_parent" => $commentaire->getCommentaireParent()->getId(),
                ":signaled" => $commentaire->isSignaled(),
                ":banished" => $commentaire->isBanished(),
                ":created_at" => $commentaire->getCreatedAt(),
            )
        );
    }

    public function getOne($id)
    {
        $q = $this->db->prepare(
            "SELECT id, commentaire, id_chapitre, id_parent, id_user, signaled, banished, created_at FROM Commentaire WHERE  id = :id"
        );
        $q->execute(array(":id" => $id));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);

        return new Commentaire($donnees);
    }

    public function getAll()
    {
        $commentaires = [];

        $q = $this->db->query(
            "SELECT id,commentaire, id_chapitre, id_parent, id_user, signaled, banished, created_at FROM Commentaire "
        );
        while ($donnees = $q->fetch(\PDO::FETCH_ASSOC)) {
            $commentaires[] = new Commentaire($donnees);
        }

        return $commentaires;
    }

    public function getAllForAChapitre($id)
    {
        $commentaires = [];

        $q = $this->db->prepare(
            "SELECT id,commentaire, id_chapitre, id_parent, id_user, signaled, banished, created_at FROM Commentaire WHERE id_chapitre = :id"
        );
        $q->execute(array(":id" => $id));
        while ($donnees = $q->fetch(\PDO::FETCH_ASSOC)) {
            $userManager = new UserManager();
            $user = $userManager->findOneById($donnees['id_user']);
            $chapitreManager = new ChapitreManager();
            $chapitre = $chapitreManager->getOne($donnees['id_chapitre']);
            $donnees['user'] = $user;
            $donnees['chapitre'] = $chapitre;
            if($donnees['id_parent']){
                $commentaireParent = $this->getOne($donnees['id_parent']);
                $donnees['commentaireParent'] = $commentaireParent;
            }
            $commentaires[] = new Commentaire($donnees);
        }

        return $commentaires;
    }

    public function bannish(Commentaire $commentaire)
    {
        $q = $this->db->prepare("UPDATE Commentaire SET banished = :banished WHERE id = :id");
        $q->execute(
            array(
                ":banished" => $commentaire->isBanished(),
                ":id" => $commentaire->getId(),
            )
        );
    }

    public function signaled(Commentaire $commentaire)
    {
        $q = $this->db->prepare("UPDATE Commentaire SET signaled = :signaled WHERE id = :id");
        $q->execute(array(":signaled" => $commentaire->isSignaled()));
    }

}