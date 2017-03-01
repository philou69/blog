<?php


namespace App\Controller;


use App\Manager\ChapterManager;
use App\Manager\CommentManager;
use App\Validator\ChapterValidator;

class ChapterController extends AdminController
{
    public function chaptersAction(){
        session_start();
        // On vérifie si le visiteur viens pour la premier fois sur le site
        $this->session();
        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findPublished();

        if (!$chapters) {
            throw new \Exception("Page introuvable");
        }
        $this->render(
            'chapters.html.twig',
            array('chapters' => $chapters),
            $_SESSION
        );
    }

    public function chapterAction($id)
    {
        session_start();
        if (!is_numeric($id)) {
            throw new RouterException("$id has to be a number");
        }
        $chapterManager = new ChapterManager();
        $commentManager = new CommentManager();
        $chapter = $chapterManager->findOneById($id);
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            if(isset($_POST['comment']) && !empty($_POST['comment'])){
                $commentPosted = htmlspecialchars($_POST['comment']);
                $userManager = new UserManager();
                $user = $userManager->findOneById($_SESSION['id']);
                $comment = new Comment();
                $now = new \DateTime();
                $comment->setComment($commentPosted)
                    ->setChapter($chapter)
                    ->setUser($user);
                $commentManager->add($comment);
            }
        }
        $comments = $commentManager->findAllForAChapter($id);
        // On vérifie la présence du chapter
        // Comme les comments ne sont pas obligatoire, on ne vérifie pas
        if (!$chapter || $chapter->getPublishedAt() > new \DateTime() || !$chapter->isPublished()) {
            throw new \Exception("Page Introuvable");
        }

        $this->render(
            'chapter.html.twig',
            array('chapter' => $chapter, 'comments' => $comments),
            $_SESSION
        );
    }

    public function chaptersAdminAction(){
        $this->isAuthorized();
        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findAll();

        $this->render('admin/chapters.html.twig', array('chapters' => $chapters), $_SESSION);
    }

    public function addChapterAction(){
        $this->isAuthorized();
        $errors = [];
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $title = htmlspecialchars($_POST['title']);
            $text = htmlspecialchars($_POST['chapter']);
            $publishedAt = htmlspecialchars($_POST['publishedAt']);
            $published = htmlspecialchars($_POST['published']);
            $chapterValidator = new  ChapterValidator();
            if(empty($title)){
                $errors[] = ['error' => "title", "message" => "Le titre ne peut être vide"];
            }else if(!$chapterValidator->isTitle($title)){
                $errors[] = ['error' => "title", "message" => "Le titre n'est pas au bon format!"];
            }
            if(empty($chapter)){
                $errors[] = ['error' => "chapter", "message" => "Le chapter ne peut être vide"];
            }elseif (!$chapterValidator->isChapter($chapter)){
                $errors[] = ['error' => "chapter", "message" => "Le chapter n'est pas au bon format!"];
            }
            if(empty($published_at)){
                $errors[] = ['error' => "published_at", "message" => "La date de publication ne peut être vide!"];
            }elseif(!$chapterValidator->isDate($published_at)){
                $errors[]= ['error' => "published_at", "message" => "La date n'est pas valide!"];
            }
            if(!isset($published)){
                $errors[] = ['error' => "published", "message" => "Le statut de la publication ne peut être vide"];
            }elseif (!$chapterValidator->isPublished($published)){
                $errors[] = ['erro' => "published", "message" => "Le statut de publication n'est pas valide"];
            }

            if(empty($errors)){
                $chapterManager = new ChapterManager();
                $chapter = new Chapter();
                $chapter->setTitle($title)
                    ->setChapter($chapter)
                    ->setPublishedAt($published_at)
                    ->setPublished($published);
                $chapterManager->add($chapter);
                $this->redirectTo('/admin/chapters');
            }

        }
        $this->render("admin/chapter.html.twig", array('errors' => $errors));
    }

    public function editChapterAction($id){
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable!");
        }
        $chapterManager = new ChapterManager();
        $chapter = $chapterManager->findOneById($id);
        if(!$chapter){
            throw new \Exception("Page introuvable");
        }
        $errors = [];
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $title = htmlspecialchars($_POST['title']);
            $text = htmlspecialchars($_POST['chapter']);
            $publishedAt = htmlspecialchars($_POST['publishedAt']);
            $published = htmlspecialchars($_POST['published']);
            $chapterValidator = new  ChapterValidator();
            if(empty($title)){
                $errors[] = ['error' => "title", "message" => "Le titre ne peut être vide"];
            }else if(!$chapterValidator->isTitle($title)){
                $errors[] = ['error' => "title", "message" => "Le titre n'est pas au bon format!"];
            }
            if(empty($text)){
                $errors[] = ['error' => "chapter", "message" => "Le chapter ne peut être vide"];
            }elseif (!$chapterValidator->isChapter($text)){
                $errors[] = ['error' => "chapter", "message" => "Le chapter n'est pas au bon format!"];
            }
            if(empty($publishedAt)){
                $errors[] = ['error' => "published_at", "message" => "La date de publication ne peut être vide!"];
            }elseif(!$chapterValidator->isDate($publishedAt)){
                $errors[]= ['error' => "published_at", "message" => "La date n'est pas valide!"];
            }
            if(!isset($published)){
                $errors[] = ['error' => "published", "message" => "Le statut de la publication ne peut être vide"];
            }elseif (!$chapterValidator->isPublished($published)){
                $errors[] = ['erro' => "published", "message" => "Le statut de publication n'est pas valide"];
            }

            if(empty($errors)){
                $chapterManager = new ChapterManager();
                $chapter->setTitle($title)
                    ->setChapter($text)
                    ->setPublishedAt($publishedAt)
                    ->setPublished($published);

                $chapterManager->update($chapter);
                $this->redirectTo('/admin/chapters');
            }

        }
        $this->render("admin/chapter.html.twig", array('errors' => $errors, 'chapter' => $chapter));
    }

    public function deleteChapterAction($id){
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable");
        }

        $chapterManager = new ChapterManager();
        $chapter = $chapterManager->findOneById($id);
        if(!$chapter){
            throw new \Exception("Page introuvable");
        }
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            if(htmlspecialchars($_POST['delete']) == 'Supprimer'){
                $chapterManager->delete($chapter);
                $this->redirectTo("/admin/chapters");
            }
        }

        $this->render("admin/delete_chapter.html.twig", array('chapter' => $chapter));
    }

    public function chaptersDraftAction(){
        $this->isAuthorized();

        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findAllDraft();

        if(!$chapters){
            throw new \Exception("Page Introuvable");
        }

        $this->render("admin/chapters.html.twig", array('chapters' => $chapters));
    }

    public function chaptersPublishedAction(){
        $this->isAuthorized();

        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findAllPublished();

        if(!$chapters){
            throw new \Exception("Page Introuvable");
        }

        $this->render("admin/chapters.html.twig", array('chapters' => $chapters));
    }

}