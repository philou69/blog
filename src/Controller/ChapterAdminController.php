<?php


namespace App\Controller;


use App\Entity\Chapter;
use App\Manager\ChapterManager;
use App\Validator\ChapterValidator;

/*
 * Controller des Chapters pour la zone admin
 */
class ChapterAdminController extends AdminController
{
    /*
     * Affichage de tous les chapitres
     */
    public function chaptersAction(){

        $this->isAuthorized();
        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findAll();

        echo $this->render('admin/chapters.html.twig', array('chapters' => $chapters), $_SESSION);
    }

    /*
     * Création d'un chapter
     */
    public function addAction(){
        $this->isAuthorized();
        // Initialisation d'un tableau d'erreurs
        $errors = [];
        // On verifie si la requete est en post ou en get
        // Cela nous definis si le fomrulaire à été validé ou pas
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            // On récupère toutes les données du form en désactivant d'hypotétique balises
            $title = htmlspecialchars($_POST['title']);
            $text = htmlspecialchars($_POST['chapter']);
            $publishedAt = htmlspecialchars($_POST['publishedAt']);
            $published = htmlspecialchars($_POST['published']);

            $chapterValidator = new  ChapterValidator();
            // On va ensuite vérifie sur chaqu'une d'elles si elles sont vide
            // et si elles correspondent au format demandé
            // et on remplit le tableau d'erreurs en fonction
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
                $errors[] = ['error' => "published", "message" => "Le statut de publication n'est pas valide"];
            }
            // Si le tableau d'erreurs est vide, on enregistre le chapter
            if(empty($errors)){

                $published = ($published == 'true' ? true : false );
                $chapterManager = new ChapterManager();
                $chapter = new Chapter();
                $chapter->setTitle($title)
                    ->setChapter($text)
                    ->setPublishedAt($publishedAt)
                    ->setPublished($published);
                $chapterManager->create($chapter);
                // On redirige vers la page des chapters
                $this->redirectTo('/admin/chapters');
            }

        }
        // On affiche la page du formulaire
        echo $this->render("admin/chapter.html.twig", array('errors' => $errors));
    }

    /*
     * Edition d'un chapter
     */
    public function editAction($id){
        $this->isAuthorized();
        // Vérification que le paramètre id est bien au format numérique
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable!");
        }
        // On cherche le chapter et vérifie s'il existe
        $chapterManager = new ChapterManager();
        $chapter = $chapterManager->findOneById($id);
        if(!$chapter){
            throw new \Exception("Page introuvable");
        }
        // Tableau des erreurs
        $errors = [];
        // Vérification de la validation du formulaire
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            // Récuperation des differents variables
            $title = htmlspecialchars($_POST['title']);
            $text = htmlspecialchars($_POST['chapter']);
            $publishedAt = htmlspecialchars($_POST['publishedAt']);
            $published = htmlspecialchars($_POST['published']);
            $chapterValidator = new  ChapterValidator();
            // Vérification du remplissage et du format
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

            // Mise à jour en bdd si pas d'erreurs
            if(empty($errors)){
                $published = ($published == 'true' ? true : false );
                $chapterManager = new ChapterManager();
                $chapter->setTitle($title)
                    ->setChapter($text)
                    ->setPublishedAt($publishedAt)
                    ->setPublished($published);

                $chapterManager->update($chapter);

                // On redirige vers la liste des chapters
                $this->redirectTo('/admin/chapters');
            }

        }

        // Affichage du formulaire
        echo $this->render("admin/chapter.html.twig", array('errors' => $errors, 'chapter' => $chapter));
    }

    /*
     * Suppression d'un chapitre
     */
    public function deleteAction($id){
        $this->isAuthorized();
        // Vérification du parametre et de l'existence du chapter
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable");
        }

        $chapterManager = new ChapterManager();
        $chapter = $chapterManager->findOneById($id);

        if(!$chapter){
            throw new \Exception("Page introuvable");
        }

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            // Si la valeur est bien supprimer, on detruit le chapter
            if(htmlspecialchars($_POST['delete']) == 'Supprimer'){
                $chapterManager->delete($chapter);
                // On redirige vers les chapters
                $this->redirectTo("/admin/chapters");
            }
        }

        echo $this->render("admin/delete_chapter.html.twig", array('chapter' => $chapter));
    }

    /*
     * Liste des chapters en cours de rédaction
     */
    public function chaptersDraftAction(){
        $this->isAuthorized();

        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findAllDraft();

        if(!$chapters){
            throw new \Exception("Page Introuvable");
        }

        echo $this->render("admin/chapters.html.twig", array('chapters' => $chapters));
    }

    /*
     * Liste des chapters publiés
     */
    public function chaptersPublishedAction(){
        $this->isAuthorized();

        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findAllPublished();

        if(!$chapters){
            throw new \Exception("Page Introuvable");
        }

        echo $this->render("admin/chapters.html.twig", array('chapters' => $chapters));
    }

}