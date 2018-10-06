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

    $DataQuery = " SELECT transaction.* , (transaction.grandTotal - (transaction_details.product_cost * transaction_details.product_qty)) / transaction.grandTotal as MP, users.name as AdminName FROM transaction
    LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID
    LEFT JOIN users on users.id = transaction.created_by ";

    $price = 'SELECT TotalCostPrice as costprice, TotalSellingPrice as sellingprice, grandTotal as GrandTotal from transaction';

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


    if( $search != 'no' ) { //age
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0]. ' 00:00:00';
        $endsDate = $rangeArray[1]. ' 23:59:59';

        $DataQuery .=" WHERE transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid." GROUP BY transaction.transactionID ORDER BY transaction.created_date DESC LIMIT ".$request['start']." ,".$request['length']." ";
        

        $price .=" WHERE transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ". $status_paid;
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);

        $costprice = $price['costprice'];
        $sellingprice = $price['sellingprice'];
        $GrandTotal = $price['GrandTotal'];

        $stmt = $config->runQuery($DataQuery);
        $stmt->execute();

        $count = $config->runQuery($DataQuery);
        $count->execute(); 

        $totalFilter = $count->rowCount();
        $totalData = $totalFilter;

    } else {
        
        $price .=' WHERE MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE())';
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);
        
        $costprice = $price['costprice'];
        $sellingprice = $price['sellingprice'];
        $GrandTotal = $price['GrandTotal'];

        $DataQuery.=" WHERE MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE()) GROUP BY transaction.transactionID ORDER BY transaction.created_date DESC LIMIT ".$request['start']." ,".$request['length']." ";
        $stmt = $config->runQuery($DataQuery);
        $stmt->execute();

        $stmt2 = $config->runQuery($DataQuery);
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

            $statuspaid = '<span class="badge badge-secondary">UNPAID</span>';
            if($row['statusPaid'] == 1) $statuspaid = '<span class="badge badge-success">PAID</span>';
    
            $arrpaid = [
                'UNPAID',
                'PAID'
            ];
            
            $subdata = array();
            // $subdata[]  = $row[0];
            $subdata[]  = '<a href="'.$config->url().'order/?p=detailtrx&trx='. $row['transactionID'] .'" target="_blank">'.$row['transactionID'].'</a>';
            $subdata[]  = $row['CustomerName'];
            $subdata[]  = $row['AdminName'];
            $subdata[]  = $config->_formatdate($row['created_date']);
            $subdata[]  = $config->_formatdate($row['delivery_date']);
            $subdata[]  = $statuspaid;
            $subdata[]  = $config->_formatdate($row['updated_date']);
            $subdata[]  = $config->formatPrice($row['TotalCostPrice']);
            $subdata[]  = $config->formatPrice($row['TotalSellingPrice']);
            $subdata[]  = ceil($row['MP']).'%';
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
        'subtotal'          => $selisihPembayaran
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

    $status_paid = 'AND transaction.statusPaid = 0';

    $DataQuery = " SELECT transaction.* , (transaction.grandTotal - (transaction_details.product_cost * transaction_details.product_qty)) / transaction.grandTotal as MP, users.name as AdminName FROM transaction
    LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID
    LEFT JOIN users on users.id = transaction.created_by ";

    $price = 'SELECT TotalCostPrice as costprice, TotalSellingPrice as sellingprice, grandTotal as GrandTotal from transaction';

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


    if( $search != 'no' ) { //age
        $rangeArray = explode("_",$daterange); 

        $startDate = $rangeArray[0]. ' 00:00:00';
        $endsDate = $rangeArray[1]. ' 23:59:59';

        $DataQuery .=" WHERE transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid." GROUP BY transaction.transactionID ORDER BY transaction.created_date DESC LIMIT ".$request['start']." ,".$request['length']." ";
        

        $price .=" WHERE transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ". $status_paid;
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);

        $costprice = $price['costprice'];
        $sellingprice = $price['sellingprice'];
        $GrandTotal = $price['GrandTotal'];

        $stmt = $config->runQuery($DataQuery);
        $stmt->execute();

        $count = $config->runQuery($DataQuery);
        $count->execute(); 

        $totalFilter = $count->rowCount();
        $totalData = $totalFilter;

    } else {
        
        $price .=' WHERE MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE())';
        $submitprice = $config->runQuery($price);
        $submitprice->execute();

        $price = $submitprice->fetch(PDO::FETCH_LAZY);
        
        $costprice = $price['costprice'];
        $sellingprice = $price['sellingprice'];
        $GrandTotal = $price['GrandTotal'];

        $DataQuery.=" WHERE MONTH(transaction.created_date) = MONTH(CURRENT_DATE())
        AND YEAR(transaction.created_date) = YEAR(CURRENT_DATE()) GROUP BY transaction.transactionID ORDER BY transaction.created_date DESC LIMIT ".$request['start']." ,".$request['length']." ";
        $stmt = $config->runQuery($DataQuery);
        $stmt->execute();

        $stmt2 = $config->runQuery($DataQuery);
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
            $subdata[]  = $statuspaid;
            $subdata[]  = $datepaid;
            $subdata[]  = $config->formatPrice($row['TotalCostPrice']);
            $subdata[]  = $config->formatPrice($row['TotalSellingPrice']);
            $subdata[]  = ceil($row['MP']).'%';
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
        'subtotal'          => $selisihPembayaran
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