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
    $tgl = $config->getDate('YmdHms');
    $unik = str_replace(' ', '', $a);
    $unik = substr($unik, 0, 3);
    $unik = strtoupper($unik);
    $unik = $unik. $tgl;

    $sql = "INSERT INTO corporates (nama, CorporateUniqueID, bidang, telp, handphone, fax, email, website, cp, alamat, kelurahan, kecamatan, kota, provinsi, kodepos, created_at)
            VALUES (:a, :nen, :b, :c, :d, :e, :f, :g, :n, :l, :k, :j, :i, :h, :m, :date)";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':a' => $a,
        ':nen' => $unik,
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
            $cp = $config->runQuery("INSERT INTO corporate_pics (corporate_id, name, nomor, province_id, city, kecamatan, kelurahan, alamat) VALUES (:a, :b, :c, :d, :e, :f, :g, :h)");
            $cp->execute(array(
                ':a'    => $unik,
                ':b'    => $n,
                ':c'    => $c,
                ':d'    => $h,
                ':e'    => $i,
                ':f'    => $j,
                ':g'    => $k,
                ':h'    => $l
            ));

            $reff = $config->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'new PIC');
    }else{
        echo '0';
    }
}

if($_GET['type'] == 'savePIC'){
    $a = $_POST['kode_perusahaan'];
    $b = $_POST['nama_pic'];
    $c = $_POST['nomor_hp'];
    $d = $_POST['provinsi'];
    $e = $_POST['kota'];
    $f = $_POST['kec'];
    $g = $_POST['kel'];
    $h = $_POST['alamat'];

    $stmt = $config->runQuery("INSERT INTO corporate_pics (corporate_id, name, nomor, province_id, city, kecamatan, kelurahan, alamat) VALUES (:a, :b, :c, :d, :e, :f, :g, :h)");
    $stmt->execute(array(
        ':a'    => $a,
        ':b'    => $b,
        ':c'    => $c,
        ':d'    => $d,
        ':e'    => $e,
        ':f'    => $f,
        ':g'    => $g,
        ':h'    => $h
    ));

    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new PIC');

    if($stmt){
        echo $config->actionMsg('c', 'corporate_pics');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'deletePIC'){
    $a = $_POST['kode_perusahaan'];

    $stmt = $config->delRecord('corporate_pics', 'id', $a);

  
    $logs = $config->saveLogs($a, $admin, 'd', 'hapus PIC');

    if($stmt){
        echo $config->actionMsg('d', 'corporate_pics');
    }else{
        echo 'Failed!';
    }
}