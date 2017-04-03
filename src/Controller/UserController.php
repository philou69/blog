<?php


namespace App\Controller;


use App\Constraint\UserConstraint;
use App\Entity\User;
use App\Mailer\Mailer;
use App\Manager\UserManager;
use App\Validator\UserValidator;

class UserController extends AdminController
{

    public function inscriptionAction()
    {
        session_start();

        if (isset($_SERVER['HTTP_REFERER'])) {
            $route = $_SERVER['HTTP_REFERER'];
            if ($route != $this->url.'/inscription') {
                $_SESSION['route'] = $route;
            }
        }

        $errors = [];
        // On vérifie si le formulaire a bien été envoyé
        $user = new User();
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $firstname = htmlspecialchars($_POST['firstname']);
            $username = htmlspecialchars($_POST['username']);
            $mail = htmlspecialchars($_POST['mail']);
            $password = htmlspecialchars($_POST['password']);
            $passwordConfirmation = htmlspecialchars($_POST['passwordConfirmation']);

            // On vérifie la presence des éléments post username, mail, password et passwordConfirmation
            if (isset($pseudo) && isset($firstname) && isset($username) && isset($mail) && isset($password) && isset($passwordConfirmation)) {
                // Ils sont tous remplient
                // On utilise le userValidator
                $userValidator = new UserValidator();
                if (!$userValidator->isUsername($pseudo)) {
                    $errors[] = [
                        'error' => 'pseudo',
                        'message' => "Ce pseudo n'est pas valide !<br/> Il ne doit contenir que des caractères alphanumériques.",
                    ];
                }
                // le prénoms d'abord
                if (!$userValidator->isUsername($firstname)) {
                    $errors[] = [
                        "error" => "firstname",
                        "message" => "Ce prénom n'est pas valide !<br/> Il ne doit contenir que des caractères alphabétiques",
                    ];
                }
                if (!$userValidator->isUsername($username)) {
                    $errors[] = [
                        "error" => "username",
                        "message" => "Ce nom n'est pas valide !<br/> Il ne doit contenir que des caractères alphabétiques",
                    ];
                }
                // le mail
                if (!$userValidator->isMail($mail)) {
                    $errors[] = [
                        "error" => "mail",
                        "message" => "Cette adresse mail n'est pas valide !<br> Le format doit être xxx@xxx.xxx",
                    ];
                }
                // les mots de passe
                if ($password !== $passwordConfirmation) {
                    $errors[] = ["error" => "passwords", "message" => "Les mots de passe ne sont pas identiques !"];
                } elseif (!$userValidator->isPassword($password)) {
                    $errors[] = [
                        "error" => "passwords",
                        "message" => "Ce mot de passe n'est pas valide !<br> Il peut être constitué de caractères alphanumérique et des caractères spéciaux suivant éèêùàîôûç@+*&-",
                    ];
                }
                // On va s'assurer que l'username et mail n'est pas déjà utilisé

                $user->setPseudo($pseudo)
                    ->setUsername($username)
                    ->setFirstname($firstname)
                    ->setMail($mail)
                    ->setPassword(hash("sha512", $password))
                    ->setRoles(['ROLE_USER']);
                $userConstraint = new UserConstraint($user);

                if (!$userConstraint->isNotOtherUser()) {
                    $errors[] = [
                        "error" => "user",
                        "message" => "Il existe déjà un visiteur avec ce prénom ou ce mail",
                    ];

                }
            } else {
                $errors[] = ["error" => "formulaire", "message" => "le formulaire ne peut être vide"];
            }
            // On vérifie si la  variable error n'est pas vide
            if (empty($errors)) {
                if (isset($_SESSION['route'])) {
                    $route = $_SESSION['route'];
                } else {
                    $route = '/';
                }
                // On l'enregistre
                $userManager = new UserManager();
                $user = $userManager->create($user);
                // On crée la session
                $this->fillSession($user);
                // On renvoie vers la page d'accueil
                $this->redirectTo($route);
            }
        }

