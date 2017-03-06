<?php


namespace App\Manager;


use App\Entity\Chapter;
use App\Entity\Comment;
use App\Entity\PDO;
use App\Entity\Status;
use App\Entity\User;

class CommentManager
{
    private $db;

    function __construct()
    {
        $this->db = PDO::get();
    }

    public function add(Comment $comment)
    {
        // Fonction pour ajouter un comment
        if ($comment->getCommentParent() != null) {
            $q = $this->db->prepare(
                "INSERT INTO Comment(comment, id_chapter, id_user, id_parent, createdAt, place, id_status) VALUES (:comment, :id_chapter, :id_user, :id_parent, :createdAt, :place, :status)"
            );
            $q->bindValue(":id_parent", $comment->getCommentParent()->getId(), \PDO::PARAM_INT);
        } else {
            $q = $this->db->prepare(
                "INSERT INTO Comment(comment, id_chapter, id_user, id_parent, createdAt, place, id_status) VALUES (:comment, :id_chapter, :id_user, null, :createdAt, :place, :status)"
            );
        }
        $q->bindValue(":comment", $comment->getComment(), \PDO::PARAM_STR);
        $q->bindValue(":id_chapter", $comment->getChapter()->getId(), \PDO::PARAM_STR);
        $q->bindValue(":id_user", $comment->getUser()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":createdAt", $comment->getCreatedAt()->format("Y-m-d"));
        $q->bindValue(":place", $comment->getPlace(), \PDO::PARAM_INT);
        $q->bindValue(":status", $comment->getStatus()->getId(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function update(Comment $comment)
    {
        // Fonction pour mettre à jour un comment
        if ($comment->getCommentParent() != null) {
            $q = $this->db->prepare(
                "UPDATE Comment SET comment = :comment, id_chapter = :id_chapter, id_user = :id_user, id_parent = :id_parent, createdAt = :createdAt, place = :place, statusedBy = :statusedBy, statusedAt = :statusedAt, id_status = :status WHERE id = :id"
            );
            $q->bindValue(":id_parent", $comment->getCommentParent()->getId(), \PDO::PARAM_INT);
        } else {
            $q = $this->db->prepare(
                "UPDATE Comment SET comment = :comment, id_chapter = :id_chapter, id_user = :id_user, createdAt = :createdAt, place = :place, statusedBy = :statusedBy, statusedAt = :statusedAt,  id_status = :status WHERE id = :id"
            );
        }
        $q->bindValue(":comment", $comment->getComment(), \PDO::PARAM_STR);
        $q->bindValue(":id_chapter", $comment->getChapter()->getId(), \PDO::PARAM_STR);
        $q->bindValue(":id_user", $comment->getUser()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":createdAt", $comment->getCreatedAt()->format('Y-m-d'), \PDO::PARAM_STR);
        $q->bindValue(":place", $comment->getPlace(), \PDO::PARAM_INT);
        $q->bindValue(":statusedBy", $comment->getStatusedBy()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":statusedAt", $comment->getStatusedAt()->format("Y-m-d"), \PDO::PARAM_STR);
        $q->bindValue(":status", $comment->getStatus()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":id", $comment->getId(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function bannish(Comment $comment)
    {
        // Fonction mettant à jour le status banished

        $q = $this->db->prepare("UPDATE Comment SET statusedBy = :statusedBy, statusedAt = :statusedAt, id_status = :status  WHERE id = :id");
        $q->bindValue(":statusedBy", $comment->getStatusedBy()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":statusedAt", $comment->getStatusedAt()->format('Y-m-d'), \PDO::PARAM_STR);
        $q->bindValue(":status", $comment->getStatus()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":id", $comment->getId(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function signaled(Comment $comment)
    {
        // Fonction mettant à jour le status de signaled
        $q = $this->db->prepare("UPDATE Comment SET statusedBy = :statusedBy, statusedAt = :statusedAt, id_status = :status WHERE id = :id");
        $q->bindValue(":statusedBy", $comment->getStatusedBy()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":statusedAt", $comment->getStatusedAt()->format('Y-m-d'), \PDO::PARAM_STR);
        $q->bindValue(":status", $comment->getStatus()->getId());
        $q->bindValue(":id", $comment->getId(), \PDO::PARAM_INT);
        $q->execute();
    }


    public function findOneById($id)
    {
        // Fonction cherchant un comment par son id
        $q = $this->db->prepare(
            "SELECT id, comment, id_chapter, id_parent, id_user, createdAt, place, statusedBy, statusedAt, id_status FROM Comment WHERE  id = :id"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie le nombre d'entrée renvoyé
        if ($q->rowCount() == 0) {
            return false;
        }
        $data = $q->fetch(\PDO::FETCH_ASSOC);
        // On va ajouté l'objet chapter et user correspondant
        $data['chapter'] = $this->addChapter($data['id_chapter']);
        $data['user'] = $this->addUser($data['id_user']);
        // un comment a un parent si sa place n'est pas 1
        if ($data['place'] != 1) {
            $commentParent = new Comment();
            $commentParent->setId($data['id_parent']);
            $data['commentParent'] = $commentParent;
        } elseif ($data['place'] != 3) {
            // Si la place n'est pas 3 le commetnaire peut avoir des enfants
            $commentsEnfant = $this->findChildrenForOneComment($data['id']);
            $data["comments"] = $commentsEnfant;
        }
        if($data['statusedBy'] != null){
            $data['statusedBy'] = $this->addUser($data['statusedBy']);
        }else{
            unset($data['statusedAt']);
        }
        $data['status'] = $this->addStatus($data['id_status']);
        // On retourne le comment
        return new Comment($data);
    }


    public function findAll()
    {
        // Fonction retournant tous les comments
        // Tableau contenant les comments
        $comments = [];
        $q = $this->db->query(
            "SELECT Comment.id,comment, id_chapter, id_parent, id_user, createdAt, place, statusedBy, statusedAt, id_status FROM Comment ORDER BY id_status"
        );
        // On vérifie avoir au moins une entrée
        if ($q->rowCount() == 0) {
            return false;
        }
        // On boucle sur les entrées
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            $data['status'] = $this->addStatus($data['id_status']);
            // On ajoute le user et le chapter
            $data['chapter'] = $this->addChapter($data['id_chapter']);
            $data['user'] = $this->addUser($data['id_user']);
            if ($data['place'] != 1) {
                // si la place est 1, le comment n'a pas de parent
                $commentParent = new Comment();
                $commentParent->setId($data['id_parent']);
                $data['commentParent'] = $commentParent;
            } elseif ($data['place'] != 3) {
                // Si la place est 3, on a pas de comment enfants
                $commentsEnfants = $this->findChildrenForOneComment($data['id']);
                $data['comments'] = $commentsEnfants;
            }
            if($data['statusedBy'] != null){
                $data['statusedBy'] = $this->addUser($data['statusedBy']);
            }else{
                unset($data['statusedAt']);
            }
            $comments[] = new Comment($data);
        }
        return $comments;
    }

    public function findAllForAChapter($id)
    {
        // Fonction cherchant les comments d'un chapter
        // On selectionne uniquement les comments premiers
        // Tableau contenant les comment
        $comments = [];

        $q = $this->db->prepare(
            "SELECT id, comment, id_user, signaled, banished, createdAt, id_parent, place, statusedBy, statusedAt, id_status FROM Comment WHERE id_chapter = :id AND place = 1"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie avoir au moins une entrée
        if ($q->rowCount() == 0) {
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // Création d'un objet user passer dans le tableau data  ainsi qu'un objet chapter
            $data['status'] = $this->addStatus($data['id_status']);
            $data['user'] = $this->addUser($data['id_user']);
            $data['chapter'] = $this->addChapter($id);
            $data['comments'] = $this->findChildrenForOneComment($data['id']);
            if($data['statusedBy'] != null){
                $data['statusedBy'] = $this->addUser($data['statusedBy']);
            }else{
                unset($data['statusedAt']);
            }
            $comments[] = new Comment($data);
        }
        // On retourne le tableau de comments
        return $comments;
    }

    public function findChildrenForOneComment($id)
    {
        // Fonction cherchant les enfants d'un comment
        // Tableau contenant les comments
        $comments = [];
        $q = $this->db->prepare(
            "SELECT id, comment, id_user, id_chapter, banishedBy, banishedAt, statusedAt, statusedBy, id_status FROM Comment WHERE id_parent = :id"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie s'il y a des entrées
        if ($q->rowCount() < 1) {
            return null;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // On ajoute le chapter et l'user
            $data['chapter'] = $this->addChapter($data['id_chapter']);
            $data['user'] = $this->addUser($data['id_user']);
            $data['status'] = $this->addStatus($data['id_status']);
            if($data['statusedBy'] != null){
                $data['statusedBy'] = $this->addUser($data['statusedBy']);
            }else{
                unset($data['statusedAt']);
            }
            $data['comments'] = $this->findChildrenForOneComment($data['id']);

            $comments[] = new Comment($data);
        }

        // On retourne le tableau de comment.
        return $comments;
    }

    public function findAllSignaled(){
        $comments =[];
        $q = $this->db->query("SELECT id, comment, id_user, place, id_chapter, statusedBy, statusedAt, id_status FROM Comment WHERE id_status = 1");
        if($q->rowCount() == 0){
            return false;
        }
        while ($data =$q->fetch(\PDO::FETCH_ASSOC)){
            $data['status'] = $this->addStatus($data['id_status']);
            $data['user'] = $this->addUser($data['id_user']);
            $data['chapter'] = $this->addChapter($data['id_chapter']);
            $data['statusedBy'] = $this->addUser($data['statusedBy']);
            $comments[] =  new Comment($data);
        }
        return $comments;
    }
    public function findAllBanished(){
        $comments =[];
        $q = $this->db->query("SELECT id, comment, id_user, place, id_chapter, statusedBy, statusedAt, id_status FROM Comment WHERE id_status = 2");
        if($q->rowCount() == 0){
            return false;
        }
        while ($data =$q->fetch(\PDO::FETCH_ASSOC)){
            $data['status'] = $this->addStatus($data['id_status']);
            $data['user'] = $this->addUser($data['id_user']);
            $data['chapter'] = $this->addChapter($data['id_chapter']);
            $data['statusedBy'] = $this->addUser($data['statusedBy']);
            $comments[] =  new Comment($data);
        }
        return $comments;
    }
    public function addUser($id)
    {
        $userManager = new UserManager();
        $user = $userManager->findOneById($id);

        return $user;
    }

    public function addChapter($id)
    {
        $chapterManager = new ChapterManager();
        $chapter = $chapterManager->findOneById($id);

        return $chapter;
    }

    public function addStatus($id){
        $statusManager = new StatusManager();
        $status = $statusManager->findOneById($id);
        if($status == false){
            return;
        }
        return $status;
    }
}