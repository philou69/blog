<?php

namespace App\Controller;

use App\Constraint\UserConstraint;
use App\Entity\Chapitre;
use App\Entity\Commentaire;
use App\Entity\User;
use App\Manager\ChapitreManager;
use App\Manager\CommentaireManager;
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
        $chapitreManager = new ChapitreManager();
        $chapitre = $chapitreManager->findLastPublished();
        if (!$listContent || !$chapitre) {
            throw new \Exception("Page introuvable");
        }
        $this->render(
            'index.html.twig',
            array('listContent' => $listContent, 'chapitre' => $chapitre),
            $_SESSION
        );
    }

    public function chapitresAction(){
        session_start();
        // On vérifie si le visiteur viens pour la premier fois sur le site
        $this->session();
        $chapitreManager = new ChapitreManager();
        $listChapitres = $chapitreManager->findPublished();

        if (!$listChapitres) {
            throw new \Exception("Page introuvable");
        }
        $this->render(
            'chapitres.html.twig',
            array('listChapitres' => $listChapitres),
            $_SESSION
        );
    }
    public function chapitreAction($id)
    {
        session_start();
        if (!is_numeric($id)) {
            throw new RouterException("$id has to be a number");
        }
        $chapitreManager = new ChapitreManager();
        $commentaireManager = new CommentaireManager();
        $chapitre = $chapitreManager->findOneById($id);
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            if(isset($_POST['commentaire']) && !empty($_POST['commentaire'])){
                $userManager = new UserManager();
                $user = $userManager->findOneById($_SESSION['id']);
                $commentaire = new Commentaire();
                $now = new \DateTime();
                $commentaire->setCommentaire(htmlspecialchars($_POST['commentaire']))
                    ->setChapitre($chapitre)
                    ->setUser($user);
                $commentaireManager->add($commentaire);
            }
        }
        $listCommentaires = $commentaireManager->findAllForAChapitre($id);
        // On vérifie la présence du chapitre
        // Comme les commentaires ne sont pas obligatoire, on ne vérifie pas
        if (!$chapitre) {
            throw new \Exception("Page Introuvable");
        }



        $this->render(
            'chapitre.html.twig',
            array('chapitre' => $chapitre, 'listCommentaires' => $listCommentaires),
            $_SESSION
        );
    }

    public function loginAction()
    {
        session_start();

        $erreurs = [];
        // On vérifie qe la methode est post et donc que le formulaire est passé
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // On vérifie la présence de données username et password en post
            // On va pour cela utiliser un validator
            if (isset($_POST['username']) && isset($_POST['password'])) {
                // On va vérifier les données avec un validator
                // Les regex sont gérer dans le validator
                $userValidator = new UserValidator();
                if (!$userValidator->isUsername($_POST['username'])) {
                    $erreurs[] = ["erreur" => "username", "message" => "Erreur sur le format du nom"];
                }
                if (!$userValidator->isPassword(htmlspecialchars($_POST['password']))) {
                    $erreurs[] = ["erreur" => "password", "message" => "Erreur sur le format du mot de passe"];
                }
            } else {
                $erreurs[] = ["erreur" => "formulaire", "message" => "Il faut un nom et un mot de passe!"];
            }
            // Si $erreurs est vide, cela signifie que tous est ok
            // Et on connect l'utilisateur
            if (empty($erreurs)) {
                // On va chercher un utilisateur correspondant au username et password
                // Il n'est pas utile de protege les POST car password est  hashé et username est protéger automatiquement dans la requête
                $password = hash("sha512", htmlspecialchars($_POST["password"]));
                $userManager = new UserManager();
                $user = $userManager->findOneByUserNameAndPassword($_POST["username"], $password);
                // on vérifie l'existance de l'user
                if (!$user) {
                    throw new \Exception("L'user n'existe pas ou mauvais mot de passe");
                }
                // On enregistre l'utilisateur dans une session
                $this->fillSession($user);
                // L'utilisateur étant enrgistrer on le renvoye vers la page d'accueil
                header("Location : /");
                echo "<META HTTP-EQUIV='refresh' CONTENT='0;URL=/'>";
            }
        }
        $this->render('login.html.twig', array('erreurs' => $erreurs), $_SESSION);
    }

    public function logoutAction()
    {
        session_start();
        $this->fillSession();
        // Puis, on redirige vers la page d'accueil
        header("Locate : http://blog.fr");
        echo "<META HTTP-EQUIV='refresh' CONTENT='0;URL=http://blog.fr'>";
    }

    public function inscriptionAction()
    {
        session_start();
        $erreurs = [];
        // On vérifie si le formulaire a bien été envoyé
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // On vérifie la presence des éléments post username, mail, password et password2
            if (isset($_POST['username']) && isset($_POST['mail']) && isset($_POST['password']) && isset($_POST['passwordConfirmation'])) {

                // Ils sont tous remplient
                // On utilise le userValidator
                $userValidator = new UserValidator();
                // le prénoms d'abord
                if (!$userValidator->isUsername($_POST['username'])) {
                    $erreurs[] = ["erreur" => "username", "message" => "Le nom n'est pas du format"];
                }
                // le mail
                if (!$userValidator->isMail($_POST['mail'])) {
                    $erreurs[] = ["erreur" => "mail", "message" => "Le mail n'est pas du format"];
                }
                // les mots de passe
                if ($_POST['password'] !== $_POST['passwordConfirmation']) {
                    $erreurs[] = ["erreur" => "passwords", "message" => "Les mots de passe ne sont pas identique"];
                }
                if (!$userValidator->isPassword($_POST['password'])) {
                    $erreurs[] = ["erreur" => "password", "message" => "Le mot de passe n'est pas du format"];
                }
                // On va s'assurer que l'username et mail n'est pas déjà utilise
                $user = new User(
                    [
                        "username" => htmlspecialchars($_POST['username']),
                        "mail" => htmlspecialchars($_POST['mail']),
                        "password" => hash("sha512", $_POST['password']),
                        "roles" => ['ROLE_USER'],
                    ]
                );
                $userConstraint = new UserConstraint($user);
                if (!$userConstraint->isNotOtherUser()) {
                    $erreurs[] = [
                        "erreur" => "user",
                        "message" => "Il existe déjà un visiteur avec ce nom ou cet mail",
                    ];

                }
            } else {
                $erreurs[] = ["erreur" => "formulaire", "message" => "le formulaire ne peut être vide"];
            }
            // On vérifie si la  variable erreur n'est pas vide
            if (empty($erreurs)) {

                // On l'enregistre
                $userManager = new UserManager();
                $user = $userManager->create($user);
                $this->fillSession($user);
                // On renvoye vers la page d'accueil
                header("Location : http://blog.fr");
                echo "<META HTTP-EQUIV='refresh' CONTENT='0;URL=http://blog.fr'>";
            }
        }
        // On affiche la vue du formulaire
        $this->render('connect.html.twig', array("erreurs" => $erreurs), $_SESSION);
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
        $erreurs = [];

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // On crée un tableau d'erreurs et un vérificateur
            $userValidator = new UserValidator();
            if (isset($_POST['username'])) {
                //username n'est pas vide.
                if (!$userValidator->isUsername($_POST['username'])) {
                    // ce n'est pas un username, on remplie le tableau
                    $erreurs[] = ["erreur" => "username", "message" => "Le nom n'a pas un bon format"];
                }
                if (!$userConstraint->isNotOtherUserName($_POST['username']) && $user->getUsername(
                    ) != $_POST['username']
                ) {
                    $erreurs[] = ["erreur" => "formulaire", "message" => "Modification 1 impossible"];
                }
                $user->setUsername(htmlspecialchars($_POST['username']));
            }
            if (isset($_POST['mail'])) {
                // mail n'est pas vide.
                if (!$userValidator->isMail($_POST['mail'])) {
                    $erreurs[] = ["erreur" => "mail", "message" => "Le mail n'est pas au bon format!"];
                }
                if (!$userConstraint->isNotOtherMail($_POST['mail']) && $user->getMail() != $_POST['mail']) {
                    $erreurs[] = ["erreur" => "formulaire", "message" => "Modification 2 impossible"];
                }
                $user->setMail(htmlspecialchars($_POST['mail']));
            }
            if (!empty($_POST['password']) && !empty($_POST['password_confirmation'])) {
                // password et password_confirmation ne sont pas vides
                if ($_POST['password'] != $_POST['password_confirmation']) {
                    // Les mots de passe ne sont pas identique
                    $erreurs[] = ["erreur" => "password", "message" => "Les mots de passe ne sont pas identiques"];
                } else {
                    // Les mots sont identiques
                    if (!$userValidator->isPassword($_POST['password'])) {
                        $erreurs[] = ["erreur" => "password", "message" => "Le mot de passe n'est pas au bon format!"];
                    }
                    $user->setPassword(hash("sha512", $_POST['password']));
                }
            }
            // Si le tableau est vide on update le user
            if (empty($erreurs)) {
                $userManager->update($user);
                $success = "Vos modifications ont bien été enregistrer !";
            }

        }
        // Si la méthode est post, on vérifie les données du formulaire

        $this->render(
            'profil.html.twig',
            array('user' => $user, 'erreurs' => $erreurs, 'success' => $success),
            $_SESSION
        );
    }

    public function signalAction($id)
    {
        if (!is_numeric($id)) {
            throw new \Exception("Page introuvable!");
        }
        $commentaireManager = new CommentaireManager();
        $commentaire = $commentaireManager->findOneById($id);
        if ($commentaire == false) {
            throw new \Exception("Page Introuvable");
        }

        $commentaire->setSignaled(true);
        $commentaireManager->signaled($commentaire);
        $idChapitre = $commentaire->getChapitre()->getId();
        $this->redirectTo("/chapitre/$idChapitre");
    }

    public function responseAction($id)
    {
        if (!is_numeric($id)) {
            throw new \Exception("Page Introuvable");
        }
        $commentaireManager = new CommentaireManager();
        $commentaire = $commentaireManager->findOneById($id);
        if (!$commentaire) {
            throw new \Exception("Page Introuvalbe");
        }
        session_start();
        if (isset($_POST['response']) && !empty($_POST['response'])) {
            // on crée un commentaire qui contiendra la réponse
            $new_commentaire = new Commentaire();
            $text = null;
            $username = $commentaire->getUser()->getUsername();
            // On regarde la place du commentaire
            if ($commentaire->getPlace() == 3) {
                // Si elle vaut 3, on rajoute @username au message
                $text = "@".$username." ".htmlspecialchars($_POST['response']);
                // On passe à la reponse le commentaire parent  en parentet la place 3
                $new_commentaire->setCommentaireParent($commentaire->getCommentaireParent())
                    ->setPlace(3);
            }else{
                // autrement, on prends simplement le message
                $text = htmlspecialchars($_POST['response']);
                // On passe le commentaire en parent et la place du commentaire plus 1
                $new_commentaire->setCommentaireParent($commentaire)
                    ->setPlace($commentaire->getPlace()+1);

            }
            $user = new User(array('id' => $_SESSION['id']));


            $new_commentaire->setCommentaire($text)
                ->setUser($user)
                ->setChapitre($commentaire->getChapitre());
            $commentaireManager->add($new_commentaire);

            $this->redirectTo("/chapitre/".$commentaire->getChapitre()->getId());
        }
    }

}