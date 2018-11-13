<?php
/**
 * Created by PhpStorm.
 * User: small-project
 * Date: 14/04/2018
 * Time: 01.16
 */

session_start();
require '../../config/api.php';
$config = new Admin();
$admin = $config->adminID();


if($_GET['type'] == 'pay-kurir')
{
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];
    
    if(isset($_POST['date_range'])){
        $daterange = $_POST['date_range'];
        $month = '';   
    }else{
        $daterange = '';
        // $month = 'AND MONTH(pay_kurirs.created_at) = MONTH(CURRENT_DATE())
        // AND YEAR(pay_kurirs.created_at) = YEAR(CURRENT_DATE())';
        $month = '';
        // $month = 'AND DATE(pay_kurirs.created_at) = DATE(NOW())';
    }

    $databox = '';
    if(isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        // echo $_POST['search']['value'];
        $databox = 'AND (pay_kurirs.no_trx LIKE "%'. $_POST['search']['value'] . '%" OR kurirs.nama_kurir LIKE "%'. $_POST['search']['value'] . '%" OR delivery_charges.price LIKE "%'. $_POST['search']['value'] . '%")  OR (villages.name LIKE "%'.$_POST['search']['value'].'%") ';
    }

    $payCharge = " SELECT pay_kurirs.id as payChargeID, pay_kurirs.no_trx, pay_kurirs.kurir_id, pay_kurirs.charge_id, pay_kurirs.remarks, pay_kurirs.total, pay_kurirs.weight, pay_kurirs.status, pay_kurirs.created_at, kurirs.nama_kurir, delivery_charges.price, villages.id as villagesid, villages.name, users.name as admin, transaction.delivery_date FROM pay_kurirs INNER JOIN kurirs ON kurirs.id = pay_kurirs.kurir_id
    INNER JOIN delivery_charges ON delivery_charges.id = pay_kurirs.charge_id
    INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan
    INNER JOIN users ON users.id = delivery_charges.admin_id 
    LEFT JOIN transaction ON transaction.transactionID = pay_kurirs.no_trx
    WHERE pay_kurirs.status != '2' ";
    
    // $totalPembayaran = $config->runQuery()
    $Pembayaran = "SELECT SUM(total) AS TOTAL FROM pay_kurirs WHERE pay_kurirs.status != '2' ";
    $SUM = "SELECT SUM(total) AS TOTAL FROM pay_kurirs WHERE pay_kurirs.status != '2' ";
    // $totalPembayaran = $config->getData('SUM(total) as TOTAL', 'pay_kurirs', "pay_kurirs.status != '2' ");
    // $totalPembayaran = $totalPembayaran['TOTAL'];
    //print_r($request);
    $colom = array(
        0   => 'nama_kurir',
        1   => 'no_trx',
        2   => 'name',
        3   => 'delivery_date',
        4   => 'remarks',
        5   => 'total',
        6   => 'weight',
        7   => 'status',
        8   => 'created_at',
        9   => 'nama_kurir',
        10   => 'price',
        11   => 'name',
        12   => 'admin'
    );
    $orderby = 'ORDER BY pay_kurirs.created_at DESC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }
    $payCharge .=$databox;
    $Pembayaran .=$databox;
    $SUM .=$databox;
    $colom = array(
        0   => 'payChargeID',
        1   => 'no_trx',
        2   => 'kurir_di',
        3   => 'charge_id',
        4   => 'remarks',
        5   => 'total',
        6   => 'weight',
        7   => 'status',
        8   => 'created_at',
        9   => 'nama_kurir',
        10   => 'price',
        11   => 'name',
        12   => 'admin'
    );
    // var_dump($payCharge);
    $stmt = $config->runQuery($payCharge);
    $stmt->execute();
    $totalData = $stmt->rowCount();
    $totalFilter = $totalData;
    
    $totalPerKurir = 0;
    if( $search != 'no' ){ //age
        $kurir = $_POST['kurir_id'];

        if($kurir == 0) {
            $kurir = '';
        } else {
            $kurir = "AND pay_kurirs.kurir_id = ". $kurir;
        }
        
        $rangeArray = explode("_",$daterange); 
        $startDate = $rangeArray[0]. ' 00:00:00';
        $endsDate = $rangeArray[1]. ' 23:59:59';
        $payCharge.= $kurir." AND ( pay_kurirs.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' ) ". $orderby;
        $stmt = $config->runQuery($payCharge);
        $stmt->execute(); 
        $totalFilter = $stmt->rowCount();

        $Pembayaran.= $kurir." AND ( pay_kurirs.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' )";
        $stmt2 = $config->runQuery($Pembayaran);
        $stmt2->execute();
        
        $SUM.= " AND ( pay_kurirs.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' )";
        $stmt3 = $config->runQuery($SUM);
        $stmt3->execute();
        
        $totalPerKurir = $config->getData('SUM(total) as TOTAL', 'pay_kurirs', "pay_kurirs.status != '2' ". $month . $kurir ." AND ( pay_kurirs.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' ) ");
        
        // var_dump($totalPerKurir);
        $totalPerKurir = $totalPerKurir['TOTAL'];

    }
        $stmt2 = $config->runQuery($Pembayaran);
        $stmt2->execute();
        $stmt3 = $config->runQuery($SUM);
        $stmt3->execute();

        $payCharge.= " LIMIT ".$request['start']." ,".$request['length']." ";
        // var_dump($payCharge);
        $stmt = $config->runQuery($payCharge);
        $stmt->execute(); 
   
    $data = array();
    // 9 1 11 4 10 12 7
    while ($row = $stmt->fetch(PDO::FETCH_LAZY)){
        $remarks = '<span class="badge badge-secondary">unset</span>';
        $total = '<span class="badge badge-secondary">unset</span>';
        $styleRemarks = "";
        if(!empty($row['remarks'])){
            $remarks = $row['remarks'];
            $total = $config->formatPrice($row['weight']);
            $styleRemarks = "disabled";
        }

        if(!empty($row['remarks']) || !empty($row['status'])){
            $styleRemarks = 'disabled';
        }

        if(empty($row['status'])){
            $stPay = '';
            $stDel = '';
            // $styleRemarks = '';
            $payy = "UPAID";
        }else{
            $stPay = 'disabled';
            $stDel = 'disabled';
            
            $payy = "PAID";
        }

        $weight = $row['weight'];
        if(empty($row['weight'])){
           $weight = 0;
        }
        $subtotal = $row['price'] + $weight;

        $pay = '<button type="button" class="btn btn-sm btn-warning" style="text-transform: uppercase; font-size: 10px; font-weight: 500;" data-toggle="tooltip" data-placement="top" title="Pay Charge" onclick="payDelivery('. $row["payChargeID"] .')" '. $stPay .' > '. $payy .' </button>';
        $del = '
        <button type="button"  class="btn btn-sm btn-danger" onclick="delPayCharge('. $row['payChargeID'] .')" style="text-transform: uppercase; font-size: 10px; font-weight: 500;" data-toggle="tooltip" data-placement="top" title="delete"  '. $stDel .'>  <span class="fa fa-trash"></span> </button>
        ';
        $remk = '
        <div class="btn-group">
            <button style="text-transform: uppercase; font-size: 10px; font-weight: 500;" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-toggle="tooltip" data-placement="top" title="Remarks" '. $styleRemarks .'>
            <span class="fa fa-tasks"></span>
            </button>
            <div class="dropdown-menu">
            <a class="dropdown-item" href="#" onclick="remarks(1, '. $row['payChargeID'] .')">parking</a>
            <a class="dropdown-item" href="#" onclick="remarks(2, '. $row['payChargeID'] .')">standing</a>
            <a class="dropdown-item" href="#" onclick="remarks(3, '. $row['payChargeID'] .')">time remarks</a>
            </div>
        </div>
        ';
        $button = $remk . $pay . $del;


        $Kelurahan = $row['name'];
        if($row['villagesid'] == 9415073004) {
            $Kelurahan = '
        <button type="button"  class="btn btn-sm btn-danger" onclick="changekelurahan(\''. $row['no_trx'] .'\')" style="text-transform: uppercase; font-size: 10px; font-weight: 500;" data-toggle="tooltip" data-placement="top" title="Change Kelurahan ?"> '.$row['name'].' </button>
        ';
        }
        
        $subdata = array();
        // $subdata[]  = $row[0];
        $subdata[]  = $row['nama_kurir'];
        $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['no_trx'] .'" target="_blank">'.$row['no_trx'].'</a>';
        // $subdata[]  = $row[2];
        // $subdata[]  = $row[3];
        $subdata[]  = $Kelurahan;
        $subdata[]  = $row['delivery_date'];
        $subdata[]  = $remarks;
        // $subdata[]  = $row[5];
        // $subdata[]  = $row[6];
        $subdata[]  = $total;
        // $subdata[]  = $row[8];
        
        $subdata[]  = $config->formatPrice($row['price']);
        
        $subdata[]  = $config->formatPrice($subtotal);
        $subdata[]  = $button;
        array_push($data, $subdata);
        //$data = $subdata;
    }
    $datapembayaran = $stmt2->fetch(PDO::FETCH_LAZY);
    $SUM = $stmt3->fetch(PDO::FETCH_LAZY);
    $selisihPembayaran = $SUM['TOTAL'] - $datapembayaran['TOTAL'];
    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $data,
        'totalData'        => $config->formatPrice($SUM['TOTAL']),
        'totalKurir'         => $config->formatPrice($datapembayaran['TOTAL']),
        'subtotal'          => $config->formatPrice($selisihPembayaran)
    );
    echo json_encode($json_data);
}