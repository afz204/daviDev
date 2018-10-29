<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 10.07
 */

require 'config/config.php';

$totalUser = $config->CountTables('id', 'users');
$neworder  = $config->CountTables('*', 'transaction where statusOrder = 0 ');
$process  = $config->CountTables('*', 'transaction where statusOrder = 1 ');
$delivery  = $config->CountTables('*', 'transaction where statusOrder = 2 ');
$report  = 0;
$cancelorder  = $config->CountTables('*', 'transaction where statusOrder = 6 ');


include 'php/header.php';

$pages_dir = 'php/order/';
if(!empty($_GET['p'])){
    $pages = scandir($pages_dir, 0);
    unset($pages[0], $pages[1]);

    $p = $_GET['p'];
    if(in_array($p.'.php', $pages)){
        include($pages_dir.'/'.$p.'.php');
    } else {
        include('404.php');
    }
} else {
    include($pages_dir.'/index.php');
}

include 'php/footer.php';
