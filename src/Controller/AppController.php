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
    public function indexAction()
    {
        session_start();
        // On vérifie si le visiteur viens pour la premier fois sur le site
        $this->session();
        $listContent = $this->contentManager->findAllPerPage("index");
        $chapterManager = new ChapterManager();
        $chapter = $chapterManager->findLastPublished();
        if (!$listContent || !$chapter) {
            throw new \Exception("Page introuvable");
        }
        $this->render(
            'index.html.twig',
            array('listContent' => $listContent, 'chapter' => $chapter),
            $_SESSION
        );
    }

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
                $username = htmlspecialchars($_POST['username']);
                $password = htmlspecialchars($_POST['password']);
                $userValidator = new UserValidator();
                if (!$userValidator->isUsername($username)) {
                    $errors[] = ["error" => "username", "message" => "Erreur sur le format du nom"];
                }
                if (!$userValidator->isPassword($password)) {
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
                $password = hash("sha512", $password);
                $userManager = new UserManager();
                $user = $userManager->findOneByUserNameAndPassword($username, $password);
                // on vérifie l'existance de l'user
                if (!$user) {
                    throw new \Exception("L'user n'existe pas ou mauvais mot de passe");
                }
                // On enregistre l'utilisateur dans une session
                $this->fillSession($user);
                // L'utilisateur étant enrgistrer on le renvoye vers la page d'accueil
                $this->redirectTo('/');
            }
        }
        $this->render('login.html.twig', array('errors' => $errors), $_SESSION);
    }

    public function logoutAction()
    {
        session_start();
        $this->fillSession();
        // Puis, on redirige vers la page d'accueil
        $this->redirectTo('/');
    }

    public function inscriptionAction()
    {
        session_start();
        $errors = [];
        // On vérifie si le formulaire a bien été envoyé
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $username = htmlspecialchars($_POST['username']);
            $mail = htmlspecialchars($_POST['mail']);
            $password = htmlspecialchars($_POST['password']);
            $passwordConfirmation = htmlspecialchars($_POST['passwordConfirmation']);

            // On vérifie la presence des éléments post username, mail, password et passwordConfirmation
            if (isset($username) && isset($mail) && isset($password) && isset($passwordConfirmation)) {
                // Ils sont tous remplient
                // On utilise le userValidator
                $userValidator = new UserValidator();
                // le prénoms d'abord
                if (!$userValidator->isUsername($username)) {
                    $errors[] = ["error" => "username", "message" => "Le nom n'est pas du format"];
                }
                // le mail
                if (!$userValidator->isMail($mail)) {
                    $errors[] = ["error" => "mail", "message" => "Le mail n'est pas du format"];
                }
                // les mots de passe
                if ($password !== $passwordConfirmation) {
                    $errors[] = ["error" => "passwords", "message" => "Les mots de passe ne sont pas identique"];
                }
                if (!$userValidator->isPassword($password)) {
                    $errors[] = ["error" => "password", "message" => "Le mot de passe n'est pas du format"];
                }
                // On va s'assurer que l'username et mail n'est pas déjà utilise
                $user = new User();
                $user->setUsername($username)
                    ->setMail($mail)
                    ->setPassword(hash("sha512", $password))
                    ->setRoles(['ROLE_USER']);

                $userConstraint = new UserConstraint($user);

                if (!$userConstraint->isNotOtherUser()) {
                    $errors[] = [
                        "error" => "user",
                        "message" => "Il existe déjà un visiteur avec ce nom ou cet mail",
                    ];

                }
            } else {
                $errors[] = ["error" => "formulaire", "message" => "le formulaire ne peut être vide"];
            }
            // On vérifie si la  variable error n'est pas vide
            if (empty($errors)) {

                // On l'enregistre
                $userManager = new UserManager();
                $user = $userManager->create($user);
                $this->fillSession($user);
                // On renvoye vers la page d'accueil
                $this->redirectTo('/');
            }
        }
        // On affiche la vue du formulaire
        $this->render('connect.html.twig', array("errors" => $errors), $_SESSION);
    }

    public function profilAction()
    {
        session_start();
        if (!$_SESSION['isconnected'] || empty($_SESSION['id'])) {
            throw new \Exception("Accès interdit");
        }
        $success = null;
        $userManager = new  UserManager();
        $userConstraint = new UserConstraint();
        $user = $userManager->findOneById($_SESSION['id']);
        // Si le visiteur n'est pas connecter, on leve une exeption
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // On crée un tableau d'errors et un vérificateur
            $username = htmlspecialchars($_POST['username']);
            $mail = htmlspecialchars($_POST['mail']);
            $password = htmlspecialchars($_POST['password']);
            $passwordConfirmation = htmlspecialchars($_POST['passwordConfirmation']);
            $userValidator = new UserValidator();
            if (isset($username)) {
                //username n'est pas vide.
                if (!$userValidator->isUsername($username)) {
                    // ce n'est pas un username, on remplie le tableau
                    $errors[] = ["error" => "username", "message" => "Le nom n'a pas un bon format"];
                }
                if (!$userConstraint->isNotOtherUserName($username) && $user->getUsername(
                    ) != $username ) {
                    $errors[] = ["error" => "formulaire", "message" => "Modification 1 impossible"];
                }
                $user->setUsername($username);
            }
            if (isset($mail)) {
                // mail n'est pas vide.
                if (!$userValidator->isMail($mail)) {
                    $errors[] = ["error" => "mail", "message" => "Le mail n'est pas au bon format!"];
                }
                if (!$userConstraint->isNotOtherMail($mail) && $user->getMail() != $mail) {
                    $errors[] = ["error" => "formulaire", "message" => "Modification 2 impossible"];
                }
                $user->setMail($mail);
            }
            if (!empty($password) && !empty($password_confirmation)) {
                // password et password_confirmation ne sont pas vides
                if ($password != $password_confirmation) {
                    // Les mots de passe ne sont pas identique
                    $errors[] = ["error" => "password", "message" => "Les mots de passe ne sont pas identiques"];
                } else {
                    // Les mots sont identiques
                    if (!$userValidator->isPassword($password)) {
                        $errors[] = ["error" => "password", "message" => "Le mot de passe n'est pas au bon format!"];
                    }
                    $user->setPassword(hash("sha512", $password));
                }
            }
            // Si le tableau est vide on update le user
            if (empty($errors)) {
                $userManager->update($user);
                $success = "Vos modifications ont bien été enregistrer !";
            }

        }
        // Si la méthode est post, on vérifie les données du formulaire

        $this->render(
            'profil.html.twig',
            array('user' => $user, 'errors' => $errors, 'success' => $success),
            $_SESSION
        );
    }

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
        var_dump($comment);
        if ($comment == false) {
            throw new \Exception("Page Introuvable");
        }
        $now = new \DateTime();
        $user = new User();
        $user->setId($_SESSION['id']);
        $comment->setSignaled(true)
            ->setSignaledBy($user)
            ->setSignaledAt($now);
        $commentManager->signaled($comment);
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

}