<?php


namespace App\Controller;


use App\Entity\Chapter;
use App\Entity\User;
use App\Manager\ChapterManager;
use App\Manager\CommentManager;
use App\Manager\ContentManager;
use App\Manager\PageManager;
use App\Manager\UserManager;
use App\Validator\ChapterValidator;
use App\Validator\UserValidator;

class AdminController extends Controller
{

    /*
     * Fonction renvoyant à la page index
     */
    public function indexAction(){
        // Vérification des autorisations du visiteur
        $this->isAuthorized();

        // On affiche la vue index de la partie admin
        echo $this->render('admin/index.html.twig');
    }

    /*
     * fonction vérifiant si le visiteur est autorisé
     */
    protected function isAuthorized(){
        // on démarre la session
        session_start();
        // On vérifie la présence de ROLE_ADMIN dans le tableau roles
        if(!in_array("ROLE_ADMIN",$_SESSION['roles'])){
            // On est dans le cas ou ce n'est pas bon
            // On vide la session et on redirige vers l'accueil du site
            session_unset();
            $this->redirectTo('/');
        }
    }


}