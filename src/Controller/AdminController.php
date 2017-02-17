<?php


namespace App\Controller;


use App\Entity\Chapitre;
use App\Manager\ChapitreManager;
use App\Manager\CommentaireManager;
use App\Manager\UserManager;
use App\Validator\ChapitreValidator;
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

    public function addChapitreAction(){
        $this->isAuthorized();
        $errors = [];
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $chapitreValidator = new  ChapitreValidator();
            if(empty(htmlspecialchars($_POST['title']))){
                $errors[] = ['error' => "title", "message" => "Le titre ne peut être vide"];
            }else if(!$chapitreValidator->isTitle(htmlspecialchars($_POST['title']))){
                $errors[] = ['error' => "title", "message" => "Le titre n'est pas au bon format!"];
            }
            if(empty(htmlspecialchars($_POST['chapitre']))){
                $errors[] = ['error' => "chapitre", "message" => "Le chapitre ne peut être vide"];
            }elseif (!$chapitreValidator->isChapitre(htmlspecialchars($_POST['chapitre']))){
                $errors[] = ['error' => "chapitre", "message" => "Le chapitre n'est pas au bon format!"];
            }
            if(empty(htmlspecialchars($_POST['published_at']))){
                $errors[] = ['error' => "published_at", "message" => "La date de publication ne peut être vide!"];
            }elseif(!$chapitreValidator->isDate(htmlspecialchars($_POST['published_at']))){
                $errors[]= ['error' => "published_at", "message" => "La date n'est pas valide!"];
            }
            if(!isset($_POST['published'])){
                $errors[] = ['error' => "published", "message" => "Le statut de la publication ne peut être vide"];
            }elseif (!$chapitreValidator->isPublished(htmlspecialchars($_POST['published']))){
                $errors[] = ['erro' => "published", "message" => "Le statut de publication n'est pas valide"];
            }

            if(empty($errors)){
                $chapitreManager = new ChapitreManager();
                $chapitre = new Chapitre();
                $chapitre->setTitle(htmlspecialchars($_POST['title']))
                    ->setChapitre($_POST['chapitre'])
                    ->setPublished_at(htmlspecialchars($_POST['published_at']))
                    ->setPublished(htmlspecialchars($_POST['published']));

                $chapitreManager->add($chapitre);

                $this->redirectTo('/admin/chapitres');
            }

        }
        $this->render("admin/chapitre.html.twig", array('errors' => $errors));
    }

    public function editChapitreAction($id){
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable!");
        }
        $chapitreManager = new ChapitreManager();
        $chapitre = $chapitreManager->findOneById($id);
        if(!$chapitre){
            throw new \Exception("Page introuvable");
        }
        $errors = [];
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $chapitreValidator = new  ChapitreValidator();
            if(empty(htmlspecialchars($_POST['title']))){
                $errors[] = ['error' => "title", "message" => "Le titre ne peut être vide"];
            }else if(!$chapitreValidator->isTitle(htmlspecialchars($_POST['title']))){
                $errors[] = ['error' => "title", "message" => "Le titre n'est pas au bon format!"];
            }
            if(empty(htmlspecialchars($_POST['chapitre']))){
                $errors[] = ['error' => "chapitre", "message" => "Le chapitre ne peut être vide"];
            }elseif (!$chapitreValidator->isChapitre(htmlspecialchars($_POST['chapitre']))){
                $errors[] = ['error' => "chapitre", "message" => "Le chapitre n'est pas au bon format!"];
            }
            if(empty(htmlspecialchars($_POST['published_at']))){
                $errors[] = ['error' => "published_at", "message" => "La date de publication ne peut être vide!"];
            }elseif(!$chapitreValidator->isDate(htmlspecialchars($_POST['published_at']))){
                $errors[]= ['error' => "published_at", "message" => "La date n'est pas valide!"];
            }
            if(!isset($_POST['published'])){
                $errors[] = ['error' => "published", "message" => "Le statut de la publication ne peut être vide"];
            }elseif (!$chapitreValidator->isPublished(htmlspecialchars($_POST['published']))){
                $errors[] = ['erro' => "published", "message" => "Le statut de publication n'est pas valide"];
            }

            if(empty($errors)){
                var_dump($_POST['published']);
                exit;
                $chapitreManager = new ChapitreManager();
                $chapitre = new Chapitre();
                $chapitre->setTitle(htmlspecialchars($_POST['title']))
                    ->setChapitre($_POST['chapitre'])
                    ->setPublished_at(htmlspecialchars($_POST['published_at']))
                    ->setPublished(htmlspecialchars($_POST['published']));

                $chapitreManager->add($chapitre);

                $this->redirectTo('/admin/chapitres');
            }

        }
        $this->render("admin/chapitre.html.twig", array('errors' => $errors, 'chapitre' => $chapitre));
    }

    public function deleteChapitreAction($id){
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable");
        }

        $chapitreManager = new ChapitreManager();
        $chapitre = $chapitreManager->findOneById($id);
        if(!$chapitre){
            throw new \Exception("Page introuvable");
        }
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            var_dump($_POST);
            if(htmlspecialchars($_POST['delete']) == 'Supprimer'){
                $chapitreManager->delete($chapitre);
            }
        }

        $this->render("admin/delete_chapitre.html.twig", array('chapitre' => $chapitre));
    }

    public function commentairesAction(){
        $this->isAuthorized();
        $commentaireManager = new CommentaireManager();

        $listCommentaires = $commentaireManager->findAll();
        $this->render('admin/commentaires.html.twig', array('listCommentaire' => $listCommentaires), $_SESSION);
    }

    public function editCommentaireAction($id){
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable");
        }
        $commentaireManager = new CommentaireManager();
        $commentaire = $commentaireManager->findOneById($id);
        if(!$commentaire){
            throw new \Exception("Page introuvable");
        }
        $errors = [];

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            if(!isset($_POST['etat'])){
                $errors[] = ["error" => "etat", "message" => "L'etat du commentaire ne peut être vide"];
            }elseif (htmlspecialchars($_POST['etat']) != "normal" && htmlspecialchars($_POST['etat']) != "signaled" &&htmlspecialchars($_POST['etat']) != "banished" ){
                $errors[] = ["error" => "etat", "message" =>"L'eta du commentaire n'est pas au bon format"];
            }

            if(empty($errors)){
                if($_POST['etat'] == "normal"){
                    $commentaire->setSignaled(false)
                        ->setBanished(false);
                }elseif ($_POST['etat'] == "signaled"){
                    $commentaire->setSignaled(true)
                        ->setBanished(false);
                }elseif($_POST['etat'] == "banished"){
                    $commentaire->setSignaled(false)
                        ->setBanished(true);
                }
                $commentaireManager->update($commentaire);
                $this->redirectTo('/admin/commentaires');
            }

        }

        $this->render("admin/commentaire.html.twig", array('commentaire' => $commentaire, 'errors' => $errors));

    }

    public function signaledCommentairesAction(){
        $this->isAuthorized();

        $commentaireManager = new CommentaireManager();
        $listCommentaire = $commentaireManager->findAllSignaled();

        $this->render("admin/commentaires_signaled.html.twig",array('listCommentaire' => $listCommentaire));
    }

    public function banishedCommentairesAction(){
        $this->isAuthorized();

        $commentaireManager = new CommentaireManager();
        $listCommentaire = $commentaireManager->findAllBanished();

        $this->render("admin/commentaires_banished.html.twig",array('listCommentaire' => $listCommentaire));
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
}