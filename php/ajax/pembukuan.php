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


if($_GET['type'] == 'revenue')
{
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];
    
    if(isset($_POST['date_range'])){
        $daterange = $_POST['date_range'];
        $statuspaid = $_POST['status_paid'];

        $status_paid = 'AND transaction.statusPaid = '.$statuspaid;
        if($statuspaid == 2) {
            $status_paid = '';
        } 
        $month = '';   
    }else{
        $daterange = '';
        $statuspaid = '';
        // $month = 'AND MONTH(pay_kurirs.created_at) = MONTH(CURRENT_DATE())
        // AND YEAR(pay_kurirs.created_at) = YEAR(CURRENT_DATE())';
        $month = '';
        // $month = 'AND DATE(pay_kurirs.created_at) = DATE(NOW())';
    }

    $databox = '';
    if(isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        // echo $_POST['search']['value'];
        $databox = ' (transaction.transactionID LIKE "%'. $_POST['search']['value'] . '%" OR transaction.CustomerName LIKE "%'. $_POST['search']['value'] . '%" OR users.name LIKE "%'. $_POST['search']['value'] . '%") AND';
    }

    $DataQuery = " SELECT transaction.* , (transaction.grandTotal - transaction.TotalCostPrice) / transaction.grandTotal as MP, users.name as AdminName FROM transaction
    LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID
    LEFT JOIN users on users.id = transaction.created_by WHERE transaction.statusOrder NOT IN (6, 99) AND ";

    $QueryTotal = " SELECT transaction.* , (transaction.grandTotal - transaction.TotalCostPrice) / transaction.grandTotal as MP, users.name as AdminName FROM transaction
    LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID
    LEFT JOIN users on users.id = transaction.created_by WHERE transaction.statusOrder NOT IN (6, 99) AND ";

    $price = 'SELECT SUM(TotalCostPrice) as costprice, SUM(TotalSellingPrice) as sellingprice, SUM(grandTotal) as GrandTotal from transaction WHERE transaction.statusOrder NOT IN (6, 99) AND';

    //print_r($request);
    $colom = array(
        0   => 'transactionID',
        1   => 'CustomerName',
        2   => 'AdminName',
        3   => 'created_date',
        4   => 'delivery_date',
        5   => 'statusPaid',
        6   => 'updated_date',
        7   => 'costprice',
        8   => 'sellingprice',
        9   => 'quantity',
        10   => 'MP',
    );

    $orderby = 'ORDER BY transaction.delivery_date ASC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }

    $limitstart = $_POST['start'];
    $limitend = $_POST['length'];
    $limit = 'LIMIT '.$limitstart.','.$limitend;


    $DataQuery .= $databox;
    $QueryTotal .= $databox;
    if( $search != 'no' ) { //age
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0]. ' 00:00:00';
        $endsDate = $rangeArray[1]. ' 23:59:59';

        $DataQuery .=" transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid." GROUP BY transaction.transactionID ". $orderby. ' '. $limit;

        $QueryTotal .=" transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid." GROUP BY transaction.transactionID ". $orderby;
        

        $price .=" transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ". $status_paid;
        // var_dump($price);
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);

        $costprice = $price['costprice'];
        $sellingprice = $price['sellingprice'];
        $GrandTotal = $price['GrandTotal'];

        $stmt = $config->runQuery($DataQuery);
        $stmt->execute();

        $count = $config->runQuery($QueryTotal);
        $count->execute(); 

        $totalFilter = $count->rowCount();
        $totalData = $totalFilter;

    } else {
        
        $price .=' MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE())';
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);
        
        $costprice = $price['costprice'];
        $sellingprice = $price['sellingprice'];
        $GrandTotal = $price['GrandTotal'];

        $DataQuery.=" MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE()) GROUP BY transaction.transactionID ". $orderby. ' '. $limit;

        $QueryTotal.=" MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE()) GROUP BY transaction.transactionID ". $orderby;
        // var_dump($DataQuery);
        
        $stmt = $config->runQuery($DataQuery);
        $stmt->execute();
        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;
        
        $totalPerKurir = 0;
    }
    
    
   //var_dump($stmt);
    $data = [];
    // 9 1 11 4 10 12 7frecordsTotal
    if($totalData > 0 ) {
        while ($row = $stmt->fetch(PDO::FETCH_LAZY)){

            $users = $config->getData('*', 'users', "id = '".$row['PaidBy']."' ");
            $adminuser = '';
            if($users['name'] != '') {
                $adminuser = ' By: '.$users['name'];
            }
            $statuspaid = '<span class="badge badge-secondary">UNPAID</span>';
            if($row['statusPaid'] == 1) $statuspaid = '<span class="badge badge-success">PAID</span>';
    
            $arrpaid = [
                'UNPAID',
                'PAID'
            ];
            $paydate = $config->_formatdate($row['PaidDate']);
            if(strtotime($row['PaidDate']) == false) {
                $paydate = '<span class="badge badge-secondary">unset</span>';
            }
            $subdata = array();
            // $subdata[]  = $row[0];
            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $row['CustomerName'];
            $subdata[]  = $row['AdminName'];
            $subdata[]  = $config->_formatdate($row['created_date']);
            $subdata[]  = $config->_formatdate($row['delivery_date']);
            $subdata[]  = $statuspaid.$adminuser;
            $subdata[]  = $paydate;
            $subdata[]  = $config->formatPrice($row['TotalCostPrice']);
            $subdata[]  = $config->formatPrice($row['grandTotal']);
            $subdata[]  = ceil($row['MP'] * 100).'%';
            array_push($data, $subdata);
            //$data = $subdata;
        }
    }
    
    $selisihPembayaran = 0;
    if($GrandTotal > 0) $selisihPembayaran = ($GrandTotal - $costprice) / $GrandTotal;
    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $data,
        'totalData'         => $config->formatPrice($costprice),
        'totalKurir'        => $config->formatPrice($sellingprice),
        'subtotal'          => ceil($selisihPembayaran * 100).'%'
    );
    echo json_encode($json_data);
}
if($_GET['type'] == 'piutang')
{
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];
    
    if(isset($_POST['date_range'])){
        $daterange = $_POST['date_range'];
        $statuspaid = $_POST['status_paid'];

        $month = '';   
    }else{
        $daterange = '';
        $statuspaid = '';
        // $month = 'AND MONTH(pay_kurirs.created_at) = MONTH(CURRENT_DATE())
        // AND YEAR(pay_kurirs.created_at) = YEAR(CURRENT_DATE())';
        $month = '';
        // $month = 'AND DATE(pay_kurirs.created_at) = DATE(NOW())';
    }

    $databox = '';
    if(isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        // echo $_POST['search']['value'];
        $databox = 'AND (transaction.transactionID LIKE "%'. $_POST['search']['value'] . '%" OR transaction.CustomerName LIKE "%'. $_POST['search']['value'] . '%" OR users.name LIKE "%'. $_POST['search']['value'] . '%") ';
    }

    $DataQuery = " SELECT transaction.* , (transaction.grandTotal - (transaction_details.product_cost * transaction_details.product_qty)) / transaction.grandTotal as MP, users.name as AdminName FROM transaction
    LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID
    LEFT JOIN users on users.id = transaction.created_by WHERE transaction.statusPaid = 0 AND transaction.statusOrder NOT IN (6, 99) ";

    $QueryTotal = " SELECT transaction.* , (transaction.grandTotal - (transaction_details.product_cost * transaction_details.product_qty)) / transaction.grandTotal as MP, users.name as AdminName FROM transaction
    LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID
    LEFT JOIN users on users.id = transaction.created_by WHERE transaction.statusPaid = 0 AND transaction.statusOrder NOT IN (6, 99) ";

    $price = 'SELECT SUM(transaction.grandTotal) as TotalSelling from transaction WHERE transaction.statusPaid = 0 ';

    //print_r($request);
    $colom = array(
        0   => 'transactionID',
        1   => 'CustomerName',
        2   => 'AdminName',
        3   => 'created_date',
        4   => 'delivery_date',
        5   => 'statusPaid',
        6   => 'updated_date',
        7   => 'costprice',
        8   => 'sellingprice',
        9   => 'quantity',
        10   => 'MP',
    );

    $orderby = 'ORDER BY transaction.delivery_date ASC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }

    $limitstart = $_POST['start'];
    $limitend = $_POST['length'];
    $limit = 'LIMIT '.$limitstart.','.$limitend;


    $DataQuery .= $databox;
    $QueryTotal .= $databox;
    if( $search != 'no' ) { //age
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0]. ' 00:00:00';
        $endsDate = $rangeArray[1]. ' 23:59:59';
        $status_paid = 'AND transaction.statusPaid = '.$statuspaid;
        $DataQuery .=" AND transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid." GROUP BY transaction.transactionID ". $orderby. ' '. $limit;

        $QueryTotal .=" AND transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid." GROUP BY transaction.transactionID ". $orderby;
        

        $price .="  AND transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ". $status_paid;
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);

        $GrandTotalPayment = $price['TotalSelling'];
    // var_dump($DataQuery);
        $stmt = $config->runQuery($DataQuery);
        $stmt->execute();

        $count = $config->runQuery($QueryTotal);
        $count->execute(); 

        $totalFilter = $count->rowCount();
        $totalData = $totalFilter;

    } else {
        $status_paid = 'AND transaction.statusPaid = 0';
        $price .=' AND MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE())';
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);
        
        $GrandTotalPayment = $price['TotalSelling'];

        $DataQuery.=" AND MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE()) GROUP BY transaction.transactionID ". $orderby. ' '. $limit;

        $QueryTotal.=" AND MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE()) GROUP BY transaction.transactionID ". $orderby;

        $stmt = $config->runQuery($DataQuery);
        $stmt->execute();

        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;
        
        $totalPerKurir = 0;
    }
    
    
    //    var_dump($QueryTotal);
    $data = [];
    // 9 1 11 4 10 12 7frecordsTotal
    if($totalData > 0 ) {
        while ($row = $stmt->fetch(PDO::FETCH_LAZY)){

            $users = $config->getData('*', 'users', "id = '".$row['PaidBy']."' ");
            $adminuser = '';
            if($users['name'] != '') {
                $adminuser = ' By: '.$users['name'];
            }
            $statuspaid = '<a href="javascript:;" onclick="changestatuspaid(\''.$row['transactionID'].'\')"><span class="badge badge-warning">UNPAID</span></a>';
            if($row['statusPaid'] == 1) $statuspaid = '<span class="badge badge-success">PAID</span>';
    
            $arrpaid = [
                'UNPAID',
                'PAID'
            ];
            $subdata = array();

            $datepaid = 'unset';
            if(($row['PaidDate']) != '0000-00-00 00:00:00') $datepaid = $config->_formatdate($row['PaidDate']);
            // $subdata[]  = $row[0];
            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $row['CustomerName'];
            $subdata[]  = $row['AdminName'];
            $subdata[]  = $config->_formatdate($row['created_date']);
            $subdata[]  = $config->_formatdate($row['delivery_date']);
            $subdata[]  = $statuspaid .$adminuser;
            $subdata[]  = $datepaid;
            $subdata[]  = $config->formatPrice($row['TotalCostPrice']);
            $subdata[]  = $config->formatPrice($row['grandTotal']);
            $subdata[]  = '<input type="checkbox" class="checkitem" name="piutangpaid[]" value="'.$row['transactionID'].'" id="exampleCheck1">';
            array_push($data, $subdata);
            //$data = $subdata;
        }
    }
    
    
    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $data,
        'subtotal'          => $config->formatPrice($GrandTotalPayment)
    );
    echo json_encode($json_data);
}
if($_GET['type'] == 'bonus')
{
    $request = $_REQUEST;
    $search = $_POST['is_date_search'];
    
    if(isset($_POST['date_range'])){
        $daterange = $_POST['date_range'];
        $status_paid = 'AND transaction.created_by = '.$_POST['admin_id'].'';
        if($_POST['admin_id'] == 0) {
            $status_paid = '';
        }

        $month = '';   
    }else{
        $daterange = '';
        $status_paid = '';
        // $month = 'AND MONTH(pay_kurirs.created_at) = MONTH(CURRENT_DATE())
        // AND YEAR(pay_kurirs.created_at) = YEAR(CURRENT_DATE())';
        $month = '';
        // $month = 'AND DATE(pay_kurirs.created_at) = DATE(NOW())';
    }

    $databox = '';
    if(isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
        // echo $_POST['search']['value'];
        $databox = 'AND (transaction.transactionID LIKE "%'. $_POST['search']['value'] . '%" OR transaction.CustomerName LIKE "%'. $_POST['search']['value'] . '%" OR users.name LIKE "%'. $_POST['search']['value'] . '%") ';
    }
    

    $DataQuery = " SELECT transaction.* , (transaction.grandTotal - transaction.TotalCostPrice) / transaction.grandTotal as MP, users.name as AdminName FROM transaction 
        LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID 
        LEFT JOIN users on users.id = transaction.created_by WHERE transaction.statusOrder NOT IN (6, 99) ";

    $QueryTotal = " SELECT transaction.* , (transaction.grandTotal - transaction.TotalCostPrice) / transaction.grandTotal as MP, users.name as AdminName FROM transaction 
        LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID 
        LEFT JOIN users on users.id = transaction.created_by WHERE transaction.statusOrder NOT IN (6, 99) ";

    $price = 'SELECT TotalCostPrice as costprice, TotalSellingPrice as sellingprice, grandTotal as GrandTotal from transaction WHERE transaction.statusOrder NOT IN (6, 99) AND transaction.statusPaid = 1 ';

    //print_r($request);
    $colom = array(
        0   => 'transactionID',
        1   => 'CustomerName',
        2   => 'AdminName',
        3   => 'created_date',
        4   => 'delivery_date',
        5   => 'statusPaid',
        6   => 'updated_date',
        7   => 'costprice',
        8   => 'sellingprice',
        9   => 'quantity',
        10   => 'MP',
    );


    $orderby = 'ORDER BY transaction.delivery_date ASC';
    if(isset($_POST['order'][0]['column'])) {
        $column     = $_POST['order'][0]['column'];
        $typesort   = $_POST['order'][0]['dir'];

        $orderby = 'ORDER BY '.$colom[$column].' '. $typesort;
    }

    $limitstart = $_POST['start'];
    $limitend = $_POST['length'];
    $limit = 'LIMIT '.$limitstart.','.$limitend;


    $DataQuery .= $databox;
    $QueryTotal .= $databox;
    if( $search != 'no' ) { //age
        $rangeArray = explode("_",$daterange);
        

        $startDate = $rangeArray[0]. ' 00:00:00';
        $endsDate = $rangeArray[1]. ' 23:59:59';

        $DataQuery .=" AND transaction.statusPaid = 1 AND transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid. $orderby. ' '. $limit;
        $QueryTotal .=" AND transaction.statusPaid = 1 AND transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid. $orderby;
        

        $price .=" AND transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ". $status_paid;
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);

        $costprice = $price['costprice'];
        $sellingprice = $price['sellingprice'];
        $GrandTotal = $price['GrandTotal'];

        // var_dump($DataQuery);

        $stmt = $config->runQuery($DataQuery);
        $stmt->execute();

        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;
        

    } else {
        $status_paid = 'AND transaction.created_by = 0';
        $price .=' AND MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE())';
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);
        
        $costprice = $price['costprice'];
        $sellingprice = $price['sellingprice'];
        $GrandTotal = $price['GrandTotal'];

        $QueryTotal.=" AND transaction.statusPaid = 1 AND MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE()) GROUP BY transaction.transactionID  ". $orderby;
        $DataQuery.=" AND transaction.statusPaid = 1 AND MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE()) GROUP BY transaction.transactionID  ". $orderby. ' '. $limit;
        // var_dump($DataQuery);
       $stmt = $config->runQuery($DataQuery);
        $stmt->execute();

        $stmt2 = $config->runQuery($QueryTotal);
        $stmt2->execute();
        $totalData = $stmt2->rowCount();
        $totalFilter = $totalData;
        
        $totalPerKurir = 0;
    }
    
    
   // var_dump($stmt);
    $data = [];
    // 9 1 11 4 10 12 7frecordsTotal
    if($totalData > 0 ) {
        while ($row = $stmt->fetch(PDO::FETCH_LAZY)){

            $users = $config->getData('*', 'users', "id = '".$row['PaidBy']."' ");
            $adminuser = '';
            if($users['name'] != '') {
                $adminuser = ' By: '.$users['name'];
            }
            $statuspaid = '<a href="javascript:;" onclick="changestatuspaid(\''.$row['transactionID'].'\')"><span class="badge badge-warning">UNPAID</span></a>';
            if($row['statusPaid'] == 1) $statuspaid = '<span class="badge badge-success">PAID</span>';
    
            $arrpaid = [
                'UNPAID',
                'PAID'
            ];
            $subdata = array();

            $datepaid = 'unset';
            if(($row['PaidDate']) != '0000-00-00 00:00:00') $datepaid = $config->_formatdate($row['PaidDate']);
            // $subdata[]  = $row[0];
            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $row['CustomerName'];
            $subdata[]  = $row['AdminName'];
            $subdata[]  = $config->_formatdate($row['created_date']);
            $subdata[]  = $config->_formatdate($row['delivery_date']);
            $subdata[]  = $statuspaid.$adminuser;
            $subdata[]  = $datepaid;
            $subdata[]  = $config->formatPrice($row['TotalCostPrice']);
            $subdata[]  = $config->formatPrice($row['TotalSellingPrice']);
            $subdata[]  = ceil($row['MP']*100).'%';
            array_push($data, $subdata);
            //$data = $subdata;
        }
    }
    
    $selisihPembayaran = 0;
    if($GrandTotal > 0) $selisihPembayaran = ($GrandTotal - $costprice) / $GrandTotal;
    $json_data = array(
        'draw'              => intval($request['draw']),
        'recordsTotal'      => intval($totalData),
        'recordsFiltered'   => intval($totalFilter),
        'data'              => $data,
        'totalData'         => $config->formatPrice($costprice),
        'totalKurir'        => $config->formatPrice($sellingprice),
        'subtotal'          => ceil($selisihPembayaran) * 100
    );
    echo json_encode($json_data);
}

