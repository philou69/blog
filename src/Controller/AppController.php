<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\User;
use App\Manager\ChapitreManager;
use App\Manager\UserManager;
use App\Router\RouterException;

class AppController
{
    public function indexAction(){
        $chapitreManager = new ChapitreManager();
        $listChapitres =  $chapitreManager->getAll();

        require __DIR__."/../Resources/views/index.php";
    }

    public function episodeAction($id){
        if(is_numeric($id)){
            echo "episode $id";
        }else{
            throw new RouterException("$id has to be a number");
        }
    }

    public function loginAction(){

    }

    public function logoutAction(){

    }

    public function inscriptionAction(){

    }
}inc