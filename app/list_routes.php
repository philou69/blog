<?php
// Listes des routes enregistré dans un tableau
// Le tableau sera de la forme
// $routes['nom_route']= ['method' => 'nom_method',
//                      'url' => 'nom_url',
//                      'controller' => 'Nom_controller:nom_action'];
$routes = [];

// Route Principale
/*
 * Route de la page d'accueil
 */
$routes["index"] = ["method" => "get",
                "url" => "/",
                "controller" => "App:index"];

/*
 * Route de la page d'accueil de la zone admin
 */
$routes["index_admin"] = ["method" => "get",
    "url" => "/admin/",
    "controller"=> "Admin:index"];

// Route des Chapters
/*
 * Route de la liste des chapitres
 */
$routes["chapters"] = ["method" => "get",
                "url" => "/chapters/:page",
                "controller" => "Chapter:chapters"];

/*
 * Route affichant un chapitre
 */
$routes["chapter"] = ["method" => "get",
                "url" => "/chapter/:id",
                "controller" => "Chapter:chapter"];
/*
 * Route affichant les chapitres dans la zone admin
 */
$routes["chapters_admin"] = ["method" =>"get",
    "url" => "/admin/chapters",
    "controller" => "ChapterAdmin:chapters"];

/*
 * Route affichant les chapitres en cours d'écriture dans la zone admin
 */
$routes["chapters_draft"] = ["method" =>"get",
    "url" => "/admin/chapters/draft",
    "controller" => "ChapterAdmin:chaptersDraft"];

/*
 * Route affichant les chapitres publiés dans la zone admin
 */
$routes["chapters_published"] = ["method" =>"get",
    "url" => "/admin/chapters/published",
    "controller" => "ChapterAdmin:chaptersPublished"];
/*
 * Route permettant d'ajouter un chapitre par le biais de la zone admin
 */
$routes["chapter_add"] = ["method" =>"get",
    "url" => "/admin/chapter/add",
    "controller" => "ChapterAdmin:add"];
/*
 * Version post de la route précédente
 */
$routes["chapter_add_post"] = ["method" =>"post",
    "url" => "/admin/chapter/add",
    "controller" => "ChapterAdmin:add"];

/*
 * Route permettant de modifier un chapitre dans la zone admin
 */
$routes["chapter_edit"] = ["method" =>"get",
    "url" => "/admin/chapter/edit/:id",
    "controller" => "ChapterAdmin:edit"];
/*
 * Version post de la route précédente
 */
$routes["chapter_edit_post"] = ["method" =>"post",
    "url" => "/admin/chapter/edit/:id",
    "controller" => "ChapterAdmin:edit"];

/*
 * Route permettant de supprimer un chapitre dans la zone admin
 */
$routes["chapter_delete"] = ["method" =>"get",
    "url" => "/admin/chapter/delete/:id",
    "controller" => "ChapterAdmin:delete"];
/*
 * Version post de la route précédente
 */
$routes["chapter_delete_post"] = ["method" =>"post",
    "url" => "/admin/chapter/delete/:id",
    "controller" => "ChapterAdmin:delete"];

// Route des Comments
/*
 * Route permettant de créer un commentaire
 */
$routes["comment_create"] = ["method" => "get",
                "url" => "/comment/:id",
                "controller" => "Comment:create"];
/*
 * Version post
 */
$routes["comment_create_post"] = ["method" => "post",
                "url" => "/comment/:id",
                "controller" => "Comment:create"];
/*
 * Route pour signaler un comment
 */
$routes["comment_signal"] = ["method" => "get",
    "url" => "/signal/:id",
    "controller" => "Comment:signal"];
/*
 * Route pour répondre à un comment
 */
$routes["response"] = ["method" => "post",
    "url" => "/response/:id",
    "controller" => "Comment:response"];

/*
 * Route de la liste des comments dans la zone admin
 */
$routes["comments_admin"] = ["method" => "get",
    "url" => "/admin/comments",
    "controller" => "CommentAdmin:comments"];

/*
 * Route de la liste des comments banish dans la zone admin
 */
$routes["comments_banished"] = ["method" => "get",
    "url" => "/admin/comments/banished",
    "controller" => "CommentAdmin:banishedComments"];

