<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 17.27
 */
session_start();
require '../../config/api.php';
$config = new Admin();

$admin = $config->adminID();


if($_GET['type'] == 'new'){
    $a = $_POST['nama'];
    $b = $_POST['bidang'];
    $c = $_POST['telp'];
    $d = $_POST['hp'];
    $e = $_POST['fax'];
    $f = $_POST['email'];
    $g = $_POST['web'];
    $h = $_POST['prov'];
    $i = $_POST['kota'];
    $j = $_POST['kec'];
    $k = $_POST['kel'];
    $l = $_POST['alamat'];
    $m = $_POST['pos'];
    $n = $_POST['cp'];
    $date = $config->getDate('Y-m-d H:m:s');

//    $z = array($a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l, $m);
//    print_r($z);

    $sql = "INSERT INTO corporates (nama, bidang, telp, handphone, fax, email, website, cp, alamat, kelurahan, kecamatan, kota, provinsi, kodepos, created_at)
            VALUES (:a, :b, :c, :d, :e, :f, :g, :n, :l, :k, :j, :i, :h, :m, :date)";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':a' => $a,
        ':b' => $b,
        ':c' => $c,
        ':d' => $d,
        ':e' => $e,
        ':f' => $f,
        ':g' => $g,
        ':n' => $n,
        ':l' => $l,
        ':k' => $k,
        ':j' => $j,
        ':i' => $i,
        ':h' => $h,
        ':m' => $m,
        ':date' => $date
    ));
    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new corporate');
    if($stmt){
        echo '1';
    }else{
        echo '0';
    }
}