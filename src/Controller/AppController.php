<?php

namespace App\Controller;

use App\Constraint\UserConstraint;
use App\Entity\Chapter;
use App\Entity\Comment;
use App\Entity\User;
use App\Manager\ChapterManager;
use App\Manager\CommentManager;
use App\Manager\ContentManager;
use App\Manager\UserManager;
use App\Router\RouterException;
use App\Validator\UserValidator;
use App\Validator\Validator;

class AppController extends Controller
{
    /*
     * Renvoie de la vue de l'accueil
     */
    public function indexAction()
    {
        session_start();
        // On vérifie si le visiteur viens pour la premier fois sur le site
        $this->session();
        // On récuper la liste des contents
        $contents = $this->contentManager->findAllPerPage("index");
        // On va récupérer le dernier article publier
        $chapterManager = new ChapterManager();
        $chapter = $chapterManager->findLastPublished();
        if (!$contents || !$chapter) {
            throw new \Exception("Page introuvable");
        }
        echo $this->render(
            'index.html.twig',
            array('contents' => $contents, 'chapter' => $chapter),
            $_SESSION
        );
    }
}