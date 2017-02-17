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
$routes["chapitres"] = ["method" => "get",
                "url" => "/chapitres",
                "controller" => "App:chapitres"];
$routes["chapitre"] = ["method" => "get",
                "url" => "/chapitre/:id",
                "controller" => "App:chapitre"];
$routes["chapitre_post"] = ["method" => "post",
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
$routes["admin_chapitres"] = ["method" =>"get",
                "url" => "/admin/chapitres",
                "controller" => "Admin:chapitres"];
$routes["admin_add_chapitre"] = ["method" =>"get",
                "url" => "/admin/chapitre/add",
                "controller" => "Admin:addChapitre"];
$routes["admin_add_chapitre_post"] = ["method" =>"post",
                "url" => "/admin/chapitre/add",
                "controller" => "Admin:addChapitre"];
$routes["admin_edit_chapitre"] = ["method" =>"get",
                "url" => "/admin/chapitre/edit/:id",
                "controller" => "Admin:editChapitre"];
$routes["admin_edit_chapitre_post"] = ["method" =>"post",
                "url" => "/admin/chapitre/edit/:id",
                "controller" => "Admin:editChapitre"];
$routes["admin_delete_chapitre"] = ["method" =>"get",
                "url" => "/admin/chapitre/delete/:id",
                "controller" => "Admin:deleteChapitre"];
$routes["admin_delete_chapitre_post"] = ["method" =>"post",
                "url" => "/admin/chapitre/delete/:id",
                "controller" => "Admin:deleteChapitre"];
$routes["admin_commentaires"] = ["method" => "get",
                "url" => "/admin/commentaires",
                "controller" => "Admin:commentaires"];
$routes["admin_banished_commentaires"] = ["method" => "get",
                "url" => "/admin/comments/banished",
                "controller" => "Admin:banishedCommentaires"];
$routes["admin_signaled_commentaires"] = ["method" => "get",
                "url" => "/admin/comments/signaled",
                "controller" => "Admin:signaledCommentaires"];
$routes["admin_edit_commentaire"] = ["method" => "get",
                "url" => "/admin/commentaire/edit/:id",
                "controller" => "Admin:editCommentaire"];
$routes["admin_edit_commentaire_post"] = ["method" => "post",
                "url" => "/admin/commentaire/edit/:id",
                "controller" => "Admin:editCommentaire"];
$routes["admin_logout"] = ["method" => "get",
                "url" => "/admin/login",
                "controller" => "Admin:logout"];
$routes["admin_users"] = ["method" => "get",
                "url" => "/admin/users",
                "controller" => "Admin:users"];
$routes["admin_user"] = ["method" => "get",
                "url" => "/admin/user/:id",
                "controller" => "Admin:user"];
$routes["admin_pages"] = ["method" => "get",
                "url" => "/admin/pages",
                "controller" => "Admin:pages"];
$routes["admin_page"] = ["method" => "get",
                "url" => "/admin/page/:page",
                "controller" => "Admin:page"];
$routes["admin_user_post"] = ["method" => "post",
                "url" => "/admin/user/:id",
                "controller" => "Admin:user"];


