<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Entity\User;
use App\Manager\ChapterManager;
use App\Manager\CommentManager;

class CommentController extends AdminController
{
    /*
     * Création d'un comment
     */
    public function createAction($id)
    {
        session_start();
        // Vérification de l'id et de l'existence du chapter
        if (!is_numeric($id) || $_SESSION['id'] == null ) {
            throw new \Exception("Page not found");
        }
        $chapterManager = new ChapterManager();
        $chapter = $chapterManager->findOneById($id);
        if (!$chapter) {
            throw new \Exception("Page not found");
        }

        // Vérification de l'envoie du formulaire
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $message = htmlspecialchars($_POST['comment']);
            if (isset($message)) {
                $comment = new Comment();
                $user = new User();
                $user->setId($_SESSION['id']);
                $comment->setComment($message)
                    ->setChapter($chapter)
                    ->setUser($user)
                    ->setCreatedAt(new \DateTime());
                $commentManager = new CommentManager();
                $commentManager->create($comment);
                // Redirection sur la page du chapter
                $this->redirectTo("/chapter/$id");
            }
        }
        // Si le formlaire n'est pas envoyer, on redirige vers lapage d'accueil
        $this->redirectTo('/');
    }

    /*
     * Création d'une réponse à un comment
     */
    public function responseAction($id)
    {
        // Vérification de l'id et de l'existence du comment
        session_start();
        if (!is_numeric($id) || $_SESSION['id'] == null ) {
            throw new \Exception("Page Introuvable");
        }
        $commentManager = new CommentManager();
        $comment = $commentManager->findOneById($id);
        if (!$comment) {
            throw new \Exception("Page Introuvalbe");
        }
        // Le formulaire est il bien envoyer
        if (isset($_POST['response']) && !empty($_POST['response'])) {
            // on crée un comment qui contiendra la réponse
            $new_comment = new Comment();
            $text = null;
            $username = $comment->getUser()->getUsername();
            // On regarde la place du comment
            if ($comment->getPlace() == 3) {
                // Si elle vaut 3, on rajoute @username au message
                $text = "@".$username." ".htmlspecialchars($_POST['response']);
                // On passe à la réponse le comment en parent et la place 3
                $new_comment->setCommentParent($comment)
                    ->setPlace(3);
            } else {
                // autrement, on prends simplement le message
                $text = htmlspecialchars($_POST['response']);
                // On passe le comment en parent et la place du comment plus 1
                $new_comment->setCommentParent($comment)
                    ->setPlace($comment->getPlace() + 1);

            }
            $user = new User();
            $user->setId($_SESSION['id']);

            $new_comment->setComment($text)
                ->setUser($user)
                ->setChapter($comment->getChapter());
            $commentManager->create($new_comment);

            // Après enregistrer, on redirige vers la page du chapter
            $this->redirectTo("/chapter/".$comment->getChapter()->getId());
        }
    }

    /*
     * Signalement d'un commentaire
     */
    public function signalAction($id)
    {
        // On vérifie l'id, la connection du visiteur et l'existence du comment ainsi que s'il n'est pas déjà signalé ou bani
        session_start();
        if ($_SESSION['isconnected'] == false) {
            throw new \Exception("Page introuvable");
        }
        if (!is_numeric($id)) {
            throw new \Exception("Page introuvable!");
        }
        $commentManager = new CommentManager();
        $comment = $commentManager->findOneById($id);
        if ($comment == false || $comment->getStatus()->getId() != 3) {
            throw new \Exception("Page Introuvable");
        }
        // On passe l'etat du comment à 1 et on ajout le nom du signaleur et la date
        $now = new \DateTime();
        $user = new User();
        $user->setId($_SESSION['id']);
        $comment->setStatusedBy($user)
            ->setStatusedAt($now)
            ->getStatus()->setId("1");
        $commentManager->update($comment);
        $idChapter = $comment->getChapter()->getId();

        // On retourne enfin à la page du chapter correspondant
        $this->redirectTo("/chapter/$idChapter");
    }





}