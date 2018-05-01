<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/05/2018
 * Time: 19.24
 */


require '../../config/api.php';
$config = new Admin();

if($_GET['type'] == 'newProd') {
    $type = $_POST['type'];
    $a = $_POST['cat'];
    $b = $_POST['sub'];
    $c = $_POST['title'];
    $c = str_replace(' ', '_', $c);
    $d = $_POST['tags'];
    $e = $_POST['cost'];
    $f = $_POST['sell'];

    $h = $_POST['short'];
    $i = $_POST['full'];
    $j = $_POST['note'];
    $k = $_POST['admin'];

    if ($type == '1') {
        $g = '11,12,13,14,15,16,17,18,19,21,31,32,33,34,35,36,51,52,53,61,62,63,64,65,71,72,73,74,75,76,81,82,91,94';
    } else {
        $g = $_POST['city'];
    }

    $images = $c . '.jpg';
    $tgl = $config->getDate('Y-m-d H:m:s');

//    $o = array($a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k);
//
//    echo '<pre>';
//    print_r($o);
//    echo '</pre>';


    $sql = "INSERT INTO products (category_id, subcategory_id, name_product, cost_price, selling_price, available_on, sort_desc, full_desc, note, images, created_at, admin_id) 
    VALUES (:a, :b, :c, :e, :f, :g, :h, :i, :j, :images, :tgl, :k)";

    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':a' => $a,
        ':b' => $b,
        ':c' => $c,
        ':e' => $e,
        ':f' => $f,
        ':g' => $g,
        ':h' => $h,
        ':i' => $i,
        ':j' => $j,
        ':images' => $images,
        ':tgl' => $tgl,
        ':k' => $k
    ));

    if ($stmt) {
        echo $config->actionMsg('c', 'products');
    } else {
        echo 'Failed!';
    }
}