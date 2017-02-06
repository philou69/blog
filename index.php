<?php

use App\Router\Router;

require 'vendor/autoload.php';
include 'app/list_routes.php';
// Initialisation du router avec l'url en paramètre
$router = new Router($_GET['url']);

// Liste des routes avec leurs fonctions
// $router->get('route', function(parmas){ appel au controller});
$routes = $routes;
foreach ($routes as $route){
    $method = $route['method'];
    $url = $route['url'];
    $controller = $route['controller'];

    $router->$method($url, $controller);

}
$router->run();