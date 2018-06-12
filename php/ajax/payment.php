<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 10/04/2018
 * Time: 14.09
 */
session_start();
require '../../config/api.php';
$config = new Admin();
$admin = $config->adminID();

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
    $tipe = 'kredit';
    $status = '3';

    $query = "SELECT total FROM kas_ins WHERE id = :kodeIDnya";
    $cek = $config->runQuery($query);
    $cek->execute(array(':kodeIDnya' => '3'));
    $row = $cek->fetch(PDO::FETCH_LAZY);
    $idKas = '3';
    $totalAwal = $row['total'];

    if($totalAwal > 0){
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
        $logs = $config->saveLogs($reff, $admin, 'c', 'kredit pengeluaran');
        if($stmt)
        {
            echo $config->actionMsg('c', 'kas_outs');
            $totalSaldoAkhir = $totalAwal - $totalBelanja;
            $query2 = "UPDATE kas_ins SET total = :totalAkhir WHERE id = :id";
            $update = $config->runQuery($query2);
            $update->execute(array(
                ':totalAkhir'   => $totalSaldoAkhir,
                ':id'           => $idKas
            ));
            if($update){
                echo $config->actionMsg('u', 'kas_ins');
                
                $logs = $config->saveLogs($idKas, $admin, 'u', 'update kas besar');

                    $sql3 = "INSERT INTO kas_ins (parent_id, types, title, total, ket, admin_id, status) VALUES (:parent, :tipe, :title, :total, :ket, :admin, :status)";
                    $stmt3 = $config->runQuery($sql3);
                    $stmt3->execute(array(
                        ':parent'   => $idKas,
                        ':tipe'     => 'kredit',
                        ':title'    => $c,
                        ':total'    => $totalBelanja,
                        ':ket'      => $g,
                        ':admin'    => $i,
                        ':status'   => '3'
                    ));
                    $reff = $config->lastInsertId();
                    $logs = $config->saveLogs($reff, $admin, 'c', 'tambah kredit kas produksi');
                    if($stmt3){
                        echo $config->actionMsg('c', 'kas_ins');
                    }
            }else{

                echo 'failed';
            }
        }else{
            echo "Failed";
        }
    }else{
        echo 'Maaf Saldo Anda tidak memadai. Silahkan isi Saldo DLL terlebih dahulu!';
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
        
        $logs = $config->saveLogs($b, $admin, 'd', 'hapus belanjaan');
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'reportKasOut')
{
    $a = $_POST['admin'];
    $b = $_POST['users'];

    $tanggal = $config->getDate('Y-m-d H:m:s');
    $sql = "SELECT id FROM kas_outs WHERE admin_id = :admin AND status = '' ";
    $total = $config->runQuery($sql);
    $total->execute(array(
        ':admin' => $b
    ));
    if($total->rowCount() > 0){

        $stmt = $config->runQuery("UPDATE kas_outs SET report_at = :tanggal,  status = '1' WHERE admin_id = :adm AND status = '' ");
        $stmt->execute(array(
            ':tanggal' => $tanggal,
            ':adm' => $b
        ));
        if($stmt){
            echo '1';
        }
    }else{
        echo '0';
    }
    // $sql = "SELECT SUM(qty * harga) as total FROM kas_outs WHERE admin_id = :admin AND status = '' ";
    // $total = $config->runQuery($sql);
    // $total->execute(array(
    //     ':admin' => $b
    // ));
    // if($total->rowCount() > 0){
    //     $info = $total->fetch(PDO::FETCH_LAZY);

    //     $total = $info['total'];

    //     $stmt = $config->runQuery("UPDATE kas_outs SET report_at = :tanggal,  status = '1' WHERE admin_id = :adm AND status = '' ");
    //     $stmt->execute(array(
    //         ':tanggal' => $tanggal,
    //         ':adm' => $b
    //     ));
    //     if($stmt){
    //         $query = "INSERT INTO kas_outs (nama, harga, ket, created_at, admin_id, status) VALUES (:a, :b, :c, :d, :e, :f)";
    //         $input = $config->runQuery($query);
    //         $input->execute(array(
    //             ':a'    => $b,
    //             ':b'    => $total,
    //             ':c'    => 'report',
    //             ':d'    => $tanggal,
    //             ':e'    => $a,
    //             ':f'    => '0'
    //         ));
    //         if($input){
    //             echo '1';
    //         }else{
    //             echo '0';
    //         }
    //     }else{
    //         echo '0';
    //     }
    // }else{
    //     echo '2';
    // }

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
    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', 'menambah kas produksi');
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
    $e = $_POST['price'];
    $f = $_POST['ket'];
    $tgl = $config->getDate('Y-m-d H:m:s');

    $query = "SELECT total FROM kas_ins WHERE id = :kodeIDnya";
    $cek = $config->runQuery($query);
    $cek->execute(array(':kodeIDnya' => '2'));
    $row = $cek->fetch(PDO::FETCH_LAZY);
    $idKas = '2';
    $totalAwal = $row['total'];

        $sql = "INSERT INTO pay_kurirs (no_trx, kurir_id, charge_id, total, created_at, admin_id) VALUES (:trx, :a, :b, :total, :c, :d)";
        $stmt = $config->runQuery($sql);
        $stmt->execute(array(
            ':trx'  => $d,
            ':a'    => $b,
            ':b'    => $c,
            ':total'=> $e,
            ':c'    => $tgl,
            ':d'    => $a
        ));
        $reff = $config->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'bayar kurir');
        if($stmt){
            
            echo $config->actionMsg('c', 'pay_kurirs');
$kasBesar = $config->runQuery("INSERT INTO kas_besar type, total, title, ket, status, admin VALUES (:a, :b, :c, :d, :e, :f, :g)");
        }else{
            echo 'Failed!';
        }

    
}
if($_GET['type'] == 'delKasIns')
{
    $a = $_POST['dataID'];
    $b = $_POST['typesID'];
    $c = $_POST['kategori'];
    $d = $_POST['totalReturn'];
    $e = $_POST['admin'];
    $tgl = $config->getDate('Y-m-d H:m:s');

    $stmt = $config->delRecord('kas_ins', 'id', $a);
    $logs = $config->saveLogs($a, $e, 'd', 'hapus belanja');
    if($stmt){
        echo $config->actionMsg('d', 'kas_ins');
        $cek = $config->runQuery("SELECT total FROM kas_ins WHERE id ='". $c ."' ");
        $cek->execute();
        $cek =  $cek->fetch(PDO::FETCH_LAZY);
        $dana = $cek['total'] + $d;
        $update = $config->runQuery("UPDATE kas_ins SET total = '". $dana ."' WHERE id = '". $c ."' ");
        $update->execute();

        if($update){
                $sql = "INSERT INTO kas_ins (types, title, total, ket, admin_id, status, created_at) VALUES (:a, :c, :b, :d, :e, :f, :tgl)";
            $stmt = $config->runQuery($sql);
            $stmt->execute(array(
                ':a'    => 'debit',
                ':c'    => 'return belanja',
                ':b'    => $d,
                ':d'    => 'return belanja',
                ':e'    => $e,
                ':f'    => $c,
                ':tgl'  => $tgl
            ));
            $reff = $config->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'refund dana belanja ');
        }
        
        

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
        
        $logs = $config->saveLogs($b, $admin, 'd', 'hapus data pembayaran kurir');
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
     WHERE pay_kurirs.kurir_id = :kurir AND pay_kurirs.status = '1' ";
    $total = $config->runQuery($sql);
    $total->execute(array(
        ':kurir' => $b
    )); 
    $info = $total->fetch(PDO::FETCH_LAZY);
    if($info['id'] > 0){
        

        $total = $info['total']; 

        $stmt = $config->runQuery("UPDATE pay_kurirs SET status = '2', report_at = :report WHERE kurir_id = :kurir ");
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
            $reff = $config->lastInsertId();
                        $logs = $config->saveLogs($reff, $admin, 'c', 'bayar kurir');
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
if($_GET['type'] == 'kasBesar')
{
   $a = $_POST['tipe'];
   $b = $_POST['biaya'];
   $c = $_POST['judul'];
   $d = $_POST['keterangan'];
   $e = $_POST['admin'];
   $f = $_POST['status'];
   $tgl = $config->getDate('Y-m-d H:m:s');

    $sql = "INSERT INTO kas_besar (type, total, title, ket, status, admin_id) VALUES (:a, :b, :c, :d, :f, :e)";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(
        ':a'    => $a,
        ':b'    => $b,
        ':c'    => $c,
        ':d'    => $d,
        ':f'    => $f,
        ':e'    => $e
    ));
    $reff = $config->lastInsertId();
    $logs = $config->saveLogs($reff, $admin, 'c', "insert ". $a ." kas besar");
    $cek = $config->runQuery("SELECT total FROM kas_ins WHERE id = :datas");
    $cek->execute(array(':datas' => $f));
    $cc = $cek->fetch(PDO::FETCH_LAZY);
    $kasAwal = $cc['total'];

    if($stmt){
        if($a == 'kredit'){
            $sql = "INSERT INTO kas_ins (types, title, total, ket, admin_id, status, created_at) VALUES (:a, :c, :b, :d, :e, :f, :tgl)";
            $stmt = $config->runQuery($sql);
            $stmt->execute(array(
                ':a'    => 'debit',
                ':c'    => $c,
                ':b'    => $b,
                ':d'    => $d,
                ':e'    => $e,
                ':f'    => $f,
                ':tgl'  => $tgl
            ));
            $reff = $config->lastInsertId();
                $logs = $config->saveLogs($reff, $admin, 'c', 'debit kas type: debit ');
            if($stmt){
                
                echo $config->actionMsg('c', 'kas_ins');
                $kasAkhirTotal = $kasAwal + $b;
                $query3 = "UPDATE kas_ins SET total = :total WHERE id = :idnya";
                $update2 = $config->runQuery($query3);
                $update2->execute(array(
                    ':total' => $kasAkhirTotal,
                    ':idnya' => $f
                ));
                if($update2){
                    echo $config->actionMsg('c', 'kas_ins');
                }
            }else{
                echo 'Failed!';
            }
        }
        echo $config->actionMsg('c', 'kas_besar');
        
    }else{
        echo 'Failed!';
    }
}

if($_GET['type'] == 'delKasBesar')
{
    $a = $_POST['admin'];
    $b = $_POST['keterangan'];
    $c = $_POST['total'];
    $d = $_POST['tipe'];
    $tgl = $config->getDate('Y-m-d H:m:s');

    if($d == '0'){
        $stmt = $config->delRecord('kas_besar', 'id', $b);
        if($stmt){
            
            $logs = $config->saveLogs($b, $admin, 'd', 'hapus record kas besar');
            echo $config->actionMsg('d', 'kas_besar');
        }
    }else{
            $cek = $config->runQuery('SELECT total FROM kas_ins WHERE id = :id');
            $cek->execute(array(':id'   => $d));
            $data = $cek->fetch(PDO::FETCH_LAZY);
                    
            $danaAkhir = $data['total'] - $c;
            if($danaAkhir >= 0){
                    $updateKas = $config->runQuery('UPDATE kas_ins SET total = :total WHERE id = :idnya ');
                    $updateKas->execute(array(
                        ':total'    => $danaAkhir,
                        ':idnya'   => $d
                    ));

                    if($updateKas){
                        echo $config->actionMsg('u', 'kas_ins');

                            $stmt = $config->delRecord('kas_besar', 'id', $b);
                            $logs = $config->saveLogs($b, $admin, 'd', 'hapus record kas besar');
                            if($stmt){
                                echo $config->actionMsg('d', 'kas_besar');
                                    $kurang = $config->runQuery("INSERT INTO kas_ins (parent_id, types, title, total, ket, admin_id, status) 
                                    VALUES (:a, :b, :c, :d, :e, :f, :g)");
                                    $kurang->execute(array(
                                        ':a'    => $d,
                                        ':b'    => 'kredit',
                                        ':c'    => 'return kas masuk',
                                        ':d'    => $c,
                                        ':e'    => 'return kas to kas_besar',
                                        ':f'    => $a,
                                        ':g'    => $d
                                    ));

                                    if($kurang){
                                        echo $config->actionMsg('c', 'kas_ins');
                                        
                                    }
                            }else{
                                echo 'Failed!';
                            }
                    }

            }else{
                echo 'Delete tidak bisa dijalankan, dikarenakan Dana kurang memadai.';
            }
    }

    
     
}
if($_GET['type'] == 'returnKas'){
    $a = $_POST['id'];
    $b = $_POST['total'];
    $c = $_POST['adm'];

    $types = 'DLL';
    if($a == '1'){
        $types = "PRODUKSI";
    }elseif($a == '2'){
        $types = "KURIR";
    }
    $stmt = $config->runQuery("UPDATE kas_ins SET total = 0 WHERE id = :id");
    $stmt->execute(array(':id'  => $a));
    if($stmt){
        
        $logs = $config->saveLogs($a, $admin, 'u', "return kas produksi type: " .$types. "");
        echo $config->actionMsg('u', 'kas_ins');

        $input = $config->runQuery("INSERT INTO kas_besar (type, total, title, ket, admin_id) VALUES (:a, :b, :c, :d, :e)");
        $input->execute(array(
            ':a'    => 'debit',
            ':b'    => $b,
            ':c'    => 'return kas',
            ':d'    => $types,
            ':e'    => $c
        ));
        $reff = $input->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'tambah kas besar');
        if($input){
            
            echo $config->actionMsg('c', 'kas_besar');

            $kurang = $config->runQuery("INSERT INTO kas_ins (parent_id, types, title, total, ket, admin_id, status) 
            VALUES (:a, :b, :c, :d, :e, :f, :g)");
            $kurang->execute(array(
                ':a'    => $a,
                ':b'    => 'kredit',
                ':c'    => 'return kas',
                ':d'    => $b,
                ':e'    => 'return kas to kas_besar',
                ':f'    => $c,
                ':g'    => $a
            ));
            $reff = $kurang->lastInsertId();
            $logs = $config->saveLogs($reff, $admin, 'c', 'tambah dana kas produksi');
            if($kurang){
                
                echo $config->actionMsg('c', 'kas_ins');
            }
        }
    }
}

if($_GET['type'] == 'bayarParkir'){
    
    $a = $_POST['biaya'];
    $b = $_POST['nama_parkiran'];
    $c = $_POST['id_record'];

    $cek = $config->getData('total', 'pay_kurirs', "id = '". $c ."' "); //get total
    

    $sql = $config->runQuery("UPDATE pay_kurirs SET remarks = 'parkir: ". $b ."', weight = '". $a ."' WHERE id = '". $c ."' ");
    $sql->execute();
   
    $logs = $config->saveLogs($c, $admin, 'u', 'tambah remarks parkir!');
    if ($sql) {
        # code...
        echo $config->actionMsg('u', 'pay_kurirs');

    }
}

if($_GET['type'] == 'remarksDelivery'){
    
    $a = $_POST['id_record'];
    $b = $_POST['types'];

    $cek = $config->getData('total', 'pay_kurirs', "id = '". $a ."' "); //get total
    

    $types = 'standing';
    $charge = '20000';
    if($b == '3'){
        $types = 'time charge';
        $charge = '10000';
    }

    
    $sql = $config->runQuery("UPDATE pay_kurirs SET remarks = '". $types ."', weight = '". $charge ."' WHERE id = '". $a."' ");
    $sql->execute();
   
    $logs = $config->saveLogs($b, $admin, 'u', 'tambah remarks parkir!');
    if ($sql) {
        # code...
        echo $config->actionMsg('u', 'pay_kurirs');

    }
}

if($_GET['type'] == 'payDelivery'){
    
    $a = $_POST['id_record'];

    $cek = $config->getData('remarks, weight, total', 'pay_kurirs', "id = '". $a ."' "); //get keterangan

    $sql = $config->runQuery("UPDATE pay_kurirs SET status = '1' WHERE id = '". $a."' ");
    $sql->execute();
    
    $total = $cek['weight'] + $cek['total'];
    $logs = $config->saveLogs($a, $admin, 'u', 'paid kurir!');
    if ($sql) {
        # code...
        echo $config->actionMsg('u', 'pay_kurirs');

        $kasBesar = $config->runQuery("INSERT INTO kas_besar (type, total, title, ket, status, admin_id) VALUES (:a, :b, :c, :d, :e, :f)");
        $kasBesar->execute(array(
            ':a' => 'kredit',
            ':b' => $total,
            ':c' => 'pay_kurirs',
            ':d' => $cek['remarks'],
            ':e' => '2',
            ':f' =>  $admin
        ));

        $reff = $config->lastInsertId();
        $logs = $config->saveLogs($reff, $admin, 'c', "insert pay_kurirs kas besar");


    }
}