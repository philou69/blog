<?php


namespace App\Controller;


use App\Manager\ContentManager;

class ContentController extends AdminController
{
    public function contentsAction(){
        $this->isAuthorized();
        $contentManager = new ContentManager();
        $contents = $contentManager->findAll();
        echo $this->render("admin/contents.html.twig", array('contents' => $contents));
    }

    public function contentAction($id){
        $this->isAuthorized();
        if(!is_numeric($id)){
            throw new \Exception("Page introuvable!");
        }
        $contentManager = new ContentManager();
        $content = $contentManager->findById($id);
        if(!$content){
            throw new \Exception("Page introuvable");
        }

        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $text = htmlspecialchars($_POST['content']);
            if(isset($text)){
                $content->setContent($text);
                $contentManager->update($content);
                $this->redirectTo("/admin/contents");
            }
        }
        echo $this->render("admin/content.html.twig", array("content" => $content));
    }
}