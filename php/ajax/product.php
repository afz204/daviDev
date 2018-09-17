<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/05/2018
 * Time: 19.24
 */

// $g = array(11,12,13,14,15,16,17,18,19,21,31,32,33,34,35,36,51,52,53,61,62,63,64,65,71,72,73,74,75,76,81,82,91,94);
// $c = array(11,12,13);

// $b = array_diff($g, $c);



// $b = implode(' ', $b);
// $b = str_replace(' ', ',', $b);

// echo $b;
session_start();
require '../../config/api.php';
$config = new Admin();
$admin = $config->adminID();

if($_GET['type'] == 'newProd') {
    $type = $_POST['type'];
    $code = strtoupper($_POST['codeProduct']);
    $a = $_POST['cat'];
    $b = $_POST['sub'];
    $c = $_POST['title'];
    $link = str_replace(' ', '_', strtolower($c));
    $d = $_POST['tags'];
    $e = $_POST['cost'];
    $f = $_POST['sell'];

    $h = $_POST['short'];
    $i = $_POST['full'];
    $j = $_POST['note'];
    $k = $_POST['admin'];
    $title = strtolower(str_replace(" ", "_", $c)).'.jpg';
    if ($type == '1') {
        $g = '11,12,13,14,15,16,17,18,19,21,31,32,33,34,35,36,51,52,53,61,62,63,64,65,71,72,73,74,75,76,81,82,91,94';
    }else{
        $g = $_POST['city'];   
    }
    //echo $g;
    
    $tgl = $config->getDate('Y-m-d H:m:s');

    $cek = $config->getData('product_id', 'products', "product_id = '". $code ."' ");

    if(!empty($cek['product_id'])){
        echo '0';
    }else{
        $sql = "INSERT INTO products (product_id, category_id, subcategory_id, name_product, cost_price, selling_price, available_on, sort_desc, full_desc, note, tags, images, permalink, created_at, admin_id) 
        VALUES (:code, :a, :b, :c, :e, :f, :g, :h, :i, :j, :d, :title, :link, :tgl, :k)";

        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':code' => $code,
            ':a' => $a,
            ':b' => $b,
            ':c' => $c,
            ':e' => $e,
            ':f' => $f,
            ':g' => $g,
            ':h' => $h,
            ':i' => $i,
            ':j' => $j,
            ':d' => $d,
            ':title' => $title,
            ':link' => $link,
            ':tgl' => $tgl,
            ':k' => $k
        ));
        $reff = $config->lastInsertId();
        $logs = $config->saveLogs($reff, $admin, 'c', 'new products');
        if ($stmt) {
            echo $config->actionMsg('c', 'products');
        } else {
            echo 'Failed!';
        }
    }

    
}

if($_GET['type'] == 'changeStatusProduct'){
    $a = $_POST['kode_status'];
    $b = $_POST['kode_product'];

    if($a == 1){
        $cek = $config->getData('images', 'products', "product_id = '". $b ."' ");

        if(empty($cek['images'])){
            echo "Upload Images products First!";
        }else{
            $stmt = $config->runQuery('UPDATE products SET status = :st WHERE product_id = :code ');
            $stmt->execute(array(':st' => $a, ':code' => $b ));
            if($stmt){
                $logs = $config->saveLogs($b, $admin, 'u', 'change status products');
                echo $config->actionMsg('u', 'products');
            }else{
                echo 'Failed!';
            }
        }
    }else{
            $stmt = $config->runQuery('UPDATE products SET status = :st WHERE product_id = :code ');
            $stmt->execute(array(':st' => $a, ':code' => $b ));
            if($stmt){
                $logs = $config->saveLogs($b, $admin, 'u', 'change status products');
                echo $config->actionMsg('u', 'products');
            }else{
                echo 'Failed!';
            }
    }
    
}