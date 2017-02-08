<?php


namespace App\Controller;


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
    protected function render($view, $params = [])
    {
        $this->template = $this->twig->load($view);
        echo $this->template->render($params);
    }
}