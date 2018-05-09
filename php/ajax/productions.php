<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 01/04/2018
 * Time: 17.27
 */

require '../../config/api.php';
$config = new Admin();

if($_GET['type'] == 'listStoks')
{
    $id = $_GET['id'];
    $sql = "SELECT tmp_stoks.id, tmp_stoks.stoks_id, tmp_stoks.qty, tmp_stoks.admin_id, tmp_stoks.ket, tmp_stoks.created_at, stocks.nama_barang, stocks.satuan, users.name, cat.content AS category, subcat.category AS subcategory FROM tmp_stoks
    INNER JOIN stocks ON stocks.id = tmp_stoks.stoks_id
        INNER JOIN users ON users.id = tmp_stoks.admin_id
    LEFT OUTER JOIN satuans AS cat ON cat.id = stocks.cat LEFT OUTER JOIN satuans AS subcat ON subcat.id = stocks.sub_cat WHERE tmp_stoks.stoks_id = :id ORDER BY tmp_stoks.created_at DESC";

    $stmt = $config->runQuery($sql);
    $stmt->execute(array(':id' => $id));
    $data = array();

    while($rows = $stmt->fetch(PDO::FETCH_LAZY)){
        $data[] = array(
            'id'    => $rows->id,
            'category'    => $rows->category,
            'subcategory'    => $rows->subcategory,
            'qty'    => $rows->qty,
            'admin'    => $rows->admin_id,
            'created'    => $rows->created_at,
            'nama_barang'    => $rows->nama_barang,
            'name'    => $rows->name,
            'ket'       => $rows->ket
        );
    }
    echo json_encode($data);
}

if($_GET['type'] == 'addStocks')
{
    $z  = $_POST['category'];
    $m  = !empty($_POST['sub_category']) ? $_POST['sub_category'] : '0';
    $g  = $_POST['admin'];
    $a  = $_POST['title'];
    $c  = $_POST['quantity'];
    $d  = $_POST['satuan'];
    $e  = $_POST['harga'];
    $f  = $_POST['keterangan'];
    $x  = $_POST['tmpQty'];

    
    $sql = "INSERT INTO stocks (cat, sub_cat, nama_barang, qty, satuan, harga, ket, admin_id) VALUES (:z, :x, :a, :b, :c, :e, :f, :g)";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':z'    => $z,
        ':x'    => $m,
        ':a'    => $a, 
        ':b'    => $c, 
        ':c'    => $d, 
        ':e'    => $e,
        ':f'    => $f,
        ':g'    => $g
    ));

    if($stmt){
        echo $config->actionMsg('c', 'stocks');
    }else{
        echo 'Failed';
    }
}
if($_GET['type'] == 'updateStocks')
{
    $g  = $_POST['admin'];
    $h  = $_POST['idStocks'];
    $a  = $_POST['title'];
    $c  = $_POST['quantity'];
    $d  = $_POST['satuan'];
    $e  = $_POST['harga'];
    $f  = $_POST['keterangan'];
    $x  = $_POST['tmpQty'];

    // $b = array($a, $b, $c, $d, $e, $f, $g, $tgl);
    // print_r($b);
    //cek stok
    $total = $x - $c; echo $total;
    if($total <= 0 ){
        echo "Stok melebih batas persedian";
    }else{

        $sql = "UPDATE stocks SET qty = :c WHERE id = :h";
        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':c'    => $total,
            ':h'    => $h
        ));

        if($stmt){
            $query = "INSERT INTO tmp_stoks (stoks_id, qty, ket, admin_id) VALUES (:id, :qty, :ket, :adm)";
            $input = $config->runQuery($query);
            $input->execute(array(
                ':id'   => $h,
                ':ket'  => $f,
                ':qty'  => $c,
                ':adm'  => $g
            ));
            if($input){
                echo $config->actionMsg('u', 'stocks');
            }else{
                echo 'Failed';
            }
            
        }else{
            echo 'Failed';
        }
    }
}


if($_GET['type'] == 'editStocks'){
    $a = $_POST['idStock'];

    $stmt = $config->runQuery('SELECT stocks.id, stocks.nama_barang, stocks.qty, stocks.satuan, stocks.harga, stocks.ket, stocks.created_at, stocks.admin_id, users.name, cat.content AS category, subcat.category AS subcategory FROM stocks
    INNER JOIN users ON users.id = stocks.admin_id LEFT OUTER JOIN satuans AS cat ON cat.id = stocks.cat LEFT OUTER JOIN satuans AS subcat ON subcat.id = stocks.sub_cat WHERE stocks.id = :id');
    $stmt->execute(array(':id' => $a));
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());
}

if($_GET['type'] == 'addBelanja')
{
    $a = $_POST['category'];
    $b = !empty($_POST['subcategory']) ? $_POST['subcategory'] : '0';
    $c = $_POST['title'];
    $d = $_POST['quantity'];
    $e = $_POST['harga'];
    $f = $_POST['satuan'];
    $g = $_POST['keterangan'];
    $h = $config->getDate('Y-m-d H:m:s');
    $i = $_POST['admin'];

    

    $sql = "INSERT INTO kas_outs (type, sub_type, nama, qty, harga, satuan, ket, created_at, admin_id) VALUES (:a, :b, :c, :d, :e, :f, :g, :h, :i)";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':a'    => $a,
        ':b'    => $b,
        ':c'    => $c,
        ':d'    => $d,
        ':e'    => $e,
        ':f'    => $f,
        ':g'    => $g,
        ':h'    => $h,
        ':i'    => $i
    ));
    if($stmt)
    {
        echo $config->actionMsg('c', 'kas_outs');
    }else{
        echo "Failed";
    }

//    $f = array($a, $b, $c, $d);
//    print_r($f);
}

if($_GET['type'] == 'delBelanja')
{
    $a = $_POST['admin'];
    $b = $_POST['keterangan'];

    $stmt = $config->delRecord('kas_outs', 'id', $b);
    if($stmt){
        echo $config->actionMsg('d', 'kas_outs');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'delStock')
{
    $a = $_POST['admin'];
    $b = $_POST['keterangan'];

    $stmt = $config->delRecord('stocks', 'id', $b);
    if($stmt){
        echo $config->actionMsg('d', 'stocks');
    }else{
        echo 'Failed!';
    }
}