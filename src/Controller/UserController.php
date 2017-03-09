<?php


namespace App\Controller;


use App\Constraint\UserConstraint;
use App\Entity\User;
use App\Mailer\Mailer;
use App\Manager\UserManager;
use App\Validator\UserValidator;

class UserController extends AdminController
{
    public function usersAction(){
        $this->isAuthorized();
        $userManager = new UserManager();
        $users = $userManager->findAll();

        echo $this->render('admin/users.html.twig', array("users" => $users));
    }

    public function usersBanishedAction(){
        $this->isAuthorized();

        $userManager = new UserManager();
        $users = $userManager->findAllBanish();

        echo $this->render('admin/users.html.twig', array('users' => $users));
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
            $status = htmlspecialchars($_POST['status']);
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
        echo $this->render('connect.html.twig', array("errors" => $errors), $_SESSION);
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
            if (!empty($password) && !empty($passwordConfirmation)) {
                // password et password_confirmation ne sont pas vides
                if ($password != $passwordConfirmation) {
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

        echo $this->render(
            'profil.html.twig',
            array('user' => $user, 'errors' => $errors, 'success' => $success),
            $_SESSION
        );
    }

    public function logoutAdminAction(){
        $this->isAuthorized();

        session_unset();
        $this->redirectTo('/');
    }

    public function loginAdminAction()
    {
        session_start();

        $errors = [];
        // On vérifie qe la methode est post et donc que le formulaire est passé
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);
            // On vérifie la présence de données username et password en post
            // On va pour cela utiliser un validator
            if (isset($username) && isset($password)) {
                // On va vérifier les données avec un validator
                // Les regex sont gérer dans le validator
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

    public function resetAction(){
        $errors = [];
        $infos = [];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $email = htmlspecialchars($_POST['email']);
            if(isset($email)){
                $userValidator = new UserValidator();
                if(!$userValidator->isMail($email)){
                    $errors['message'] = "Email au mauvais format";
                }else{
                    $userManager = new UserManager();
                    $user = $userManager->getByMail($email);
                    if($user){
                        $password = uniqid();
                        $view = $this->render('email.password.html.twig', array('user' => $user, 'password' => $password));
                        $mailer = new Mailer();
                        $mailer->sendMail($user, $password, $view);
                        $succes['messages'] = "Un email a été envoyer à l'adresse mail";
                        $userManager->update($user);

                    }
                    $infos[] = "Nous avons bien enregistrez votre demande.<br/> Si l'adresse mail $email correspond à un visiteur, un mail sera envoyer à cette adresse !";
                }
            }
        }

        echo $this->render('reset.password.html.twig', array('errors' => $errors, 'infos' => $infos));
    }
}