/*
 * Route de la liste des comments signaled dans la zone admin
 */
$routes["comments_signaled"] = ["method" => "get",
    "url" => "/admin/comments/signaled",
    "controller" => "CommentAdmin:signaledComments"];

/*
 * Route de modification du status d'un comment
 */
$routes["comment_edit"] = ["method" => "get",
    "url" => "/admin/comment/edit/:id",
    "controller" => "CommentAdmin:edit"];
/*
 * version post de la route précédente
 */
$routes["comment_edit_post"] = ["method" => "post",
    "url" => "/admin/comment/edit/:id",
    "controller" => "CommentAdmin:edit"];

// Route des Users
/*
 * Route de connexion
 */
$routes["login"] = ["method" => "get",
                "url" => "/login",
                "controller" => "User:login"];
/*
 * Version post de la route précédente
 */
$routes["login_post"] = ["method" => "post",
                "url" => "/login",
                "controller" => "User:login"];

/*
 * Route de deconnection
 */
$routes["logout"] = ["method" => "get",
                "url" => "/logout",
                "controller" => "User:logout"];

/*
 * Route d'inscription
 */
$routes["inscription"] = ["method" => "get",
                "url" => "/inscription",
                "controller" => "User:inscription"];
/*
 * Version post de la route précédente
 */
$routes["inscription_post"] = ["method" => "post",
                "url" => "/inscription",
                "controller" => "User:inscription"];

/*
 * Route d'accès au profil du visiteur
 */
$routes["profil"] = ["method" => "get",
                "url" => "/profil",
                "controller" => "User:profil"];
/*
 * Version post de la route précédente
 */
$routes["profil_post"] = ["method" => "post",
                "url" => "/profil",
                "controller" => "User:profil"];

/*
 * Route de connexion à la zone admin
 */
$routes["login_get_admin"] = ["method" => "get",
                "url" => "/admin/login",
                "controller" => "UserAdmin:login"];
/*
 * Version post de la route précedente
 */
$routes["login_post_admin"] = ["method" => "post",
                "url" => "/admin/login",
                "controller" => "UserAdmin:login"];

/*
 * Route de deconnexion
 */
$routes["logout_admin"] = ["method" => "get",
                "url" => "/admin/logout",
                "controller" => "UserAdmin:logout"];

/*
 * Route de la liste des visiteurs dans la zone admin
 */
$routes["users"] = ["method" => "get",
                "url" => "/admin/users",
                "controller" => "UserAdmin:users"];

/*
 * Route de la liste des visiteurs bani dans la zone admin
 */
$routes["users_banished"] = ["method" => "get",
                "url" => "/admin/users/banished",
                "controller" => "UserAdmin:usersBanished"];

/*
 * Route d'edit du status d'un user dans la zone d'admin
 */
$routes["user_admin"] = ["method" => "get",
                "url" => "/admin/user/:id",
                "controller" => "UserAdmin:user"];
$routes["user_admin_post"] = ["method" => "post",
    "url" => "/admin/user/:id",
    "controller" => "UserAdmin:user"];

/*
 * Route pour réinitialiser le mot de passe
 */
$routes["reset_password"] = ["method" => "get",
    "url" => "/user/reset",
    "controller" => "User:reset"];
/*
 * Version post de la route précédente
 */
$routes["reset_password_post"] = ["method" => "post",
    "url" => "/user/reset",
    "controller" => "User:reset"];

// Routes des Contents
/*
 * Route de la liste des contents dans la zone admin
 */
$routes["contents"] = ["method" => "get",
                "url" => "/admin/contents",
                "controller" => "ContentAdmin:contents"];

/*
 * Route d'edit d'un content dans la zone admin
 */
$routes["content"] = ["method" => "get",
                "url" => "/admin/content/:id",
                "controller" => "ContentAdmin:content"];
/*
 * Version post de la route précédente
 */
$routes["content_post"] = ["method" => "post",
                "url" => "/admin/content/:id",
                "controller" => "ContentAdmin:content"];

/*
 * Mentions légales
 */
$routes['mention_legales'] = ['method' => 'get',
                "url" => '/mentions_legales',
                'controller' => ':mention'];
