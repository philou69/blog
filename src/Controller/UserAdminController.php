<?php


namespace App\Controller;


use App\Manager\UserManager;
use App\Validator\UserValidator;

class UserAdminController extends AdminController
{
    /*
     * liste des tous les visiteurs
     */
    public function usersAction(){
        $this->isAuthorized();
        $userManager = new UserManager();
        $users = $userManager->findAll();

        echo $this->render('admin/users.html.twig', array("users" => $users));
    }

    /*
     * liste des visiteurs banis
     */
    public function usersBanishedAction(){
        $this->isAuthorized();

        $userManager = new UserManager();
        $users = $userManager->findAllBanish();

        echo $this->render('admin/users.html.twig', array('users' => $users));
    }

    /*
     * Page d'edit d'un visiteur
     */
    public function userAction($id){
        $this->isAuthorized();
        // Vérification de l'id et de l'existence d'un user
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable");
        }
        $userManager = new UserManager();
        $user = $userManager->findOneById($id);
        if(!$user){
            throw new \Exception("Page introuvable");
        }
        // Tableau des erreurs
        $errors = [];
        // Validation du formulaire
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $status = htmlspecialchars($_POST['status']);
            // Vérification des roles
            if(!isset($_POST['roles'])){
                $errors[] = ["message" => "Il faut au minimum un role"];
            }else{
                foreach ($_POST['roles'] as $role){
                    if($role != "ROLE_USER" ){
                        if($role != "ROLE_ADMIN") {
                            $errors[] = ["message" => "Le format du(des) rôle(s) n'est pas correct!"];
                        }
                    }
                }
            }
            if(isset($status)){
                $status = $status == 'true' ? true : false ;
            }

            if(empty($errors)){
                // On s'assure que si role_admin est present, role_user est présent
                if(in_array("ROLE_ADMIN", $_POST['roles']) && !in_array("ROLE_USER", $_POST['roles'])){
                    $_POST['roles'][] = 'ROLE_USER';
                }
                $user->setRoles($_POST['roles'])
                    ->setBanish($status);
                $userManager->update($user);
                $this->redirectTo('/admin/users');
            }
        }
        echo $this->render("admin/user.html.twig", array('user' => $user, 'errors' => $errors), $_SESSION);
    }

    /*
     * Connection à la zone admin
     */
    public function loginAction()
    {
        session_start();
        // Tableau
        $errors = [];
        // On vérifie qe la methode est post et donc que le formulaire est passé
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $password = htmlspecialchars($_POST['password']);
            // On vérifie la présence de données username et password en post
            // On va pour cela utiliser un validator
            if (isset($pseudo) && isset($password)) {
                // On va vérifier les données avec un validator
                // Les regex sont gérer dans le validator
                $userValidator = new UserValidator();
                if (!$userValidator->isUsername($pseudo)) {
                    $errors[] = ["error" => "pseudo", "message" => "Ce pseudo n'est pas valide"];
                }
                if (!$userValidator->isPassword($password)) {
                    $errors[] = ["error" => "password", "message" => "Ce mot de passe n'est pas valide"];
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
                $user = $userManager->findOneByPseudoAndPassword($pseudo, $password);
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

        echo $this->render('admin/login.html.twig', array('errors' => $errors), $_SESSION);
    }

    /*
     * Deconnection de la zone admin
     */
    public function logoutAction(){
        $this->isAuthorized();

        session_unset();
        $this->redirectTo('/');
    }

}