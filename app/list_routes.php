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
$routes["chapters"] = ["method" => "get",
                "url" => "/chapters",
                "controller" => "App:chapters"];
$routes["chapter"] = ["method" => "get",
                "url" => "/chapter/:id",
                "controller" => "App:chapter"];
$routes["chapter_post"] = ["method" => "post",
                "url" => "/chapter/:id",
                "controller" => "App:chapter"];
$routes["add_comment"] = ["method" => "post",
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
$routes["admin_chapters"] = ["method" =>"get",
                "url" => "/admin/chapters",
                "controller" => "Admin:chapters"];
$routes["admin_chapters_"] = ["method" =>"get",
                "url" => "/admin/chapters",
                "controller" => "Admin:chapters"];
$routes["admin_chapters"] = ["method" =>"get",
                "url" => "/admin/chapters",
                "controller" => "Admin:chapters"];
$routes["admin_chapters_draft"] = ["method" =>"get",
                "url" => "/admin/chapters/draft",
                "controller" => "Admin:chaptersDraft"];
$routes["admin_chapters_published"] = ["method" =>"get",
                "url" => "/admin/chapters/published",
                "controller" => "Admin:chaptersPublished"];
$routes["admin_add_chapter"] = ["method" =>"get",
                "url" => "/admin/chapter/add",
                "controller" => "Admin:addChapter"];
$routes["admin_add_chapter_post"] = ["method" =>"post",
                "url" => "/admin/chapter/add",
                "controller" => "Admin:addChapter"];
$routes["admin_edit_chapter"] = ["method" =>"get",
                "url" => "/admin/chapter/edit/:id",
                "controller" => "Admin:editChapter"];
$routes["admin_edit_chapter_post"] = ["method" =>"post",
                "url" => "/admin/chapter/edit/:id",
                "controller" => "Admin:editChapter"];
$routes["admin_delete_chapter"] = ["method" =>"get",
                "url" => "/admin/chapter/delete/:id",
                "controller" => "Admin:deleteChapter"];
$routes["admin_delete_chapter_post"] = ["method" =>"post",
                "url" => "/admin/chapter/delete/:id",
                "controller" => "Admin:deleteChapter"];
$routes["admin_comments"] = ["method" => "get",
                "url" => "/admin/comments",
                "controller" => "Admin:comments"];
$routes["admin_banished_comments"] = ["method" => "get",
                "url" => "/admin/comments/banished",
                "controller" => "Admin:banishedComments"];
$routes["admin_signaled_comments"] = ["method" => "get",
                "url" => "/admin/comments/signaled",
                "controller" => "Admin:signaledComments"];
$routes["admin_edit_comment"] = ["method" => "get",
                "url" => "/admin/comment/edit/:id",
                "controller" => "Admin:editComment"];
$routes["admin_edit_comment_post"] = ["method" => "post",
                "url" => "/admin/comment/edit/:id",
                "controller" => "Admin:editComment"];
$routes["admin_logout"] = ["method" => "get",
                "url" => "/admin/logout",
                "controller" => "Admin:logout"];
$routes["admin_users"] = ["method" => "get",
                "url" => "/admin/users",
                "controller" => "Admin:users"];
$routes["admin_users_banished"] = ["method" => "get",
                "url" => "/admin/users/banished",
                "controller" => "Admin:usersBanished"];
$routes["admin_user"] = ["method" => "get",
                "url" => "/admin/user/:id",
                "controller" => "Admin:user"];
$routes["admin_user_post"] = ["method" => "post",
    "url" => "/admin/user/:id",
    "controller" => "Admin:user"];
$routes["admin_contents"] = ["method" => "get",
                "url" => "/admin/contents",
                "controller" => "Admin:contents"];
$routes["admin_content"] = ["method" => "get",
                "url" => "/admin/content/:id",
                "controller" => "Admin:content"];
$routes["admin_content_post"] = ["method" => "post",
                "url" => "/admin/content/:id",
                "controller" => "Admin:content"];


