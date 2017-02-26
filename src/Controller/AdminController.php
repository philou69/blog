<?php


namespace App\Controller;


use App\Entity\Chapter;
use App\Entity\User;
use App\Manager\ChapterManager;
use App\Manager\CommentManager;
use App\Manager\ContentManager;
use App\Manager\PageManager;
use App\Manager\UserManager;
use App\Validator\ChapterValidator;
use App\Validator\UserValidator;

class AdminController extends Controller
{
    public function loginAction()
    {
        session_start();

        $errors = [];
        // On vérifie qe la methode est post et donc que le formulaire est passé
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // On vérifie la présence de données username et password en post
            // On va pour cela utiliser un validator
            if (isset($_POST['username']) && isset($_POST['password'])) {
                // On va vérifier les données avec un validator
                // Les regex sont gérer dans le validator
                $userValidator = new UserValidator();
                if (!$userValidator->isUsername($_POST['username'])) {
                    $errors[] = ["error" => "username", "message" => "Erreur sur le format du nom"];
                }
                if (!$userValidator->isPassword($_POST['password'])) {
                    $errors[] = ["error" => "password", "message" => "Erreur sur le format du mot de passe"];
                }
            } else {
                $errors[] = ["error" => "formulaire", "message" => "Il faut un nom et un mot de passe!"];
            }
            // Si $errors est vide, cela signifie que tous est ok
            // Et on connect l'utilisateur
            if (empty($errors)) {
                // On va chercher un utilisateur correspondant au username et password
                // Il n'est pas utile de protege les POST car password est  hashé et username est protéger automatiquement dans la requête
                $password = hash("sha512", $_POST["password"]);
                $userManager = new UserManager();
                $user = $userManager->findOneByUserNameAndPassword($_POST["username"], $password);
                // on vérifie l'existance de l'user
                if (!$user) {
                    throw new \Exception("L'user n'existe pas ou mauvais mot de passe");
                }
                if(!in_array("ROLE_ADMIN", $user->getRoles())){
                    $errors[] = ['error' => 'formulaire', 'message' => "L'utilisateur n'a pas accès à cette zone."];
                }else{
                    // On enregistre l'utilisateur dans une session
                    $this->fillSession($user);
                    // L'utilisateur étant enrgistrer on le renvoye vers la page d'accueil
                    $this->redirectTo('/admin/');
                }

            }
        }
        $this->render('admin/login.html.twig', array('errors' => $errors), $_SESSION);
    }

    public function indexAction(){
        $this->isAuthorized();

        $this->render('admin/index.html.twig');
    }

    private function isAuthorized(){
        session_start();
        if(!in_array("ROLE_ADMIN",$_SESSION['roles'])){
            session_unset();
            $this->redirectTo('/');
        }
    }

    public function chaptersAction(){
        $this->isAuthorized();
        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findAll();

        $this->render('admin/chapters.html.twig', array('chapters' => $chapters), $_SESSION);
    }

    public function addChapterAction(){
        $this->isAuthorized();
        $errors = [];
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $chapterValidator = new  ChapterValidator();
            if(empty(htmlspecialchars($_POST['title']))){
                $errors[] = ['error' => "title", "message" => "Le titre ne peut être vide"];
            }else if(!$chapterValidator->isTitle(htmlspecialchars($_POST['title']))){
                $errors[] = ['error' => "title", "message" => "Le titre n'est pas au bon format!"];
            }
            if(empty(htmlspecialchars($_POST['chapter']))){
                $errors[] = ['error' => "chapter", "message" => "Le chapter ne peut être vide"];
            }elseif (!$chapterValidator->isChapter(htmlspecialchars($_POST['chapter']))){
                $errors[] = ['error' => "chapter", "message" => "Le chapter n'est pas au bon format!"];
            }
            if(empty(htmlspecialchars($_POST['published_at']))){
                $errors[] = ['error' => "published_at", "message" => "La date de publication ne peut être vide!"];
            }elseif(!$chapterValidator->isDate(htmlspecialchars($_POST['published_at']))){
                $errors[]= ['error' => "published_at", "message" => "La date n'est pas valide!"];
            }
            if(!isset($_POST['published'])){
                $errors[] = ['error' => "published", "message" => "Le statut de la publication ne peut être vide"];
            }elseif (!$chapterValidator->isPublished(htmlspecialchars($_POST['published']))){
                $errors[] = ['erro' => "published", "message" => "Le statut de publication n'est pas valide"];
            }

            if(empty($errors)){
                $chapterManager = new ChapterManager();
                $chapter = new Chapter();
                $chapter->setTitle(htmlspecialchars($_POST['title']))
                    ->setChapter($_POST['chapter'])
                    ->setPublishedAt(htmlspecialchars($_POST['published_at']))
                    ->setPublished(htmlspecialchars($_POST['published']));
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
            $chapterValidator = new  ChapterValidator();
            if(empty(htmlspecialchars($_POST['title']))){
                $errors[] = ['error' => "title", "message" => "Le titre ne peut être vide"];
            }else if(!$chapterValidator->isTitle(htmlspecialchars($_POST['title']))){
                $errors[] = ['error' => "title", "message" => "Le titre n'est pas au bon format!"];
            }
            if(empty(htmlspecialchars($_POST['chapter']))){
                $errors[] = ['error' => "chapter", "message" => "Le chapter ne peut être vide"];
            }elseif (!$chapterValidator->isChapter(htmlspecialchars($_POST['chapter']))){
                $errors[] = ['error' => "chapter", "message" => "Le chapter n'est pas au bon format!"];
            }
            if(empty(htmlspecialchars($_POST['published_at']))){
                $errors[] = ['error' => "published_at", "message" => "La date de publication ne peut être vide!"];
            }elseif(!$chapterValidator->isDate(htmlspecialchars($_POST['published_at']))){
                $errors[]= ['error' => "published_at", "message" => "La date n'est pas valide!"];
            }
            if(!isset($_POST['published'])){
                $errors[] = ['error' => "published", "message" => "Le statut de la publication ne peut être vide"];
            }elseif (!$chapterValidator->isPublished(htmlspecialchars($_POST['published']))){
                $errors[] = ['erro' => "published", "message" => "Le statut de publication n'est pas valide"];
            }

            if(empty($errors)){
                $chapterManager = new ChapterManager();
                $chapter->setTitle(htmlspecialchars($_POST['title']))
                    ->setChapter($_POST['chapter'])
                    ->setPublishedAt(htmlspecialchars($_POST['published_at']))
                    ->setPublished(htmlspecialchars($_POST['published']));

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
            if(!isset($_POST['etat'])){
                $errors[] = ["error" => "etat", "message" => "L'etat du comment ne peut être vide"];
            }elseif (htmlspecialchars($_POST['etat']) != "normal" && htmlspecialchars($_POST['etat']) != "signaled" &&htmlspecialchars($_POST['etat']) != "banished" ){
                $errors[] = ["error" => "etat", "message" =>"L'eta du comment n'est pas au bon format"];
            }

            if(empty($errors)){
                $user = new User();
                $user->setId($_SESSION['id']);
                if($_POST['etat'] == "normal"){
                    $comment->setSignaled(false)
                        ->setSignaledBy(null)
                        ->setSignaledAt(null)
                        ->setBanished(false)
                        ->setSignaledBy(null)
                        ->setBanishedAt(null);
                    $commentManager->update($comment);
                }elseif ($_POST['etat'] == "signaled"){
                    $comment->setSignaled(true)
                        ->setSignaledBy($user)
                        ->setSignaledAt(new \DateTime())
                        ->setBanishedBy(null)
                        ->setBanishedAt(null)
                        ->setBanished(false);
                    $commentManager->signaled($comment);
                }elseif($_POST['etat'] == "banished"){
                    $comment->setSignaled(false)
                        ->setBanishedBy($user)
                        ->setBanishedAt(new \DateTime())
                        ->setSignaledBy(null)
                        ->setSignaledAt(null)
                        ->setBanished(true);
                    $commentManager->bannish($comment);
                }
                $this->redirectTo('/admin/comments');
            }

        }

        $this->render("admin/comment.html.twig", array('comment' => $comment, 'errors' => $errors));

    }

    public function signaledCommentsAction(){
        $this->isAuthorized();

        $commentManager = new CommentManager();
        $comments = $commentManager->findAllSignaled();

        $this->render("admin/comments_signaled.html.twig",array('comments' => $comments));
    }

    public function banishedCommentsAction(){
        $this->isAuthorized();

        $commentManager = new CommentManager();
        $comments = $commentManager->findAllBanished();

        $this->render("admin/comments_banished.html.twig",array('comments' => $comments));
    }

    public function usersAction(){
        $this->isAuthorized();
        $userManager = new UserManager();
        $listUser = $userManager->findAll();

        $this->render('admin/users.html.twig', array("listUser" => $listUser));
    }

    public function userAction($id){
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable");
        }
        $userManager = new UserManager();
        $user = $userManager->findOneById($id);
        $errors = [];
        if(!$user){
            throw new \Exception("Page introuvable");
        }
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            if(!isset($_POST['roles'])){
                $errors[] = ["message" => "Il faut au minimum un role"];
            }else{
                foreach ($_POST['roles'] as $role){
                    if($role != "ROLE_USER" && $role != "ROLE_ADMIN"){
                        $errors[] = ["message" => "LE format du(des) rôle(s) n'est pas correct!"];
                    }
                }
            }

            if(empty($errors)){
                if(in_array("ROLE_ADMIN", $_POST['roles']) && !in_array("ROLE_USER", $_POST['roles'])){
                    $_POST['roles'][] = 'ROLE_USER';
                }
                $user->setRoles($_POST['roles']);
                $userManager->update($user);
                $this->redirectTo('/admin/users');
            }
        }
        $this->render("admin/user.html.twig", array('user' => $user, 'errors' => $errors));
    }

    public function contentsAction(){
        $this->isAuthorized();
        $contentManager = new ContentManager();
        $contents = $contentManager->findAll();
        $this->render("admin/contents.html.twig", array('contents' => $contents));
    }

    public function contentAction($id){
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable!");
        }
        $contentManager = new ContentManager();
        $content = $contentManager->findById($id);
        if(!$content){
            throw new \Exception("Page introuvable");
        }

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            if(isset($_POST['content'])){
                $content->setContent(htmlspecialchars($_POST['content']));
                $contentManager->update($content);
                $this->redirectTo("/admin/contents");
            }
        }
        $this->render("admin/content.html.twig", array("content" => $content));
    }

    public function chaptersDraftAction(){
        $this->isAuthorized();

        $chapterManager = new ChapterManager();
        $chapters = $chapterManager->findAllDraft();

        if(!$chapters){
            throw new \Exception("Page Introuvable");
        }

        $this->render("admin/chapters.draft.html.twig", array('chapters' => $chapters));
    }
}