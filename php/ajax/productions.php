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
    // $total = $x - $c; echo $total;
    // if($total <= 0 ){
    //     echo "Stok melebih batas persedian";
    // }else{

        
    // }
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
    $stok = $_POST['stok'];
    

    $query = "SELECT total FROM kas_ins WHERE id = :kodeID";
    $cek = $config->runQuery($query);
    $cek->execute(array(':kodeID' => '1'));
    $row = $cek->fetch(PDO::FETCH_LAZY);
    $idKas = '1';
    $saldoAwal = $row['total'];

    if($saldoAwal > 0 ){
        //get id kas_ins
        $totalBelanja = $d * $e;
        

        $sql = "INSERT INTO kas_outs (id_kas_ins, type, sub_type, nama, qty, harga, satuan, ket, created_at, admin_id) VALUES (:idKas, :a, :b, :c, :d, :e, :f, :g, :h, :i)";
        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':idKas'=> $idKas,
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
        $reff = $config->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'tambah belanja');
        if($stmt)
        {
            echo $config->actionMsg('c', 'kas_outs');

            if($stok == '1'){
                $sql2 = "INSERT INTO stocks (cat, sub_cat, nama_barang, qty, satuan, harga, ket, admin_id) VALUES (:a, :b, :c, :d, :e, :f, :g, :h)";
                $stmt2 = $config->runQuery($sql2);
                $stmt2->execute(array(
                    ':a'    => $a,
                    ':b'    => $b,
                    ':c'    => $c,
                    ':d'    => $d,
                    ':e'    => $f,
                    ':f'    => $e,
                    ':g'    => $g,
                    ':h'    => $i
                ));
                $reff = $config->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'tambah stock barang');
                if($stmt2){
                    echo $config->actionMsg('c', 'stocks');

                    $sql3 = "INSERT INTO kas_ins (parent_id, types, title, total, ket, admin_id, status) VALUES (:parent, :tipe, :title, :total, :ket, :admin, :status)";
                    $stmt3 = $config->runQuery($sql3);
                    $stmt3->execute(array(
                        ':parent'   => $idKas,
                        ':tipe'     => 'kredit',
                        ':title'    => $c,
                        ':total'    => $totalBelanja,
                        ':ket'      => $g,
                        ':admin'    => $i,
                        ':status'   => '1'
                    ));
                    $reff = $config->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'kredit kas produksi belanja');
                    if($stmt3){
                        $totalSaldoAkhir = $saldoAwal - $totalBelanja;
                        $query10 = "UPDATE kas_ins SET total = :totalAkhir WHERE id = :id";
                        $update = $config->runQuery($query10);
                        $update->execute(array(
                            ':totalAkhir'   => $totalSaldoAkhir,
                            ':id'           => $idKas
                        ));
                        if($update){
                            echo $config->actionMsg('u', 'kas_ins');
                        }
                    }
                }else{
                    echo 'failed';
                }

            }else{
                $sql3 = "INSERT INTO kas_ins (parent_id, types, title, total, ket, admin_id, status) VALUES (:parent, :tipe, :title, :total, :ket, :admin, :status)";
                    $stmt3 = $config->runQuery($sql3);
                    $stmt3->execute(array(
                        ':parent'   => $idKas,
                        ':tipe'     => 'kredit',
                        ':title'    => $c,
                        ':total'    => $totalBelanja,
                        ':ket'      => $g,
                        ':admin'    => $i,
                        ':status'   => '1'
                    ));
                    $reff = $config->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'kredit kas produksi belanja');
                    if($stmt3){
                        $totalSaldoAkhir = $saldoAwal - $totalBelanja;
                        $query10 = "UPDATE kas_ins SET total = :totalAkhir WHERE id = :id";
                        $update = $config->runQuery($query10);
                        $update->execute(array(
                            ':totalAkhir'   => $totalSaldoAkhir,
                            ':id'           => $idKas
                        ));
                        if($update){
                            echo $config->actionMsg('u', 'kas_ins');
                        }
                    }
            }
        }else{
            echo "Failed";
        }
    }else{
        echo 'Maaf Saldo Belanja Anda tidak memadai. Silahkan isi Saldo PRODUKSI terlebih dahulu!';
    }

    

//    $f = array($a, $b, $c, $d);
//    print_r($f);
}

if($_GET['type'] == 'delBelanja')
{
    $a = $_POST['admin'];
    $b = $_POST['keterangan'];

    $stmt = $config->delRecord('kas_outs', 'id', $b);
            $logs = $config->saveLogs($b, $admin, 'd', 'hapus belanjaan');
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
            $logs = $config->saveLogs($b, $admin, 'c', 'hapus stock barang');
    if($stmt){
        echo $config->actionMsg('d', 'stocks');
    }else{
        echo 'Failed!';
    }
}