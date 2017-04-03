<?php


namespace App\Controller;

use App\Entity\Comment;
use App\Manager\ChapterManager;
use App\Manager\CommentManager;
use App\Manager\UserManager;

class ChapterController extends AdminController
{
    /*
     * Liste des chapters visibles par les visiteurs
     */
    public function chaptersAction($page = null){
        session_start();
        // On vérifie si le visiteur viens pour la premier fois sur le site
        $this->session();
        // Récuperation des chapters publiés
        if($page === null || !is_numeric($page)){
            $page = 1;
        }
        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findPublished();
        $offset = ((2 * $page) -2);
        $limit = 2;
        $numberPage = ceil(count($chapters) / $limit);
        if (!$chapters) {
            throw new \Exception("Page introuvable");
        }
        // Affichage de la vue
        echo $this->render(
            'chapters.html.twig',
            array('chapters' => $chapters, 'page' => $page, 'offset' => $offset, 'limit' => $limit, 'numberPage' => $numberPage),
            $_SESSION
        );
    }

    /*
     * Affichage d'un chapter en particulier
     */
    public function chapterAction($id)
    {
        session_start();
        // Vérification du paramètre et de l'existence du
        if (!is_numeric($id)) {
            throw new \Exception("$id has to be a number");
        }
        $chapterManager = new ChapterManager();
        $commentManager = new CommentManager();
        $chapter = $chapterManager->findOneById($id);

        // On vérifie la présence du chapter
        // Comme les comments ne sont pas obligatoire, on ne vérifie pas
        if (!$chapter || $chapter->getPublishedAt() > new \DateTime() || !$chapter->isPublished()) {
            throw new \Exception("Page Introuvable");
        }

        // On réupere les comments correspondant
        $comments = $commentManager->findAllForAChapter($id);
        // affichage vers la vue
        echo $this->render(
            'chapter.html.twig',
            array('chapter' => $chapter, 'comments' => $comments),
            $_SESSION
        );
    }

}