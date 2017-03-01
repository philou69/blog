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


    public function indexAction(){
        $this->isAuthorized();

        $this->render('admin/index.html.twig');
    }

    protected function isAuthorized(){
        session_start();
        if(!in_array("ROLE_ADMIN",$_SESSION['roles'])){
            session_unset();
            $this->redirectTo('/');
        }
    }


}