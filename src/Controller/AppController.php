<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\User;
use App\Manager\ChapitreManager;
use App\Manager\CommentaireManager;
use App\Manager\UserManager;
use App\Router\RouterException;
use App\Validator\Validator;

class AppController extends Controller
{

    public function indexAction()
    {
        session_start();
        // On vérifie si le visiteur viens pour la premier fois sur le site
        $this->session();
        var_dump($_SESSION);
        $chapitreManager = new ChapitreManager();
        $listChapitres = $chapitreManager->getAll();

        $this->render('index.html.twig', array('listChapitres' => $listChapitres));
    }

    public function chapitreAction($id)
    {
        if (!is_numeric($id)) {
            throw new RouterException("$id has to be a number");
        } else {
            $chapitreManager = new ChapitreManager();
            $commentaireManager = new CommentaireManager();

            $chapitre = $chapitreManager->getOne($id);
            $listCommentaires = $commentaireManager->getAllForAChapitre($id);
            $this->render(
                'chapitre.html.twig',
                array('chapitre' => $chapitre, 'listCommentaires' => $listCommentaires)
            );
        }
    }

    public function loginAction()
    {
        // On vérifie si la personne n'est pas déjà connecter et on supprime la session en cours
        if (isset($_SESSION)) {
            session_unset();
        }
        $erreurs = [];
        // On vérifie qe la methode est post et donc que le formulaire est passé
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // On vérifie la présence de données username et password en post
            // On va pour cela utiliser un validator
            if (isset($_POST['username']) && isset($_POST['password'])) {
                // On va vérifier les données avec un validator
                // Les regex sont gérer dans le validator
                $validator = new Validator();
                if (!$validator->isUsername($_POST['username'])) {
                    $erreurs[] = ["erreur" => "username", "message" => "Erreur sur le format du nom"];
                }
                if (!$validator->isPassword($_POST['password'])) {
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
                $password = hash("sha512", $_POST["password"]);
                $userManager = new UserManager();
                $user = $userManager->findOneByUserName($_POST["username"], $password);
                // on vérifie l'existance de l'user
                if (!$user) {
                    throw new \Exception("L'user n'existe pas ou mauvais mot de passe");
                }
                // On enregistre l'utilisateur dans une session
                $this->fillSession($user);
                // L'utilisateur étant enrgistrer on le renvoye vers la page d'accueil
                header("Location : http://blog.fr/");
                echo "<META HTTP-EQUIV='refresh' CONTENT='0;URL=http://blog.fr'>";
            }
        }
        $this->render('login.html.twig', array('erreurs' => $erreurs));
    }

    public function logoutAction()
    {
        // On vide la session et on crée une session visiteur
        session_unset();
        $this->fillSession();
        // Puis, on redirige vers la page d'accueil
        header("Locate : http://blog.fr");
    }

    public function inscriptionAction()
    {
        // On va vide la session
        session_unset();
        $erreurs[] = null;
        // On vérifie si le formulaire a bien été envoyé
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // On vérifie la presence des éléments post username, mail, password et password2
            if (isset($_POST['username']) && isset($_POST['mail']) && isset($_POST['password']) && isset($_POST['passwordconfirmation'])) {
                // Ils sont tous remplient
                // On utilise le validator
                $validator = new Validator();
                // le prénoms d'abord
                if (!$validator->isUsername($_POST['username'])) {
                    $erreurs[] = ["erreur" => "username", "message" => "Le nom n'est pas du format"];
                }
                // le mail
                if (!$validator->isMail($_POST['mail'])) {
                    $erreurs[] = ["erreur" => "mail", "message" => "Le mail n'est pas du format"];
                }
                // les mots de passe
                if ($_POST['password'] !== $_POST['passwordconfirmation']) {
                    $erreurs[] = ["erreur" => "passwords", "message" => "Les mots de passe ne sont pas identique"];
                }
                if (!$validator->isPassword($_POST['password'])) {
                    $erreurs[] = ["erreur" => "password", "message" => "Le mot de passe n'est pas du format"];
                }
            } else {
                $erreurs[] = ["erreurs" => "formulaire", "message" => "le formulaire ne peut être vide"];
            }
            // On vérifie si la  variable erreur n'est pas vide
            if (empty($erreurs)) {
                // tous est bon, on crée le user
                $user = new User(
                    [
                        "username" => htmlspecialchars($_POST['username']),
                        "mail" => htmlspecialchars($_POST['mail']),
                        "password" => htmlspecialchars($_POST['password']),
                        "roles" => ['ROLE_USER'],
                    ]
                );
                // On l'enregistre
                $userManager = new UserManager();
                $userManager->create($user);
                // On crée une session de l'utilisateur
                $this->fillSession($user);
                // On renvoye vers la page d'accueil
                header("Location : http://blog.fr");
                echo "<META HTTP-EQUIV='refresh' CONTENT='0;URL=http://blog.fr'>";
            }
        }
        // On affiche la vue du formulaire
        $this->render('connect.html.twig');
    }

    private
    function session()
    {
        // On commence par vérifie l'exisance d'une session
        if (empty($_SESSION)) {
            // On va crée une session visiteur
            $this->fillSession();
        }
    }

    private
    function fillSession(
        $user = null
    ) {
        if ($user) {

            $_SESSION['id'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['roles'] = $user->getRoles();
            $_SESSION['isconnected'] = false;
        } else {
            $_SESSION['username'] = "visiteur";
            $_SESSION['roles'] = ["ROLE_USER"];
            $_SESSION['isconnected'] = false;
        }
    }
}