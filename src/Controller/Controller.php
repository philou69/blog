<?php


namespace App\Controller;


use App\Entity\User;
use App\Manager\ContentManager;
require_once 'app/parameters.php';
class Controller
{
    private $loader;
    private $twig;
    protected $contentManager;
    protected $template;
    protected $url;


    public function __construct()
    {
        $this->loader = new \Twig_Loader_Filesystem(__DIR__.'/../Resources/views');
        $this->twig = new \Twig_Environment($this->loader, array('cache' => __DIR__.'/../../var/cache', 'debug' =>true));
        $this->twig->addExtension(new \Twig_Extension_Debug());
        $this->contentManager = new ContentManager();
        $this->url = URL;
    }

    /**
     * @param $view
     * @param array $params
     * @return $this->template->render(
     * Fonction générique permettant d'afficher une vue twig
     */
    protected function render($view, $params = [], $session = null)
    {
        // Récuperation des contents
        $contentManager = new ContentManager();
        $contents  =  $contentManager->findAll();
        // Ajoutes des globals session et content
        $this->twig->addGlobal('session', $session);
        $this->twig->addGlobal('contents', $contents);
        // Création du template en rapport de la vue
        $this->template = $this->twig->load($view);
        // Retourne le rendu du template
        return $this->template->render($params);
    }

    /*
     * Génération d'une redirection
     * @string = route de la redirection
     */
    protected function redirectTo($string){
        header("Location : $string");
        echo "<META HTTP-EQUIV='refresh' CONTENT='0;URL=$string'>";
    }

    /*
     * creation de session
     */
    protected function session()
    {
        // On commence par vérifie l'exisance d'une session
        if (empty($_SESSION)) {
            // On va crée une session visiteur
            $this->fillSession();
        }
    }

    /*
     * Création de session
     */
    protected function fillSession(User $user = null)
    {
        session_unset();
        // Si user n'est pas vide, on créé une session correspondante
        if ($user) {
            $_SESSION['id'] = $user->getId();
            $_SESSION['pseudo'] = $user->getPseudo();
            $_SESSION['roles'] = $user->getRoles();
            $_SESSION['isconnected'] = true;
            $_SESSION['route'] = '';
        } else {
            // Sinon, on créé une session anonyme
            $_SESSION['pseudo'] = "visiteur";
            $_SESSION['roles'] = ["ROLE_USER"];
            $_SESSION['isconnected'] = false;
            $_SESSION['route'] = '';
        }
    }

    /*
     * Page des mentions légales
     */
    public function mentionAction()
    {
        echo $this->render('mentions.legales.html.twig');
    }
}