if($_GET['type'] == 'changestatuspaid') {
    $a = $_POST['transactionID'];
    $b = $_POST['password'];
    $c = $_POST['PaidDate'];

    $cekuser = $config->getData('ID, tokenkey', 'token', " AdminID = '". $admin ."' AND Status = 0 ");
    if($cekuser) {
        if(password_verify($b, $cekuser['tokenkey'])) {

            $tokenid = $cekuser['ID'];
            $insert = $config->runQuery("INSERT INTO generatetoken (TokenID, TransactionNumber, Status, GenerateBy) VALUES (".$tokenid.",'".$a."', '0', ".$admin.") ");
            $insert->execute();

            if($insert) {
                $update = $config->runQuery("UPDATE transaction SET statusPaid = 1, PaidDate = '".$c."', PaidBy = '".$admin."' WHERE transactionID = '". $a ."'");
                $update->execute();

                echo 'Success!';
            } else {
                echo 'Error insert1';
            }
            $logs = $config->saveLogs($a, $admin, 'c', 'generatetoken dan update transaction');
        } else {
            $insert = $config->runQuery("INSERT INTO generatetoken (TransactionNumber, Status) VALUES ('".$a."', '1')");
            $insert->execute();
            
            $logs = $config->saveLogs($a, $admin, 'c', 'generatetoken dan update token');
            if($insert) {
                $cekmismatch = $config->getData('COUNT(ID) as Total', 'generatetoken', " TransactionNumber = '". $a ."' AND Status ='1' ");
                if($cekmismatch['Total'] >= 3) {

                    $update = $config->runQuery("UPDATE token SET Status = 1 WHERE AdminID = '". $admin ."'");
                    $update->execute();
                    if($update) {
                        echo 'Sorry, your key has disable. Please contact your Manager!';
                    } else {
                        echo 'Error update!';
                    }
                } else {
                    echo 'Password mismatch!';
                }
            } else {
                echo 'Error Insert!';
            }
        }
    } else {
        echo 'You have no Power here, Gandalf The Grey!';
    }
}
if($_GET['type'] == 'changestatuspaidmultiple') {
    $a = $_POST['transactionID'];
    $b = $_POST['password'];
    $c = $_POST['PaidDate'];
    $data = explode(',', $a);

    $cekuser = $config->getData('ID, tokenkey', 'token', " AdminID = '". $admin ."' AND Status = 0 ");
    if($cekuser) {
        if(password_verify($b, $cekuser['tokenkey'])) {

            foreach($data as $key => $val) {
                $tokenid = $cekuser['ID'];
                $insert = $config->runQuery("INSERT INTO generatetoken (TokenID, TransactionNumber, Status, GenerateBy) VALUES (".$tokenid.",'".$val."', '0', ".$admin.") ");
                $insert->execute();

                if($insert) {
                    $update = $config->runQuery("UPDATE transaction SET statusPaid = 1, PaidDate = '".$c."', PaidBy = '".$admin."' WHERE transactionID = '". $val ."'");
                    $update->execute();

                    echo 'Success!';
                } else {
                    echo 'Error insert1';
                }
                $logs = $config->saveLogs($val, $admin, 'c', 'generatetoken dan update transaction');
            }
        } else {
            foreach($data as $key => $val) {
                $insert = $config->runQuery("INSERT INTO generatetoken (TransactionNumber, Status) VALUES ('".$val."', '1')");
                $insert->execute();
                
                $logs = $config->saveLogs($a, $admin, 'c', 'generatetoken dan update token');
                if($insert) {
                    $cekmismatch = $config->getData('COUNT(ID) as Total', 'generatetoken', " TransactionNumber = '". $val ."' AND Status ='1' ");
                    if($cekmismatch['Total'] >= 3) {

                        $update = $config->runQuery("UPDATE token SET Status = 1 WHERE AdminID = '". $admin ."'");
                        $update->execute();
                        if($update) {
                            echo 'Sorry, your key has disable. Please contact your Manager!';
                        } else {
                            echo 'Error update!';
                        }
                    } else {
                        echo 'Password mismatch!';
                    }
                } else {
                    echo 'Error Insert!';
                }
            }
        }
    } else {
        echo 'You have no Power here, Gandalf The Grey!';
    }
}