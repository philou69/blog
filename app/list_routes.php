<?php
// Listes des routes enregistré dans un tableau
// Le tableau sera de la forme
// $routes['nom_route']= ['method' => 'nom_method',
//                      'url' => 'nom_url',
//                      'controller' => 'Nom_controller:nom_action'];
$routes = [];

// Route Principale
$routes["index"] = ["method" => "get",
                "url" => "/",
                "controller" => "App:index"];
$routes["admin_index"] = ["method" => "get",
    "url" => "/admin/",
    "controller"=> "Admin:index"];

// Route des Chapters
$routes["chapters"] = ["method" => "get",
                "url" => "/chapters",
                "controller" => "Chapter:chapters"];
$routes["chapter"] = ["method" => "get",
                "url" => "/chapter/:id",
                "controller" => "Chapter:chapter"];
$routes["admin_chapters"] = ["method" =>"get",
    "url" => "/admin/chapters",
    "controller" => "Chapter:chaptersAdmin"];
$routes["admin_chapters_draft"] = ["method" =>"get",
    "url" => "/admin/chapters/draft",
    "controller" => "Chapter:chaptersDraft"];
$routes["admin_chapters_published"] = ["method" =>"get",
    "url" => "/admin/chapters/published",
    "controller" => "Chapter:chaptersPublished"];
$routes["admin_add_chapter"] = ["method" =>"get",
    "url" => "/admin/chapter/add",
    "controller" => "Chapter:addChapter"];
$routes["admin_add_chapter_post"] = ["method" =>"post",
    "url" => "/admin/chapter/add",
    "controller" => "Chapter:addChapter"];
$routes["admin_edit_chapter"] = ["method" =>"get",
    "url" => "/admin/chapter/edit/:id",
    "controller" => "Chapter:editChapter"];
$routes["admin_edit_chapter_post"] = ["method" =>"post",
    "url" => "/admin/chapter/edit/:id",
    "controller" => "Chapter:editChapter"];
$routes["admin_delete_chapter"] = ["method" =>"get",
    "url" => "/admin/chapter/delete/:id",
    "controller" => "Chapter:deleteChapter"];
$routes["admin_delete_chapter_post"] = ["method" =>"post",
    "url" => "/admin/chapter/delete/:id",
    "controller" => "Chapter:deleteChapter"];

// Route des Comments
$routes["create_comment"] = ["method" => "get",
                "url" => "/comment/:id",
                "controller" => "Comment:create"];
$routes["create_comment_post"] = ["method" => "post",
                "url" => "/comment/:id",
                "controller" => "Comment:create"];
$routes["signal"] = ["method" => "get",
    "url" => "/signal/:id",
    "controller" => "Comment:signal"];
$routes["response"] = ["method" => "post",
    "url" => "/response/:id",
    "controller" => "Comment:response"];
$routes["admin_comments"] = ["method" => "get",
    "url" => "/admin/comments",
    "controller" => "Comment:comments"];
$routes["admin_banished_comments"] = ["method" => "get",
    "url" => "/admin/comments/banished",
    "controller" => "Comment:banishedComments"];
$routes["admin_signaled_comments"] = ["method" => "get",
    "url" => "/admin/comments/signaled",
    "controller" => "Comment:signaledComments"];
$routes["admin_edit_comment"] = ["method" => "get",
    "url" => "/admin/comment/edit/:id",
    "controller" => "Comment:editComment"];
$routes["admin_edit_comment_post"] = ["method" => "post",
    "url" => "/admin/comment/edit/:id",
    "controller" => "Comment:editComment"];

// Route des Users
$routes["login_get"] = ["method" => "get",
                "url" => "/login",
                "controller" => "User:login"];
$routes["login_post"] = ["method" => "post",
                "url" => "/login",
                "controller" => "User:login"];
$routes["logout"] = ["method" => "get",
                "url" => "/logout",
                "controller" => "user:logout"];
$routes["inscription"] = ["method" => "get",
                "url" => "/inscription",
                "controller" => "User:inscription"];
$routes["inscription_post"] = ["method" => "post",
                "url" => "/inscription",
                "controller" => "User:inscription"];
$routes["profil"] = ["method" => "get",
                "url" => "/profil",
                "controller" => "User:profil"];
$routes["profil_post"] = ["method" => "post",
                "url" => "/profil",
                "controller" => "User:profil"];
$routes["admin_login_get"] = ["method" => "get",
                "url" => "/admin/login",
                "controller" => "User:loginAdmin"];
$routes["admin_login_post"] = ["method" => "post",
                "url" => "/admin/login",
                "controller" => "User:loginAdmin"];


$routes["admin_logout"] = ["method" => "get",
                "url" => "/admin/logout",
                "controller" => "User:logoutAdmin"];
$routes["admin_users"] = ["method" => "get",
                "url" => "/admin/users",
                "controller" => "User:users"];
$routes["admin_users_banished"] = ["method" => "get",
                "url" => "/admin/users/banished",
                "controller" => "User:usersBanished"];
$routes["admin_user"] = ["method" => "get",
                "url" => "/admin/user/:id",
                "controller" => "User:user"];
$routes["admin_user_post"] = ["method" => "post",
    "url" => "/admin/user/:id",
    "controller" => "User:user"];
$routes["areset_password"] = ["method" => "get",
    "url" => "/user/reset",
    "controller" => "User:reset"];
$routes["reset_password_post"] = ["method" => "post",
    "url" => "/user/reset",
    "controller" => "User:reset"];

// Routes des Contents
$routes["admin_contents"] = ["method" => "get",
                "url" => "/admin/contents",
                "controller" => "Content:contents"];
$routes["admin_content"] = ["method" => "get",
                "url" => "/admin/content/:id",
                "controller" => "Content:content"];
$routes["admin_content_post"] = ["method" => "post",
                "url" => "/admin/content/:id",
                "controller" => "Content:content"];


