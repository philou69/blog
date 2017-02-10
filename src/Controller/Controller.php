<?php


namespace App\Controller;


use App\Manager\ContentManager;

class Controller
{
    private $loader;
    private $twig;

    public function __construct()
    {
        $this->loader = new \Twig_Loader_Filesystem(__DIR__.'/../Resources/views');
        $this->twig = new \Twig_Environment($this->loader, array('cache' => __DIR__.'/../../var/cache', 'debug' =>true));
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }

    /**
     * @param $view
     * @param array $params
     * Fonction génerique permettant d'afficher une vue twig
     */
    protected function render($view, $params = [], $session)
    {
        $contentManager = new ContentManager();
        $contents  =  $contentManager->findAll();
        $this->twig->addGlobal('session', $session);
        $this->twig->addGlobal('contents', $contents);
        $this->template = $this->twig->load($view);
        echo $this->template->render($params);
    }

    protected function redirectTo($string){
        header("Location : $string");
        echo "<META HTTP-EQUIV='refresh' CONTENT='0;URL=$string'>";
    }
}