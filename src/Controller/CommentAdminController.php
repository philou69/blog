<?php


namespace App\Controller;


use App\Entity\User;
use App\Manager\CommentManager;

/*
 * Controller des Comments en zone admin
 */
class CommentAdminController extends AdminController
{
    /*
     * Liste des comments
     */
    public function commentsAction()
    {
        $this->isAuthorized();
        $commentManager = new CommentManager();
        $comments = $commentManager->findAll();
        echo $this->render('admin/comments.html.twig', array('comments' => $comments), $_SESSION);
    }

    /*
     * Liste des comments signaler
     */
    public function signaledCommentsAction()
    {
        $this->isAuthorized();

        $commentManager = new CommentManager();
        $comments = $commentManager->findAllSignaled();
        echo $this->render("admin/comments.html.twig", array('comments' => $comments));
    }

    /*
     * liste des comments banis
     */
    public function banishedCommentsAction()
    {
        $this->isAuthorized();

        $commentManager = new CommentManager();
        $comments = $commentManager->findAllBanished();
        echo $this->render("admin/comments.html.twig", array('comments' => $comments));
    }

    /*
     * Modification du status d'un comments
     */
    public function editAction($id)
    {
        $this->isAuthorized();
        // Vérification du paramètre et de l'existence du comment
        if (!is_numeric($id)) {
            throw new \Exception("Page introuvable");
        }
        $commentManager = new CommentManager();
        $comment = $commentManager->findOneById($id);
        if (!$comment) {
            throw new \Exception("Page introuvable");
        }
        // Tableau des erreurs
        $errors = [];

        // Vérification si le formulaire est validé
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $etat = htmlspecialchars($_POST['etat']);
            if (!isset($etat)) {
                $errors[] = ["error" => "etat", "message" => "L'etat du comment ne peut être vide"];
            } elseif ($etat != "normal" && $etat != "signaled" && $etat != "banished") {
                $errors[] = ["error" => "etat", "message" => "L'eta du comment n'est pas au bon format"];
            }

            // S'il n'y a pas d'erreurs, on enregistre les modifications
            if (empty($errors)) {
                $user = new User();
                $user->setId($_SESSION['id']);
                if ($etat == "normal") {
                    $comment->setStatusedBy(null)
                        ->setStatusedAt(null)
                        ->getStatus()->setId("3");
                } elseif ($etat == "signaled") {
                    $comment->setStatusedBy($user)
                        ->setStatusedAt(new \DateTime())
                        ->getStatus()->setId("1");
                } elseif ($etat == "banished") {
                    $comment->setStatusedBy($user)
                        ->setStatusedAt(new \DateTime())
                        ->getStatus()->setId("2");
                }
                $commentManager->update($comment);
                $this->redirectTo('/admin/comments');
            }

        }

        // Affichage du formulaire
        echo $this->render("admin/comment.html.twig", array('comment' => $comment, 'errors' => $errors));

    }
}