<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 10/04/2018
 * Time: 14.09
 */
require '../../config/api.php';
$config = new Admin();

if($_GET['type'] == 'kasOut')
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
if($_GET['type'] == 'delKasOut')
{
    $a = $_POST['admin'];
    $b = $_POST['keterangan'];

    $stmt = $config->delRecord('kas_outs', 'id', $b);
    if($stmt){
        echo 'Record Pengeluaran Berhasil di Hapus!';
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'reportKasOut')
{
    $a = $_POST['admin'];
    $b = $_POST['users'];

    $tanggal = $config->getDate('Y-m-d H:m:s');

    $sql = "SELECT SUM(qty * harga) as total FROM kas_outs WHERE admin_id = :admin AND status = '' ";
    $total = $config->runQuery($sql);
    $total->execute(array(
        ':admin' => $b
    ));
    if($total->rowCount() > 0){
        $info = $total->fetch(PDO::FETCH_LAZY);

        $total = $info['total'];

        $stmt = $config->runQuery("UPDATE kas_outs SET report_at = :tanggal,  status = '1' WHERE admin_id = :adm AND status = '' ");
        $stmt->execute(array(
            ':tanggal' => $tanggal,
            ':adm' => $b
        ));
        if($stmt){
            $query = "INSERT INTO kas_outs (nama, harga, ket, created_at, admin_id, status) VALUES (:a, :b, :c, :d, :e, :f)";
            $input = $config->runQuery($query);
            $input->execute(array(
                ':a'    => $b,
                ':b'    => $total,
                ':c'    => 'report',
                ':d'    => $tanggal,
                ':e'    => $a,
                ':f'    => '0'
            ));
            if($input){
                echo '1';
            }else{
                echo '0';
            }
        }else{
            echo '0';
        }
    }else{
        echo '2';
    }

}

if($_GET['type'] == 'addKasIn')
{
    $a = $_POST['admin'];
    $b = $_POST['title'];
    $c = $_POST['total'];
    $d = $_POST['keterangan'];
    $tgl = $config->getDate('Y-m-d H:m:s');

    $sql = "INSERT INTO kas_ins (title, total, ket, admin_id, created_at) VALUES (:b, :c, :d, :a, :tgl)";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':b'    => $b,
        ':c'    => $c,
        ':d'    => $d,
        ':a'    => $a,
        ':tgl'  => $tgl
    ));
    if($stmt){
        echo 'Tambah dana selesai di input!';
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'addPayCharge')
{
    $a = $_POST['admin'];
    $b = $_POST['namaKurir'];
    $c = $_POST['kelurahan'];
    $d = $_POST['trx'];
    $tgl = $config->getDate('Y-m-d H:m:s');

    $sql = "INSERT INTO pay_kurirs (no_trx, kurir_id, charge_id, created_at, admin_id) VALUES (:trx, :a, :b, :c, :d)";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':trx'  => $d,
        ':a'    => $b,
        ':b'    => $c,
        ':c'    => $tgl,
        ':d'    => $a
    ));
    if($stmt){
        echo $config->actionMsg('c', 'pay_kurirs');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'delPayCharge')
{
    $a = $_POST['admin'];
    $b = $_POST['id'];
    $tgl = $config->getDate('Y-m-d H:m:s');

    $stmt = $config->delRecord('pay_kurirs', 'id', $b);

    if($stmt){
        echo $config->actionMsg('d', 'pay_kurirs');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'reportPayCharge')
{
    $a = $_POST['admin'];
    $b = $_POST['kurir'];

    $tanggal = $config->getDate('Y-m-d H:m:s');

    $sql = "SELECT pay_kurirs.id, SUM(delivery_charges.price) as total FROM pay_kurirs
    INNER JOIN delivery_charges ON delivery_charges.id = pay_kurirs.charge_id
     WHERE pay_kurirs.kurir_id = :kurir AND pay_kurirs.status = '' ";
    $total = $config->runQuery($sql);
    $total->execute(array(
        ':kurir' => $b
    ));
    if($total->rowCount() > 0){
        $info = $total->fetch(PDO::FETCH_LAZY);

        $total = $info['total'];

        $stmt = $config->runQuery("UPDATE pay_kurirs SET status = '1', report_at = :report WHERE kurir_id = :kurir ");
        $stmt->execute(array(
            ':kurir' => $b,
            ':report'=> $tanggal
        ));
        if($stmt){
            $query = "INSERT INTO pay_kurirs (kurir_id, total, created_at, status, admin_id) VALUES (:a, :b, :c, :d, :e)";
            $input = $config->runQuery($query);
            $input->execute(array(
                ':a'    => $b,
                ':b'    => $total,
                ':c'    => $tanggal,
                ':d'    => '2',
                ':e'    => $a
            ));
            if($input){
                echo '1';
            }else{
                echo '0';
            }
        }else{
            echo '0';
        }
    }else{
        echo '2';
    }

}
