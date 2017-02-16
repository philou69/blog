<?php


namespace App\Controller;


use App\Manager\ChapitreManager;
use App\Manager\UserManager;
use App\Validator\UserValidator;

class AdminController extends Controller
{
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
                if (!$userValidator->isPassword($_POST['password'])) {
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
                $user = $userManager->findOneByUserNameAndPassword($_POST["username"], $password);
                // on vérifie l'existance de l'user
                if (!$user) {
                    throw new \Exception("L'user n'existe pas ou mauvais mot de passe");
                }
                if(!in_array("ROLE_ADMIN", $user->getRoles())){
                    $erreurs[] = ['erreur' => 'formulaire', 'message' => "L'utilisateur n'a pas accès à cette zone."];
                }else{
                    // On enregistre l'utilisateur dans une session
                    $this->fillSession($user);
                    // L'utilisateur étant enrgistrer on le renvoye vers la page d'accueil
                    $this->redirectTo('/admin/');
                }

            }
        }
        $this->render('admin/login.html.twig', array('erreurs' => $erreurs), $_SESSION);
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

    public function chapitresAction(){
        $this->isAuthorized();
        $chapitreManager = new ChapitreManager();
        $listChapitre = $chapitreManager->findAll();

        $this->render('admin/chapitres.html.twig', array('listChapitre' => $listChapitre), $_SESSION);
    }
}