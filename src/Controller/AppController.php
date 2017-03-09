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
        echo $this->render(
            'index.html.twig',
            array('listContent' => $listContent, 'chapter' => $chapter),
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
        echo $this->render('login.html.twig', array('errors' => $errors), $_SESSION);
    }

    public function logoutAction()
    {
        session_start();
        $this->fillSession();
        // Puis, on redirige vers la page d'accueil
        $this->redirectTo('/');
    }



}