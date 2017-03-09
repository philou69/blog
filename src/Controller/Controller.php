<?php


namespace App\Controller;


use App\Manager\ContentManager;

class Controller
{
    private $loader;
    private $twig;
    protected $contentManager;


    public function __construct()
    {
        $this->loader = new \Twig_Loader_Filesystem(__DIR__.'/../Resources/views');
        $this->twig = new \Twig_Environment($this->loader, array('cache' => __DIR__.'/../../var/cache', 'debug' =>true));
        $this->twig->addExtension(new \Twig_Extension_Debug());
        $this->contentManager = new ContentManager();
    }

    /**
     * @param $view
     * @param array $params
     * Fonction génerique permettant d'afficher une vue twig
     */
    protected function render($view, $params = [], $session = null)
    {
        $contentManager = new ContentManager();
        $contents  =  $contentManager->findAll();
        $this->twig->addGlobal('session', $session);
        $this->twig->addGlobal('contents', $contents);
        $this->template = $this->twig->load($view);
        return $this->template->render($params);
    }

    protected function redirectTo($string){
        header("Location : $string");
        echo "<META HTTP-EQUIV='refresh' CONTENT='0;URL=$string'>";
    }

    protected function session()
    {
        // On commence par vérifie l'exisance d'une session
        if (empty($_SESSION)) {
            // On va crée une session visiteur
            $this->fillSession();
        }
    }

    protected function fillSession($user = null)
    {
        session_unset();
        if ($user) {

            $_SESSION['id'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['roles'] = $user->getRoles();
            $_SESSION['isconnected'] = true;
        } else {
            $_SESSION['username'] = "visiteur";
            $_SESSION['roles'] = ["ROLE_USER"];
            $_SESSION['isconnected'] = false;
        }
    }
}