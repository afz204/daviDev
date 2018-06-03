<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 10/04/2018
 * Time: 20.23
 */

session_start();
require '../../config/api.php';
$config = new Admin();

$admin = $config->adminID();

if($_GET['type'] == 'newKurir'){
    $a  = $_POST['admin'];
    $b  = $_POST['nama'];
    $c  = $_POST['email'];
    $d  = $_POST['hp'];
    $e  = $_POST['wa'];
    $f  = $_POST['province'];
    $g  = $_POST['kota'];
    $h  = $_POST['kecamatan'];
    $i  = $_POST['kelurahan'];
    $j  = $_POST['alamat'];
    $k  = $config->getDate('Y-m-d H:m:s');
    $l  = '1';

    $sql = "INSERT INTO kurirs (nama_kurir, email, phone, wa, alamat, kel, kec, kota, province, status, created_at)
    VALUES (:a, :b, :c, :d, :e, :f, :g, :h, :i, :j, :k)";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':a'    => $b,
        ':b'    => $c,
        ':c'    => $d,
        ':d'    => $e,
        ':e'    => $j,
        ':f'    => $i,
        ':g'    => $h,
        ':h'    => $k,
        ':i'    => $f,
        ':j'    => $l,
        ':k'    => $k
    ));
    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new kurirs');
    if($stmt){
        echo "Berhasil menambahkan Kurir baru!";
    }else{
        echo "Failed";
    }
}
if($_GET['type'] == 'addCharge'){
    $a  = $_POST['admin'];
    $b  = $_POST['harga'];
    $c  = $_POST['kelurahan'];
    $d  = $config->getDate('Y-m-d H:m:s');

    $query = "SELECT id FROM delivery_charges WHERE id_kelurahan = :id";
    $cek = $config->runQuery($query);
    $cek->execute(array(':id' => $c));

    if($cek->rowCount() > 0 ){
        echo 'id_kelurahan sudah terdaftar di database!';
    }else{
        $sql = "INSERT INTO delivery_charges (id_kelurahan, price, created_at, admin_id) VALUES (:a, :b, :c, :d)";
        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':a'    => $c, 
            ':b'    => $b, 
            ':c'    => $d,
            ':d'    => $a
        ));
        $reff = $config->lastInsertId();
        $logs = $config->saveLogs($reff, $admin, 'c', 'new kurirs');

        if($stmt){
            echo $config->actionMsg('c', 'delivery_charges');
        }else{
            echo 'Failed';
        }
    }
}
if($_GET['type'] == 'delCharge'){
    $a  = $_POST['admin'];
    $b  = $_POST['keterangan'];

    $sql = "DELETE FROM delivery_charges WHERE id = :id";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(':id'  => $b));

    if($stmt){
        echo 'Berhasil Delete Delivery Charge!';
        
        $logs = $config->saveLogs($b, $admin, 'd', 'delete delivery_charge');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'updateCharges'){
    $a  = $_POST['admin'];
    $b  = $_POST['harga'];
    $c  = $_POST['kelurahan'];

    $sql = "UPDATE delivery_charges SET price = :harga, admin_id = :adminID WHERE id = :kode";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':harga'  => $b,
        ':adminID'=> $a,
        ':kode'   => $c
    ));

    if($stmt){
        echo $config->actionMsg('u', 'delivery_charges');
        
        $logs = $config->saveLogs($c, $admin, 'c', 'update delivery_charge');
    }else{
        echo 'Failed!';
    }
}

?>