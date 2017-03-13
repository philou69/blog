<?php


namespace App\Controller;


use App\Manager\ContentManager;

class ContentAdminController extends AdminController
{
    /*
     * Liste des contents
     */
    public function contentsAction(){
        $this->isAuthorized();
        $contentManager = new ContentManager();
        $contents = $contentManager->findAll();
        echo $this->render("admin/contents.html.twig", array('contents' => $contents));
    }

    /*
     * Edit d'un content
     */
    public function contentAction($id){
        // Vérification de l'id et de l'existence du content
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable!");
        }
        $contentManager = new ContentManager();
        $content = $contentManager->findById($id);
        if(!$content){
            throw new \Exception("Page introuvable");
        }
         // Le formulaire est il envoié
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $text = htmlspecialchars($_POST['content']);
            if(isset($text)){
                $content->setContent($text);
                $contentManager->update($content);
                $this->redirectTo("/admin/contents");
            }
        }
        // Affichage du formulaire
        echo $this->render("admin/content.html.twig", array("content" => $content));
    }
}