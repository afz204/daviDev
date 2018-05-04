<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 13.10
 */
define('URL', 'http://localhost/bungdav/');
define('CORPORATE', URL.'corporate/');
define('ORDER', URL.'order/');
define('MANAGEMENT', URL.'management/');
define('PAYMENT', URL.'payment/');
define('BD', URL.'bd/');


require 'api.php';
require 'session.php';

$config = new Admin();

if(isset($_SESSION['user_session']))
{
    $session_id = $_SESSION['user_session'];
}else{
    $session_id = "";
}
//read url
$url = "$_SERVER[REQUEST_URI]";
$url = explode('/', $url);

$menu = $url[2]; //menu
if(empty($menu)){
    $menu = 'index';
}else{
    $menu = $menu;
}
if (isset($url[3])){
    $root = explode('&', $url[3]);
    if($root == true){
        $root = explode('=', $root[0]);
        
        if(isset($root[1])){
            $footer = $root[1];
        }else{
            $footer = "";
        }
    }else{
        $footer = $url[1];
    }
}else{
    $footer = "";
}
//
$admin = $config->runQuery('SELECT id, name, email, jabatan, role_id FROM users WHERE id = :id');
$admin->execute(array(':id' => $session_id));
$admin = $admin->fetch(PDO::FETCH_LAZY);
$device = $config->systemInfo();
//info weight pages
$previllages = $config->ProductsJoin('sub_menus.link, previllages.weight', 'sub_menus', 'INNER JOIN previllages ON previllages.id_submenu = sub_menus.id',
    "WHERE previllages.id_admin = ". $admin['id'] ." AND sub_menus.link LIKE '%". $footer ."%' GROUP BY sub_menus.link");
$previllages = $previllages->fetch(PDO::FETCH_LAZY);
$access = $config->weightPages($previllages['weight']);

//end of info
$listMenu = $config->ProductsJoin('menus.id, menus.menu, menus.links, staffs.id_roles', 'menus', 'INNER JOIN staffs ON staffs.id_menu = menus.id', 'WHERE staffs.id_roles = '. $admin['role_id']);
$subMenus = $config->ProductsJoin('sub_menus.submenu, sub_menus.link, menus.menu, menus.links, previllages.id_admin, previllages.weight', 'sub_menus', 'INNER JOIN menus ON menus.id = sub_menus.id_menu
INNER JOIN previllages ON previllages.id_submenu = sub_menus.id', "WHERE previllages.id_admin = ".$admin['id'] ." AND menus.links LIKE '%". $menu ."%'" );
$listAdmin = $config->Products('id, name, email, jabatan, role_id, status', 'users');