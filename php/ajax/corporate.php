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
    $t = $_POST['type'];
    $a = $_POST['nama'];
    $c = $_POST['telp'];
    $e = $_POST['fax'];
    $g = $_POST['web'];
    $h = $_POST['prov'];
    $i = $_POST['kota'];
    $j = $_POST['kec'];
    $k = $_POST['kel'];
    $l = $_POST['alamat'];
    $m = $_POST['pos'];
    $date = $config->getDate('Y-m-d H:m:s');

//    $z = array($a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l, $m);
//    print_r($z);
    $tgl = $config->getDate('YmdHms');
    $unik = str_replace(' ', '', $a);
    $unik = substr($unik, 0, 3);
    $unik = strtoupper($unik);
    $unik = $unik. $tgl;

    if($t == 'new') {
        $sql = "INSERT INTO corporates (nama, CorporateUniqueID, telp, fax, website,alamat, kelurahan, kecamatan, kota, provinsi, kodepos, created_at)
                VALUES (:a, :nen, :c, :e, :g, :l, :k, :j, :i, :h, :m, :date)";
        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':a' => $a,
            ':nen' => $unik,
            ':c' => $c,
            ':e' => $e,
            ':g' => $g,
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
    }else { echo 'mabok';}

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
    $i = $_POST['typePIC'];
    $j = $_POST['emailPIC'];

    $stmt = $config->runQuery("INSERT INTO corporate_pics (corporate_id, type, name, email, nomor, province_id, city, kecamatan, kelurahan, alamat) VALUES (:a, :i, :b, :j, :c, :d, :e, :f, :g, :h)");
    $stmt->execute(array(
        ':a'    => $a,
        ':i'    => $i,
        ':b'    => $b,
        ':j'    => $j,
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

if($_GET['type'] == 'saveCustomer'){
    $a = $_POST['first_name'];
    $b = $_POST['last_name'];
    $c = $_POST['email'];
    $d = $_POST['jenis_kelamin'];
    $e = $_POST['mobile_phone'];
    $f = $_POST['phone_number'];
    $g = $_POST['birth_day'];
    $h = $config->newPassword($_POST['password']);
    $z = $a. ' ' . $b;

    $stmt = $config->runQuery("INSERT INTO customer (FirstName, LastName, FullName, Email, Gender, Mobile, Phone, DOB, Username, Password, IsActive, CreatedDate, CreatedBy) VALUES (:a, :b, :c, :d, :e, :f, :g, :h, :i, :j, :k, :l)");
    $stmt->execute(array(
        ':a'    => $a,
        ':b'    => $b,
        ':z'    => $z,
        ':c'    => $c,
        ':d'    => $d,
        ':e'    => $e,
        ':f'    => $f,
        ':g'    => $g,
        ':h'    => $c,
        ':i'    => $h,
        ':j'    => '1',
        ':k'    => $config->getDate('Y-m-d H:m:s'),
        ':l'    => $admin
    ));

    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new Customer Organic');

    if($stmt){
        echo $config->actionMsg('c', 'customer');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'saveFlorist'){
    $a = $_POST['FloristName'];
    $b = $_POST['Email'];
    $c = $_POST['Username'];
    $d = $config->newPassword($_POST['Password']);
    $e = $_POST['mobile_phone'];
    $f = $_POST['ProvinsiCorporate'];
    $g = $_POST['KotaCorporate'];
    $h = $_POST['kecamatanCorporate'];
    $i = $_POST['kelurahanCorporate'];
    $j = $_POST['alamatCorporate'];

    $stmt = $config->runQuery("INSERT INTO florist (FloristName, Email, Username, Password, mobile_phone, province, city, kecamatan, kelurahan, alamat, IsActive, CreatedDate, CreatedBy) VALUES (:a, :b, :c, :d, :e, :f, :g, :h, :i, :j, :k, :l, :m)");
    $stmt->execute(array(
        ':a'    => $a,
        ':b'    => $b,
        ':c'    => $c,
        ':d'    => $d,
        ':e'    => $e,
        ':f'    => $f,
        ':g'    => $g,
        ':h'    => $h,
        ':i'    => $i,
        ':j'    => $j,
        ':k'    => '1',
        ':l'    => $config->getDate('Y-m-d H:m:s'),
        ':m'    => $admin
    ));

    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new Florist');

    if($stmt){
        echo $config->actionMsg('c', 'florist');
    }else{
        echo 'Failed!';
    }
}
if($_GET['type'] == 'updateFlorist'){
    $id = $_POST['IDFlorist'];
    $a = $_POST['FloristName'];
    $b = $_POST['Email'];
    $c = $_POST['Username'];
    $e = $_POST['mobile_phone'];
    $f = $_POST['ProvinsiCorporate'];
    $g = $_POST['KotaCorporate'];
    $h = $_POST['kecamatanCorporate'];
    $i = $_POST['kelurahanCorporate'];
    $j = $_POST['alamatCorporate'];

    $stmt = $config->runQuery("UPDATE florist SET FloristName = :a, Email = :b, Username = :c, mobile_phone = :e, province = :f, city = :g, kecamatan = :h, kelurahan = :i, alamat = :j, UpdatedDate = :l, UpdatedBy = :m WHERE id = ". $id ." ");
    $stmt->execute(array(
        ':a'    => $a,
        ':b'    => $b,
        ':c'    => $c,
        ':e'    => $e,
        ':f'    => $f,
        ':g'    => $g,
        ':h'    => $h,
        ':i'    => $i,
        ':j'    => $j,
        ':l'    => $config->getDate('Y-m-d H:m:s'),
        ':m'    => $admin
    ));

    $logs = $config->saveLogs($id, $admin, 'u', 'update Florist');

    if($stmt){
        echo $config->actionMsg('u', 'florist');
    }else{
        echo 'Failed!';
    }
}