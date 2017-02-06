<?php


namespace App\Manager;


use App\Entity\Commentaire;
use App\Entity\PDO;
use App\Entity\User;

class CommentaireManager
{
    private $db;

    function __construct()
    {
        $db = PDO::get();
    }

    public function add(Commentaire $commentaire)
    {
        $q = $this->db->prepare(
            "INSERT INTO Commentaire(text, id_episode, id_user, id_parent, signaled, bannished, created_at) VALUES (:text, :id_episode, :id_user, :id_parent, :signaled, :bannished, :created_at)"
        );
        $q->execute(
            array(
                ":text" => $commentaire->getText(),
                ":id_episode" => $commentaire->getEpisode()->getId(),
                ":id_user" => $commentaire->getUser()->getId(),
                ":id_parent" => $commentaire->getCommentaireParent()->getId(),
                ":signaled" => $commentaire->isSignaled(),
                ":bannished" => $commentaire->isBanished(),
                ":created_at" => $commentaire->getCreatedAt(),
            )
        );
    }

    public function update(Commentaire $commentaire)
    {
        $q = $this->db->prepare(
            "UPDATE Commentaire SET text = :text, id_episode = :id_episode, id_user = :id_user, id_parent = :id_parent, signaled = :signaled, bannished = :bannished, created_at = :created_at WHERE id = :id"
        );
        $q->execute(
            array(
                ":text" => $commentaire->getText(),
                ":id_episode" => $commentaire->getEpisode()->getId(),
                ":id_user" => $commentaire->getUser()->getId(),
                ":id_parent" => $commentaire->getCommentaireParent()->getId(),
                ":signaled" => $commentaire->isSignaled(),
                ":bannished" => $commentaire->isBanished(),
                ":created_at" => $commentaire->getCreatedAt(),
            )
        );
    }
    public function getOne($id){
        $q = $this->db->prepare("SELECT id, text, id_episode, id_commentaire_parent, id_user, signaled, bannished, created_at FROM Commentaire WHERE  id = :id");
        $q->execute(array(":id" => $id));
        $donnees = $q->fetch(\PDO::FETCH_ASSOC);
        return new Commentaire($donnees);
    }

    public function getAll(){
        $commentaires = [];

        $q = $this->db->query("SELECT id,text, id_episode, id_commentaire_parent, id_user, signaled, bannished, created_at FROM Commentaire ");
        while ($donnees = $q->fetch(\PDO::FETCH_ASSOC)){
            $commentaires[] = new Commentaire($donnees);
        }
        return $commentaires;
    }

    public function getAllOfEpisode($id){
        $commentaires = [];

        $q = $this->db->prepare("SELECT id,text, id_episode, id_commentaire_parent, id_user, signaled, bannished, created_at FROM Commentaire WHERE id = :id");
        $q->execute(array(":id" => $id));
        while ($donnees = $q->fetch(\PDO::FETCH_ASSOC)){
            $commentaires[] = new Commentaire($donnees);
        }
        return $commentaires;
    }

    public function bannish(Commentaire $commentaire){
        $q = $this->db->prepare("UPDATE Commentaire SET bannished = :bannished WHERE id = :id");
        $q->execute(array(":bannished" => $commentaire->isBanished(),
                ":id" => $commentaire->getId()));
    }

    public  function signaled(Commentaire $commentaire){
        $q = $this->db->prepare("UPDATE Commentaire SET signaled = :signaled WHERE id = :id");
        $q->execute(array(":signaled" => $commentaire->isSignaled()));
    }

}