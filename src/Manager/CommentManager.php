<?php


namespace App\Manager;


use App\Entity\Chapter;
use App\Entity\Comment;
use App\Entity\PDO;
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
                "INSERT INTO Comment(comment, id_chapter, id_user, id_parent, signaled, banished, createdAt, place) VALUES (:comment, :id_chapter, :id_user, :id_parent, :signaled, :banished, :createdAt, :place)"
            );
            $q->bindValue(":id_parent", $comment->getCommentParent()->getId(), \PDO::PARAM_INT);
        } else {
            $q = $this->db->prepare(
                "INSERT INTO Comment(comment, id_chapter, id_user, id_parent, signaled, banished, createdAt, place) VALUES (:comment, :id_chapter, :id_user, null, :signaled, :banished, :createdAt, :place)"
            );
        }
        $q->bindValue(":comment", $comment->getComment(), \PDO::PARAM_STR);
        $q->bindValue(":id_chapter", $comment->getChapter()->getId(), \PDO::PARAM_STR);
        $q->bindValue(":id_user", $comment->getUser()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":signaled", $comment->isSignaled(), \PDO::PARAM_BOOL);
        $q->bindValue(":banished", $comment->isBanished(), \PDO::PARAM_BOOL);
        $q->bindValue(":createdAt", $comment->getCreatedAt(), \PDO::PARAM_STR);
        $q->bindValue(":place", $comment->getPlace(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function update(Comment $comment)
    {
        // Fonction pour mettre à jour un comment
        if ($comment->getCommentParent() != null) {
            $q = $this->db->prepare(
                "UPDATE Comment SET comment = :comment, id_chapter = :id_chapter, id_user = :id_user, id_parent = :id_parent, signaled = :signaled, banished = :banished, createdAt = :createdAt, place = :place WHERE id = :id"
            );
            $q->bindValue(":id_parent", $comment->getCommentParent()->getId(), \PDO::PARAM_INT);
        } else {
            $q = $this->db->prepare(
                "UPDATE Comment SET comment = :comment, id_chapter = :id_chapter, id_user = :id_user, signaled = :signaled, banished = :banished, createdAt = :createdAt, place = :place WHERE id = :id"
            );
        }
        $q->bindValue(":comment", $comment->getComment(), \PDO::PARAM_STR);
        $q->bindValue(":id_chapter", $comment->getChapter()->getId(), \PDO::PARAM_STR);
        $q->bindValue(":id_user", $comment->getUser()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":signaled", $comment->isSignaled(), \PDO::PARAM_BOOL);
        $q->bindValue(":banished", $comment->isBanished(), \PDO::PARAM_BOOL);
        $q->bindValue(":createdAt", $comment->getCreatedAt(), \PDO::PARAM_STR);
        $q->bindValue(":place", $comment->getPlace(), \PDO::PARAM_INT);
        $q->bindValue(":id", $comment->getId(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function bannish(Comment $comment)
    {
        // Fonction mettant à jour le status banished

        $q = $this->db->prepare("UPDATE Comment SET banished = :banished WHERE id = :id");
        $q->bindValue(":banished", $comment->isBanished(), \PDO::PARAM_BOOL);
        $q->bindValue(":id", $comment->getId(), \PDO::PARAM_INT);
        $q->execute();
    }

    public function signaled(Comment $comment)
    {
        // Fonction mettant à jour le status de signaled
        $q = $this->db->prepare("UPDATE Comment SET signaled = :signaled, signaledBy = :signaledBy, signaledAt = :signaledAt WHERE id = :id");
        $q->bindValue(":signaled", $comment->isSignaled(), \PDO::PARAM_BOOL);
        $q->bindValue(":signaledBy", $comment->getSignaledBy()->getId(), \PDO::PARAM_INT);
        $q->bindValue(":signaledAt", $comment->getSignaledAt(), \PDO::PARAM_STR);
        $q->bindValue(":id", $comment->getId(), \PDO::PARAM_INT);
        $q->execute();
    }


    public function findOneById($id)
    {
        // Fonction cherchant un comment par son id
        $q = $this->db->prepare(
            "SELECT id, comment, id_chapter, id_parent, id_user, signaled, banished, createdAt, place, lastChild, signaledBy, signaledAt, banishedBy, banishedAt FROM Comment WHERE  id = :id"
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
        if($data['signaledBy'] != null){
            $data['signaledBy'] = $this->addUser($data['signaledBy']);
        }else{
            unset($data['signaledAt']);
        }
        if($data['banishedBy'] != null){
            $data['banishedBy'] = $this->addUser($data['banishedBy']);
        }else{
            unset($data['banishedAt']);
        }
        // On retourne le comment
        return new Comment($data);
    }


    public function findAll()
    {
        // Fonction retournant tous les comments
        // Tableau contenant les comments
        $comments = [];
        $q = $this->db->query(
            "SELECT id,comment, id_chapter, id_parent, id_user, signaled, banished, createdAt, place, signaledBy, signaledAt, banishedBy, banishedAt FROM Comment"
        );
        // On vérifie avoir au moins une entrée
        if ($q->rowCount() == 0) {
            return false;
        }
        // On boucle sur les entrées
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
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
            if($data['signaledBy'] != null){
                $data['signaledBy'] = $this->addUser($data['signaledBy']);
            }else{
                unset($data['signaledAt']);
            }
            if($data['banishedBy'] != null){
                $data['banishedBy'] = $this->addUser($data['banishedBy']);
            }else{
                unset($data['banishedAt']);
            }
            $comments[] = new Comment($data);
        }

        // On renvoie le tableau de comments
        return $comments;
    }

    public function findAllForAChapter($id)
    {
        // Fonction cherchant les comments d'un chapter
        // On selectionne uniquement les comments premiers
        // Tableau contenant les comment
        $comments = [];

        $q = $this->db->prepare(
            "SELECT id, comment, id_user, signaled, banished, createdAt, id_parent, place, signaledBy, signaledAt, banishedBy, banishedAt FROM Comment WHERE id_chapter = :id AND place = 1"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie avoir au moins une entrée
        if ($q->rowCount() == 0) {
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // Création d'un objet user passer dans le tableau data  ainsi qu'un objet chapter
            $data['user'] = $this->addUser($data['id_user']);
            $data['chapter'] = $this->addChapter($id);
            $commentsEnfants = $this->findChildrenForOneComment($data['id']);
            if($commentsEnfants){
                $data['comments'] = $commentsEnfants;
            }
            if($data['signaledBy'] != null){
                $data['signaledBy'] = $this->addUser($data['signaledBy']);
            }else{
                unset($data['signaledAt']);
            }
            if($data['banishedBy'] != null){
                $data['banishedBy'] = $this->addUser($data['banishedBy']);
            }else{
                unset($data['banishedAt']);
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
            "SELECT id, comment, id_user, banished, signaled, id_chapter, banishedBy, banishedAt, signaledAt, signaledBy FROM Comment WHERE id_parent = :id AND place = 2"
        );
        $q->bindValue(":id", $id, \PDO::PARAM_INT);
        $q->execute();
        // On vérifie s'il y a des entrées
        if ($q->rowCount() < 1) {
            return false;
        }
        while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
            // On ajoute le chapter et l'user
            $data['chapter'] = $this->addChapter($data['id_chapter']);
            $data['user'] = $this->addUser($data['id_user']);
            $commentsEnfants = [];
            $nq = $this->db->prepare(
                "SELECT id, comment, id_user, place, banished, signaled, id_chapter, banishedBy, banishedAt, signaledAt, signaledBy FROM Comment WHERE id_parent = :id AND place = 3"
            );
            $nq->bindValue(":id", $data['id'], \PDO::PARAM_INT);
            $nq->execute();
            // S'il y a au minimum 1 entrée on ajoute le(s) comment(s)
            if ($nq->rowCount() > 0) {
                while ($dataEnfants = $nq->fetch(\PDO::FETCH_ASSOC)) {
                    // On ajoute le chapter et le user
                    $dataEnfants['chapter'] = $this->addChapter($dataEnfants['id_chapter']);
                    $dataEnfants['user'] = $this->addUser($dataEnfants['id_user']);
                    $commentsEnfants[] = new Comment($dataEnfants);
                    if($dataEnfants['signaledBy'] != null){
                        $dataEnfants['signaledBy'] = $this->addUser($dataEnfants['signaledBy']);
                    }else{
                        unset($dataEnfants['signaledAt']);
                    }
                    if($dataEnfants['banishedBy'] != null){
                        $dataEnfants['banishedBy'] = $this->addUser($dataEnfants['banishedBy']);
                    }else{
                        unset($dataEnfants['banishedAt']);
                    }
                }
                $data['comments'] = $commentsEnfants;

                if($data['signaledBy'] != null){
                    $data['signaledBy'] = $this->addUser($data['signaledBy']);
                }else{
                    unset($data['signaledAt']);
                }
                if($data['banishedBy'] != null){
                    $data['banishedBy'] = $this->addUser($data['banishedBy']);
                }else{
                    unset($data['banishedAt']);
                }
            }
            $comments[] = new Comment($data);
        }

        // On retourne le tableau de comment.
        return $comments;
    }

    public function findAllSignaled(){
        $comments =[];
        $q = $this->db->query("SELECT id, comment, id_user, place, banished, signaled, id_chapter FROM Comment WHERE signaled is true");
        if($q->rowCount() == 0){
            return false;
        }
        while ($data =$q->fetch(\PDO::FETCH_ASSOC)){
            $data['user'] = $this->addUser($data['id_user']);
            $data['chapter'] = $this->addChapter($data['id_chapter']);
            $comments[] =  new Comment($data);
        }
        return $comments;
    }
    public function findAllBanished(){
        $comments =[];
        $q = $this->db->query("SELECT id, comment, id_user, place, banished, signaled, id_chapter FROM Comment WHERE banished is true");
        if($q->rowCount() == 0){
            return false;
        }
        while ($data =$q->fetch(\PDO::FETCH_ASSOC)){
            $data['user'] = $this->addUser($data['id_user']);
            $data['chapter'] = $this->addChapter($data['id_chapter']);
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
}