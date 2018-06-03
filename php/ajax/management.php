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


if($_GET['type'] == 'addAdmin') {
    $a = $_POST['nama'];
    $b = $_POST['email'];
    $c = $config->newPassword($_POST['pass']);
    $d = $_POST['levels'];
    $e = $_POST['roles'];
    $f = '1';
    $adm = $_POST['adm'];
    $tgl = $config->getDate('Y-m-d H:m:s');

    $sql    = "INSERT INTO users (name, email, password, jabatan, role_id, status, created_at) VALUES (:a, :b, :c, :d, :e, :f, :g)";
    $stmt   = $config->runQuery($sql);
    $stmt->execute(array(
        ':a'    => $a,
        ':b'    => $b,
        ':c'    => $c,
        ':d'    => $d,
        ':e'    => $e,
        ':f'    => $f,
        ':g'    => $tgl
    ));
    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new users');
    if($stmt){
        echo "Admin Berhasil masuk ke database!";
    }else{
        echo "Failed";
    }
}elseif($_GET['type'] == 'addSubmenu') {
    $a = $_POST['admin'];
    $b = $_POST['menu'];
    $c = $_POST['submenu'];
    $d = $_POST['link'];

    $sql = "INSERT INTO sub_menus (id_menu, submenu, link) VALUES  (:a, :b, :c)";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':a'    => $b,
        ':b'    => $c,
        ':c'    => $d
    ));

    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'new submenus');
    if($stmt){
        echo 'Submenu Berhasil masuk ke Database!';
    }else{
        echo 'Failed';
    }
}elseif ($_GET['type'] == 'menu'){
    $id = $_POST['id'];

    $stmt = $config->runQuery('SELECT id, submenu FROM sub_menus WHERE id_menu = :id');
    $stmt->execute(array(':id' => $id));
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());

}elseif ($_GET['type'] == 'addPrevillagUser'){
    $f = $_POST['users'];
    $a = $_POST['admin'];
    $b = $_POST['menu'];
    $c = $_POST['submenu'];
    $d = array_sum($_POST['previllage']);
    $tgl = $config->getDate('Y-m-d H:m:s');

    //check menu for user
    $cari = $config->CountTables('id', 'previllages WHERE id_submenu = '. $c .' AND id_admin = '. $f);
    if($cari > 0){
        echo 'Sub-menu telah ada di Database untuk user ini!';
    }else{

        $sql = "INSERT INTO previllages (id_admin, id_submenu, weight, created_at, admin_id) VALUES (:a, :b, :c, :d, :e)";
        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':a'    => $f,
            ':b'    => $c,
            ':c'    => $d,
            ':d'    => $tgl,
            ':e'    => $a
        ));
        $reff = $config->lastInsertId();
        $logs = $config->saveLogs($reff, $admin, 'c', 'new previllages');

        if($stmt){
            echo 'Previllages Berhasil masuk ke Database!';
        }else{
            echo 'Failed';
        }
    }

}elseif ($_GET['type'] == 'updatePrevillageUser'){
    $a  = $_POST['admin'];
    $b  = $_POST['id'];
    $c  = array_sum($_POST['previllage']);

    $sql = "UPDATE previllages SET weight = :c, admin_id = :a WHERE id = :b";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':c'    => $c,
        ':a'    => $a,
        ':b'    => $b
    ));
   
    $logs = $config->saveLogs($b, $admin, 'u', 'update previllages');

    if($stmt){
        echo 'Previllages Berhasil update!';
    }else{
        echo 'Failed';
    }
}elseif($_GET['type'] == 'deleteSubmenu'){
    $a  = $_POST['id'];
    $b  = $_POST['admin'];

    $stmt = $config->delRecord('sub_menus', 'id', $a);
    $logs = $config->saveLogs($reff, $admin, 'd', 'hapus sub menu');
    if($stmt){
        echo "Record Sub Menu berhasil di hapus";

    }else{
        echo "Failed";
    }
}elseif($_GET['type'] == 'delPrevillages'){
    $a  = $_POST['data'];
    $b  = $_POST['user'];
    $c  = $_POST['adminI'];

    $stmt = $config->delRecord('previllages', 'id', $a);
    $logs = $config->saveLogs($a, $admin, 'd', 'hapus previllages');
    if($stmt){
        echo "Record Previllages berhasil di hapus";

    }else{
        echo "Failed";
    }
}elseif($_GET['type'] == 'catSatuan'){
    $a = $_POST['data'];

    $sql = "SELECT id, content_id, category FROM satuans WHERE content_id = :id AND category != ''";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(':id' => $a));

    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());

}elseif($_GET['type'] == 'subCatSatuan'){
    $a = $_POST['data'];

    $sql = "SELECT id, content_id, category_id,  subcategory FROM satuans WHERE category_id = :id  ";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(':id' => $a));

    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());
}elseif($_GET['type'] == 'addSatuan'){
    $a = $_POST['admin'];
    $b = $_POST['content'];
    $c = $_POST['category'];
    $d = $_POST['subcategory'];
    $e = $_POST['isi'];

    $z = array($a, $b, $c, $d, $e);

    // echo '<pre>';
    // print_r($z);
    // echo '</pre>';

    if(empty($c)){

        //category
        $sql = "INSERT INTO satuans (content_id, category, admin_at) VALUES (:content, :category, :admin_id)";
        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':content'  => $b,
            ':category' => $e,
            ':admin_id' => $a
        ));
        $reff = $config->lastInsertId();
        $logs = $config->saveLogs($reff, $admin, 'c', 'tambah satuan');
        if($stmt){
            echo $config->actionMsg('c', 'satuans');
        }else{
            echo 'Failed!';
        }
    }else{

    //     echo '<pre>';
    // print_r($z);
    // echo '</pre>';
    //     subcategory
        $sql = "INSERT INTO satuans (content_id, category_id, subcategory, admin_at) VALUES (:con, :content, :category, :admin_id)";
        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':con'      => $b,
            ':content'  => $c,
            ':category' => $e,
            ':admin_id' => $a
        ));
        $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'tambah satuan');
        if($stmt){
            echo $config->actionMsg('c', 'satuans');
        }else{
            echo 'Failed!';
        }     
    }
}