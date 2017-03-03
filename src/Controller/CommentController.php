<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Entity\User;
use App\Manager\CommentManager;

class CommentController extends AdminController
{
    public function signalAction($id)
    {
        session_start();
        if($_SESSION['isconnected'] == false){
            throw new \Exception("Page introuvable");
        }
        if (!is_numeric($id)) {
            throw new \Exception("Page introuvable!");
        }
        $commentManager = new CommentManager();
        $comment = $commentManager->findOneById($id);
        if ($comment == false) {
            throw new \Exception("Page Introuvable");
        }
        $now = new \DateTime();
        $user = new User();
        $user->setId($_SESSION['id']);
        $comment->getSstatus()->setId("1")
            ->setStatusedBy($user)
            ->setStatusedAt($now);
        $commentManager->update($comment);
        $idChapter = $comment->getChapter()->getId();
        $this->redirectTo("/chapter/$idChapter");
    }

    public function responseAction($id)
    {
        if (!is_numeric($id)) {
            throw new \Exception("Page Introuvable");
        }
        $commentManager = new CommentManager();
        $comment = $commentManager->findOneById($id);
        if (!$comment) {
            throw new \Exception("Page Introuvalbe");
        }
        session_start();
        if (isset($_POST['response']) && !empty($_POST['response'])) {
            // on crée un comment qui contiendra la réponse
            $new_comment = new Comment();
            $text = null;
            $username = $comment->getUser()->getUsername();
            // On regarde la place du comment
            if ($comment->getPlace() == 3) {
                // Si elle vaut 3, on rajoute @username au message
                $text = "@".$username." ".htmlspecialchars($_POST['response']);
                // On passe à la reponse le comment parent  en parentet la place 3
                $new_comment->setCommentParent($comment->getCommentParent())
                    ->setPlace(3);
            }else{
                // autrement, on prends simplement le message
                $text = htmlspecialchars($_POST['response']);
                // On passe le comment en parent et la place du comment plus 1
                $new_comment->setCommentParent($comment)
                    ->setPlace($comment->getPlace()+1);

            }
            $user = new User(array('id' => $_SESSION['id']));


            $new_comment->setComment($text)
                ->setUser($user)
                ->setChapter($comment->getChapter());
            $commentManager->add($new_comment);

            $this->redirectTo("/chapter/".$comment->getChapter()->getId());
        }
    }
    public function commentsAction(){
        $this->isAuthorized();
        $commentManager = new CommentManager();
        $comments = $commentManager->findAll();
        $this->render('admin/comments.html.twig', array('comments' => $comments), $_SESSION);
    }

    public function editCommentAction($id){
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable");
        }
        $commentManager = new CommentManager();
        $comment = $commentManager->findOneById($id);
        if(!$comment){
            throw new \Exception("Page introuvable");
        }
        $errors = [];

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $etat = htmlspecialchars($_POST['etat']);
            if(!isset($etat)){
                $errors[] = ["error" => "etat", "message" => "L'etat du comment ne peut être vide"];
            }elseif ($etat != "normal" && $etat != "signaled" && $etat != "banished" ){
                $errors[] = ["error" => "etat", "message" =>"L'eta du comment n'est pas au bon format"];
            }

            if(empty($errors)){
                $user = new User();
                $user->setId($_SESSION['id']);
                if($etat == "normal"){
                    $comment->getStatus()->setId("3")
                        ->setStatusedBy(null)
                        ->setStatusedAt(null);
                }elseif ($etat == "signaled"){
                    $comment->getStatus()->setId("1")
                        ->setStatusedBy($user)
                        ->setStatusedAt(new \DateTime());
                }elseif($etat == "banished"){
                    $comment->getStatus()->setId("1")
                        ->setStatusedBy($user)
                        ->setStatusedAt(new \DateTime());
                }
                $commentManager->update($comment);
                $this->redirectTo('/admin/comments');
            }

        }

        $this->render("admin/comment.html.twig", array('comment' => $comment, 'errors' => $errors));

    }

    public function signaledCommentsAction(){
        $this->isAuthorized();

        $commentManager = new CommentManager();
        $comments = $commentManager->findAllSignaled();

        $this->render("admin/comments.html.twig",array('comments' => $comments));
    }

    public function banishedCommentsAction(){
        $this->isAuthorized();

        $commentManager = new CommentManager();
        $comments = $commentManager->findAllBanished();

        $this->render("admin/comments.html.twig",array('comments' => $comments));
    }
}