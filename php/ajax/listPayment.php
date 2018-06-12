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

if($_GET['type'] == 'kasOut'){
    $outKas = $config->ProductsJoin('kas_outs.id, kas_outs.nama, kas_outs.total, kas_outs.ket, kas_outs.created_at, kas_outs.status, users.name', 'kas_outs',
        'INNER JOIN users ON users.id = kas_outs.admin_id', "WHERE DATE(kas_outs.created_at)= CURDATE() AND kas_outs.status ='' ");

    $request = $_REQUEST;
    $colom = array(
        0   => 'id',
        1   => 'nama',
        2   => 'total',
        3   => 'ket',
        4   => 'created_at',
        5   => 'status'
    );

    $totalData = $outKas->fetchAll();
    $totalData = count($totalData);
    
    $logs = $config->saveLogs('0', $admin, 'f', 'get data kas_outs');

    $data = array();

    while ($row = $outKas->fetch(PDO::FETCH_LAZY)){
        $subdata = array();
        $subdata[]  = $row[0];
        $subdata[]  = $row[1];
        $subdata[]  = $row[2];
        $subdata[]  = $row[3];
        $subdata[]  = $row[4];
        $subdata[]  = $row[5];
        $data = $subdata;
    }

    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalData),
        'data'              => $data
    );
    echo json_encode($json_data);
}

if($_GET['type'] == 'pay-kurir')
{
    $payCharge = " SELECT pay_kurirs.id as payChargeID, pay_kurirs.no_trx, pay_kurirs.kurir_id, pay_kurirs.charge_id, pay_kurirs.remarks, pay_kurirs.total, pay_kurirs.weight, pay_kurirs.status, pay_kurirs.created_at, kurirs.nama_kurir, delivery_charges.price, villages.name, users.name as admin FROM pay_kurirs INNER JOIN kurirs ON kurirs.id = pay_kurirs.kurir_id
    INNER JOIN delivery_charges ON delivery_charges.id = pay_kurirs.charge_id
    INNER JOIN villages ON villages.id = delivery_charges.id_kelurahan
    INNER JOIN users ON users.id = delivery_charges.admin_id WHERE pay_kurirs.status != '2' ";

    $request = $_REQUEST;
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

    $stmt = $config->runQuery($payCharge);
    $stmt->execute();
    $totalData = $stmt->rowCount();

    $totalFilter = 0;
    if( !empty($request['columns'][6]['search']['value']) ){ //age
        $rangeArray = explode("-",$request['columns'][6]['search']['value']);
        $startDate = $rangeArray[0];
        $endsDate = $rangeArray[1];
        $payCharge.=" AND ( created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' ) ";

        $stmt = $config->runQuery($payCharge);
        $stmt->execute();
        $totalData = $stmt->fetchAll();
        $totalFilter = count($totalFilter);
    }

    $payCharge.="ORDER BY pay_kurirs.created_at DESC";
    $data = array();
    // 9 1 11 4 10 12 7
    while ($row = $stmt->fetch(PDO::FETCH_LAZY)){
        $remarks = '<span class="badge badge-secondary">unset</span>';
        $total = '<span class="badge badge-secondary">unset</span>';
        if(!empty($row['remarks'])){
            $remarks = $row['remarks'];
            $total = $config->formatPrice($row['weight']);
        }
        $subtotal = $row['price'] + $row['weight'];

        $pay = '<button type="button" class="btn btn-sm btn-warning" style="text-transform: uppercase; font-size: 10px; font-weight: 500;" data-toggle="tooltip" data-placement="top" title="Pay Charge" ><span class="fa fa-rouble"></span></button>';
        $del = '
        <button type="button"  class="btn btn-sm btn-danger delPayCharge" style="text-transform: uppercase; font-size: 10px; font-weight: 500;" data-toggle="tooltip" data-placement="top" title="delete">  <span class="fa fa-trash"></span> </button>
        ';
        $remk = '
        <div class="btn-group">
                                          <button style="text-transform: uppercase; font-size: 10px; font-weight: 500;" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-toggle="tooltip" data-placement="top" title="Remarks">
                                            <span class="fa fa-tasks"></span>
                                          </button>
                                          <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#" onclick="remarks(1, )">parking</a>
                                            <a class="dropdown-item" href="#" onclick="remarks(2, )">standing</a>
                                            <a class="dropdown-item" href="#" onclick="remarks(3, )">time remarks</a>
                                          </div>
                                        </div>
        ';
        $button = $remk . $pay . $del;

        $subdata = array();
        // $subdata[]  = $row[0];
        $subdata[]  = $row['nama_kurir'];
        $subdata[]  = $row['no_trx'];
        // $subdata[]  = $row[2];
        // $subdata[]  = $row[3];
        $subdata[]  = $row['name'];
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

    $json_data = array(
        'draw'              => intval($request),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $data
    );
    echo json_encode($json_data);
}