        // On affiche la vue du formulaire
        echo $this->render('connect.html.twig', array("errors" => $errors, 'user' => $user), $_SESSION);
    }

    public function profilAction()
    {
        session_start();
        if (!$_SESSION['isconnected'] || empty($_SESSION['id'])) {
            throw new \Exception("Accès interdit");
        }
        $success = null;
        $userManager = new  UserManager();
        $userValidator = new UserValidator();
        $userConstraint = new UserConstraint();
        $user = $userManager->findOneById($_SESSION['id']);
        $oldUser = clone $user;
        // Si le visiteur n'est pas connecter, on leve une exeption
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // On crée un tableau d'errors et un vérificateur
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $username = htmlspecialchars($_POST['username']);
            $firstname = htmlspecialchars($_POST['firstname']);
            $mail = htmlspecialchars($_POST['mail']);
            $oldPassword = htmlspecialchars($_POST['oldPassword']);
            $password = htmlspecialchars($_POST['password']);
            $passwordConfirmation = htmlspecialchars($_POST['passwordConfirmation']);
            if (empty($oldPassword)) {
                $errors[] = ['error' => 'formulaire', 'message' => "L'ancien mot de passe ne peut être vide"];
            } else {
                if (isset($pseudo)) {
                    if (!$userValidator->isUsername($pseudo)) {
                        $errors[] = ['error' => 'pseudo', 'message' => 'Le pseudo n\'est pas au bon format'];
                    }
                }
                if (isset($username)) {
                    //username n'est pas vide.
                    if (!$userValidator->isUsername($username)) {
                        // ce n'est pas un username, on remplie le tableau
                        $errors[] = ["error" => "username", "message" => "Le nom n'a pas un bon format"];
                    }
                }
                if (isset($firstname)) {
                    //username n'est pas vide.
                    if (!$userValidator->isUsername($firstname)) {
                        // ce n'est pas un username, on remplie le tableau
                        $errors[] = ["error" => "username", "message" => "Le nom n'a pas un bon format"];
                    }
                }
                if (isset($mail)) {
                    // mail n'est pas vide.
                    if (!$userValidator->isMail($mail)) {
                        $errors[] = ["error" => "mail", "message" => "Le mail n'est pas au bon format!"];
                    }
                }
                if (!empty($password) && !empty($passwordConfirmation)) {
                    // password et password_confirmation ne sont pas vides
                    if ($password != $passwordConfirmation) {
                        // Les mots de passe ne sont pas identique
                        $errors[] = ["error" => "password", "message" => "Les mots de passe ne sont pas identiques"];
                    } else {
                        // Les mots sont identiques
                        if (!$userValidator->isPassword($password)) {
                            $errors[] = [
                                "error" => "password",
                                "message" => "Le mot de passe n'est pas valide !",
                            ];
                        }
                    }
                }
                if (hash('sha512', $oldPassword) == $user->getPassword()) {
                    if ($user->getPseudo() != $pseudo) {
                        $user->setPseudo($pseudo);
                        if (!$userConstraint->isNotOtherPseudo($pseudo)) {
                            $errors[] = [
                                'error' => 'formulaire',
                                'message' => "Vos modifications n'ont pas été enregistrer !",
                            ];
                        }
                    }
                    if ($user->getUsername() != $username) {
                        $user->setUsername($username);

                    }
                    if ($user->getFirstname() != $firstname) {
                        $user->setFirstname($firstname);

                    }
                    if ($user->getMail() != $mail) {
                        $user->setMail($mail);
                        if (!$userConstraint->isNotOtherMail($mail)) {
                            $errors[] = [
                                'error' => 'formulaire',
                                'message' => "Vos modifications n'ont pas été enregistrer !",
                            ];
                        }
                    }
                    if ($user->getPassword() != hash("sha512", $password)) {
                        $user->setPassword(hash("sha512", $password));

                    }
                } else {
                    $errors[] = [
                        'error' => 'formulaire',
                        'message' => "Les modifications n'ont pu être enregistrées !",
                    ];
                }


            }// Si le tableau est vide on update le user
            if (empty($errors)) {
                $userManager->update($user);
                $success = "Vos modifications ont bien été enregistrer !";
            } else {
                $user = $oldUser;
            }


        }
        // Si la méthode est post, on vérifie les données du formulaire

        echo $this->render(
            'profil.html.twig',
            array('user' => $user, 'errors' => $errors, 'success' => $success),
            $_SESSION
        );
    }


    public function loginAction()
    {
        session_start();
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != $this->url.'/login' && $_SERVER['HTTP_REFERER'] != $this->url.'/user/reset') {
            $_SESSION['route'] = $_SERVER['HTTP_REFERER'];
        }
        $infos = [];
        if (isset($_SESSION['success'])) {
            $infos[] = $_SESSION['success'];
            unset($_SESSION['success']);
        }
        $errors = [];
        // On vérifie qe la methode est post et donc que le formulaire est passé
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // On vérifie la présence de données username et password en post
            // On va pour cela utiliser un validator
            if (isset($_POST['pseudo']) && isset($_POST['password'])) {
                // On va vérifier les données avec un validator
                // Les regex sont gérer dans le validator
                $pseudo = htmlspecialchars($_POST['pseudo']);
                $password = htmlspecialchars($_POST['password']);
                $userValidator = new UserValidator();
                if (!$userValidator->isUsername($pseudo)) {
                    $errors[] = ["error" => "pseudo", "message" => "Le pseudo n'est pas valide !"];
                }
                if (!$userValidator->isPassword($password)) {
                    $errors[] = ["error" => "password", "message" => "Le mot de passe n'est pas valide !"];
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
                    $errors[] = ["error" => "formulaire", 'message' => "L'user n'existe pas ou mauvais mot de passe"];
                } elseif ($user->isBanish()) {
                    return $this->redirectTo('/');
                }
                if (empty($errors)) {
                    if (isset($_SESSION['route'])) {
                        $route = $_SESSION['route'];
                    } else {
                        $route = '/';
                    }
                    // On enregistre l'utilisateur dans une session
                    $this->fillSession($user);
                    // L'utilisateur étant enrgistrer on le renvoye vers la page d'accueil

                    $this->redirectTo($route);

                }

            }
        }

        echo $this->render('login.html.twig', array('errors' => $errors, 'infos' => $infos), $_SESSION);
    }

    public function logoutAction()
    {
        session_start();
        $this->fillSession();
        // Puis, on redirige vers la page d'accueil
        $this->redirectTo('/');
    }

    public function resetAction()
    {
        session_start();
        $errors = [];
        $infos = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = htmlspecialchars($_POST['email']);
            if (isset($email)) {
                $userValidator = new UserValidator();
                if (!$userValidator->isMail($email)) {
                    $errors['message'] = "Ce mail n'est pas valide !";
                } else {
                    $userManager = new UserManager();
                    $user = $userManager->findOneByMail($email);
                    if ($user) {
                        $password = uniqid();
                        $view = $this->render(
                            'email.password.html.twig',
                            array('user' => $user, 'password' => $password)
                        );
                        $mailer = new Mailer();
                        $mailer->sendMail($user, $password, $view);
                        $succes['messages'] = "Un email a été envoyé à l'adresse mail";
                        $user->setPassword(hash('sha512', $password));
                        $userManager->update($user);

                    }
                    $infos[] = "Nous avons bien enregistré votre demande.<br/> Si l'adresse mail $email correspond à un visiteur, un mail sera envoyé à cette adresse !<br/>Consultez-le !";
                    $_SESSION['success'] = $infos;
                    $this->redirectTo('/login');
                }
            }
        }

        echo $this->render('reset.password.html.twig', array('errors' => $errors));
    }
}