<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\User;
use App\Manager\ChapitreManager;
use App\Manager\CommentaireManager;
use App\Manager\UserManager;
use App\Router\RouterException;

class AppController extends Controller
{

    public function indexAction()
    {
        $this->session();
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
            $this->render('chapitre.html.twig', array('chapitre' => $chapitre, 'listCommentaires' => $listCommentaires));
        }
    }

    public function loginAction()
    {
        // On vérifie si la personne n'est pas déjà connecter et on supprime la session en cours
        if (isset($_SESSION)) {
            session_destroy();
        }
        // On vérifie la présence de données username et password en post
        if (isset($_POST['username']) && isset($_POST['password'])) {
            // on vérifie leurs formes par regex
            // Username doit etre des lettres et/ou chiffres de 2 à 25 caractères
            // Password doit être des lettres, chiffres, @, /, *, - ou + de 3 à 25 caractères
            if (preg_match("^[a-zA-Z0-9éèàùêâûîô_-]{2,25}$", $_POST['username'])) {
                if (preg_match("^[a-zA-Z0-9@+-*/&]{3,25}$", $_POST['password'])) {
                    // On va chercher un utilisateur correspondant au username et password
                    // Il n'est pas utile de protege les POST car password est  hashé et username est protéger automatiquement dans la requête
                    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    $userManager = new UserManager();
                    $user = $userManager->findOneByUserName($_POST["username"], $password);
                    // on vérifie l'existance de l'user
                    if (!$user) {
                        throw new \Exception("L'user n'existe pas ou mauvais mot de passe");
                    }
                    // On crée un session
                    session_start();
                    $_SESSION['id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['mail'] = $user->getMail();
                    $_SESSION['roles'] = $user->getMail();

                    // L'utilisateur étant enrgistrer on le renvoye vers la page d'accueil
                    header("Location : http://blog.fr");
                } else {
                    throw new \Exception("Erreur sur le format du mot de passe");
                }
            } else {
                throw new \Exception("Erreur sur le format du nom");
            }
        } else {
            throw new \Exception("Il faut un nom et un mot de passe!");
        }
    }

    public function logoutAction()
    {
        // on vérifie la presence d'une session  et d'un champ email qui confirme que la session est à un visiteur enregistrez
        if (isset($_SESSION) && isset($_SESSION['mail'])) {
            // On vide la session
            session_unset();
            // Puis on crée une session visiteur
            $this->createSession();
        }
        // Dans tous les cas, on redirige vers la page d'accueil
        header("Locate : http://blog.fr");
    }

    public function inscriptionAction()
    {
        $erreurs[] = null;
        // On vérifie la presence des éléments post username, mail, password et password2
        if (isset($_POST['username']) && isset($_POST['mail']) && isset($_POST['password']) && isset($_POST['password2'])) {
            // Ils sont tous remplient
            // On les vérifie par regex
            // le prénoms d'abord
            if(!preg_match("^[a-zA-Z0-9éèàùêâûîô-_]{2,25}$", $_POST['username'])){
                $erreurs[] = ["username" => "Le nom n'est pas du format"];
            }
            // le mail
            if(!preg_match("^[a-z0-9._-]+@[a-z0-9_-]{2,}\.[a-z]{2,4}$", $_POST['mail'])){
                $erreurs[] = ["mail" => "Le mail n'est pas du format"];
            }
            // les mots de passe
            if($_POST['password'] !== $_POST['password2']){
                $erreurs[] = ["passwords" => "Les mots de passe ne sont pas identique"];
            }
            if(!preg_match("^[a-z0-9éèà@./*-+_&]{3,25}$", $_POST['password'])){
                $erreurs[] = ["password" => "Le mot de passe n'est pas du format"];
            }
            // On vérifie si la  variable erreur est vide
            if($erreurs != null){
                // On affiche la vue du formulaire
                require "../Resources/views/create.php";
            }
            // tous est bon, on crée le user
            $user = new User(["username" => htmlspecialchars($_POST['username']),
                "mail" => htmlspecialchars($_POST['mail']),
                "password" => htmlspecialchars($_POST['password']),
                "roles" =>['ROLE_USER']
            ]);
            // On l'enregistre
            $userManagetr = new UserManager();
            $userManagetr->create($user);
            session_start();
            $_SESSION['id'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['roles'] = $user->getRoles();
            $_SESSION['isconnected'] = false;
            // On renvoye vers la page d'accueil
            header("Location : http://blog.fr");
        }else {
            // On affiche la vue du formulaire
            require "../Resources/views/create.php";
        }

    }

    private function session()
    {
        // On commence par vérifie l'exisance d'une session
        if (!isset($_SESSION)) {
            // On va crée une session visiteur
            session_start();
            $this->createSession();

            return true;
        } else {
            return false;
        }
    }

    private function createSession()
    {
        $_SESSION['username'] = "visiteur";
        $_SESSION['roles'] = ["ROLE_USER"];
        $_SESSION['isconnected'] = false;
    }
}