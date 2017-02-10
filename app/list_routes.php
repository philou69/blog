<?php
// Listes des routes enregistré dans un tableau
// Le tableau sera de la forme
// $routes['nom_route']= ['method' => 'nom_method',
//                      'url' => 'nom_url',
//                      'controller' => 'Nom_controller:nom_action'];
$routes = [];

$routes["index"] = ["method" => "get",
                "url" => "/",
                "controller" => "App:index"];

$routes["chapitre"] = ["method" => "get",
                "url" => "/chapitre/:id",
                "controller" => "App:chapitre"];
$routes["add_commentaire"] = ["method" => "post",
                "url" => "/commetaire",
                "controller" => "App:add"];
$routes["login_get"] = ["method" => "get",
                "url" => "/login",
                "controller" => "App:login"];
$routes["login_post"] = ["method" => "post",
                "url" => "/login",
                "controller" => "App:login"];
$routes["logout"] = ["method" => "get",
                "url" => "/logout",
                "controller" => "App:logout"];
$routes["inscription"] = ["method" => "get",
                "url" => "/inscription",
                "controller" => "App:inscription"];
$routes["inscription_post"] = ["method" => "post",
                "url" => "/inscription",
                "controller" => "App:inscription"];
$routes["profil"] = ["method" => "get",
                "url" => "/profil",
                "controller" => "App:profil"];
$routes["profil_post"] = ["method" => "post",
                "url" => "/profil",
                "controller" => "App:profil"];
$routes["signal"] = ["method" => "get",
                "url" => "/signal/:id",
                "controller" => "App:signal"];
$routes["response"] = ["method" => "post",
                "url" => "/response/:id",
                "controller" => "App:response"];
$routes["admin_login_get"] = ["method" => "get",
                "url" => "/admin/login",
                "controller" => "Admin:login"];
$routes["admin_login_post"] = ["method" => "post",
                "url" => "/admin/login",
                "controller" => "Admin:login"];
$routes["admin_index"] = ["method" => "get",
                "url" => "/admin/",
                "controller"=> "Admin:index"];
$routes["admin_episodes"] = ["method" =>"get",
                "url" => "/admin/episodes",
                "controller" => "Admin:episode"];
$routes["admin_episode"] = ["method" =>"get",
                "url" => "/admin/episode/:id",
                "controller" => "Admin:episode"];
$routes["admin_episode_post"] = ["method" =>"post",
                "url" => "/admin/episode/:id",
                "controller" => "Admin:episode"];
$routes["admin_commentaires"] = ["method" => "get",
                "url" => "/admin/commentaires",
                "controller" => "Admin:commentaire"];
$routes["admin_commentaire"] = ["method" => "get",
                "url" => "/admin/commentaire/:id",
                "controller" => "Admin:commentaire"];
$routes["admin_commentaire_post"] = ["method" => "post",
                "url" => "/admin/commentaire/:id",
                "controller" => "Admin:commentaire"];
$routes["admin_logout"] = ["method" => "get",
                "url" => "/admin/login",
                "controller" => "Admin:logout"];


