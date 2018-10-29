<?php 

session_start();
require '../../config/api.php';
$config = new Admin();
$admin = $config->adminID();


if($_GET['type'] == 'exportrevenue') {

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    date_default_timezone_set('Europe/London');

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    /** Include PHPExcel */
    require_once '../../assets/vendors/PHPExcel/Classes/PHPExcel.php';

    $Excel = new PHPExcel();

    $daterange = $_GET['date_range'];
    $statuspaid = $_GET['status_paid'];
    $rangeArray = explode("_",$daterange); 

    $status_paid = 'AND transaction.statusPaid = '.$statuspaid;
    if($statuspaid == 2) {
        $status_paid = '';
    }
    
    $DataQuery = " SELECT transaction.* , (transaction.grandTotal - transaction.TotalCostPrice) / transaction.grandTotal as MP, users.name as AdminName, florist.FloristName FROM transaction
    LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID
    LEFT JOIN users on users.id = transaction.created_by 
    LEFT JOIN florist on florist.ID = transaction.id_florist
    WHERE transaction.statusOrder NOT IN (6, 99) AND ";
    $startDate = $rangeArray[0]. ' 00:00:00';
    $endsDate = $rangeArray[1]. ' 23:59:59';

    $DataQuery .=" transaction.delivery_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid." GROUP BY transaction.transactionID ORDER BY transaction.delivery_date ASC ";
    // var_dump($DataQuery);
    $data = $config->runQuery($DataQuery);
    $data->execute();

    $stylecenter = array(
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'size'  => 14,
        )
    );
    $header = array(
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'size'  => 12,
        )
    );

    $Excel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'LAPORAN PEMBUKUAN (KEUNTUNGAN) BUNGA DAVI')
    ->mergeCells('A1:K1')
    ->getStyle("A1:K1")->applyFromArray($stylecenter);

    $Excel->setActiveSheetIndex(0)
    ->setCellValue('A3', 'NO')
    ->setCellValue('B3', 'No Invoice')
    ->setCellValue('C3', 'Pengirim')
    ->setCellValue('D3', 'Dibuat Oleh')
    ->setCellValue('E3', 'Tanggal Order')
    ->setCellValue('F3', 'Tanggal Pengiriman')
    ->setCellValue('G3', 'Status Pembayaran')
    ->setCellValue('H3', 'Tanggal Lunas')
    ->setCellValue('I3', 'Cost Price')
    ->setCellValue('J3', 'Selling Price')
    ->setCellValue('K3', 'Perangkai')
    ->getStyle("A3:K3")->applyFromArray($header);

    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('A')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('B')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('C')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('D')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('E')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('F')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('G')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('H')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('I')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('J')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('K')->setAutoSize(true);

    $Excel->getActiveSheet()->freezePane('A4');
    $nomor = 1;
    $loop = 4;
    while($row = $data->fetch(PDO::FETCH_LAZY)) {

        $statuspaid = 'UNPAID';
            if($row['statusPaid'] == 1) $statuspaid = 'PAID';

        $paydate = $row['PaidDate'];
        if(strtotime($row['PaidDate']) == false) {
            $paydate = 'unset';
        }
        $Excel->getActiveSheet()
        ->setCellValue('A'.$loop, $nomor++)
        ->setCellValue('B'.$loop, $row['transactionID'])
        ->setCellValue('C'.$loop, $row['CustomerName'])
        ->setCellValue('D'.$loop, $row['AdminName'])
        ->setCellValue('E'.$loop, $row['created_date'])
        ->setCellValue('F'.$loop, $row['delivery_date'])
        ->setCellValue('G'.$loop, $statuspaid)
        ->setCellValue('H'.$loop, $paydate)
        ->setCellValue('I'.$loop, $row['TotalCostPrice'])
        ->setCellValue('J'.$loop, $row['grandTotal'])
        ->setCellValue('K'.$loop, $row['FloristName']);

        $loop++;
    }
   
    $filename = str_replace(' ', '_', 'Laporan Revenue '.$daterange);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');
    $writer->save('php://output');
}
if($_GET['type'] == 'exportpiutang') {
    /** Include PHPExcel */
    require_once '../../assets/vendors/PHPExcel/Classes/PHPExcel.php';

    $Excel = new PHPExcel();

    $daterange = $_GET['date_range'];
    $statuspaid = $_GET['status_paid'];
    $rangeArray = explode("_",$daterange); 

    $status_paid = 'AND transaction.statusPaid = '.$statuspaid;
    if($statuspaid == 2) {
        $status_paid = '';
    }
    
    $DataQuery = " SELECT transaction_details.product_name, transaction.*, users.name as AdminName, florist.FloristName FROM transaction_details
    LEFT JOIN transaction on transaction.transactionID = transaction_details.id_trx
    LEFT JOIN users on users.id = transaction.created_by 
    LEFT JOIN florist on florist.ID = transaction.id_florist
    WHERE transaction.statusOrder NOT IN (6, 99) AND ";
    $startDate = $rangeArray[0]. ' 00:00:00';
    $endsDate = $rangeArray[1]. ' 23:59:59';

    $DataQuery .=" (transaction.delivery_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid.") ORDER BY transaction.delivery_date ASC";
    // $DataQuery .=" (transaction.created_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid.") GROUP BY transaction.transactionID";
    // var_dump($DataQuery);
    $data = $config->runQuery($DataQuery);
    $data->execute();

    $stylecenter = array(
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'size'  => 14,
        )
    );
    $header = array(
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'size'  => 12,
        )
    );

    $Excel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'LAPORAN PEMBUKUAN (PIUTANG)')
    ->mergeCells('A1:L1')
    ->getStyle("A1:L1")->applyFromArray($stylecenter);

    $Excel->setActiveSheetIndex(0)
    ->setCellValue('A3', 'NO')
    ->setCellValue('B3', 'No Invoice')
    ->setCellValue('C3', 'Nama Pemesan')
    ->setCellValue('D3', 'Dibuat Oleh')
    ->setCellValue('E3', 'Nama Pengirim')
    ->setCellValue('F3', 'Product Name')
    ->setCellValue('G3', 'Invoice Name')
    ->setCellValue('H3', 'Tanggal Order')
    ->setCellValue('I3', 'Tanggal Pengiriman')
    ->setCellValue('J3', 'Status Pembayaran')
    ->setCellValue('K3', 'Florist')
    ->setCellValue('L3', 'Selling Price')
    ->getStyle("A3:L3")->applyFromArray($header);

    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('A')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('B')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('C')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('D')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('E')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('F')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('G')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('H')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('I')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('J')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('K')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('L')->setAutoSize(true);

    $Excel->getActiveSheet()->freezePane('A4');
    $nomor = 1;
    $loop = 4;
    while($row = $data->fetch(PDO::FETCH_LAZY)) {

        $statuspaid = 'UNPAID';
            if($row['statusPaid'] == 1) $statuspaid = 'PAID';

        $paydate = $config->_formatdate($row['PaidDate']);
        if(strtotime($row['PaidDate']) == false) {
            $paydate = 'unset';
        }
        $Excel->getActiveSheet()
        ->setCellValue('A'.$loop, $nomor++)
        ->setCellValue('B'.$loop, $row['transactionID'])
        ->setCellValue('C'.$loop, $row['CustomerName'])
        ->setCellValue('D'.$loop, $row['AdminName'])
        ->setCellValue('E'.$loop, $row['card_from'])
        ->setCellValue('F'.$loop, "'".$config->_parsingproductname($row['product_name'])."'")
        ->setCellValue('G'.$loop, $row['invoice_name'])
        ->setCellValue('H'.$loop, Date('Y-m-d', strtotime($row['created_date'])))
        ->setCellValue('I'.$loop, $row['delivery_date'])
        ->setCellValue('J'.$loop, $statuspaid)
        ->setCellValue('K'.$loop, $row['FloristName'])
        ->setCellValue('L'.$loop, $row['grandTotal']);

        $loop++;
    }

    $filename = str_replace(' ', '_', 'Laporant Piutang '.$daterange);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');
    $writer->save('php://output');
}
if($_GET['type'] == 'exportbonus') {

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    date_default_timezone_set('Europe/London');

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    /** Include PHPExcel */
    require_once '../../assets/vendors/PHPExcel/Classes/PHPExcel.php';

    $Excel = new PHPExcel();

    $daterange = $_GET['date_range'];
    $statuspaid = $_GET['status_paid'];
    $rangeArray = explode("_",$daterange); 

    $status_paid = 'AND transaction.statusOrder = '.$_GET['status_paid'].'';
    if($_GET['status_paid'] == 0) {
        $status_paid = '';
    }
    
    $DataQuery = " SELECT transaction.* , (transaction.grandTotal - transaction.TotalCostPrice) / transaction.grandTotal as MP, users.name as AdminName, florist.FloristName FROM transaction
    LEFT JOIN transaction_details on transaction_details.id_trx = transaction.transactionID
    LEFT JOIN users on users.id = transaction.created_by 
    LEFT JOIN florist on florist.ID = transaction.id_florist
    WHERE transaction.statusOrder NOT IN (6, 99) AND ";
    $startDate = $rangeArray[0]. ' 00:00:00';
    $endsDate = $rangeArray[1]. ' 23:59:59';

    $DataQuery .=" transaction.delivery_date BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid." GROUP BY transaction.transactionID AND transaction.statusPaid = 1 ORDER BY transaction.delivery_date ASC ";
    $data = $config->runQuery($DataQuery);
    $data->execute();
    // var_dump($DataQuery);
    
    $total = $config->getData('COUNT(transaction.id) as Total', 'transaction', "transaction.statusOrder NOT IN (6, 99) AND transaction.delivery_date BETWEEN'". $startDate ."' AND '". $endsDate ."' ".$status_paid.' AND transaction.statusPaid = 1');
    $MP = $config->getData('SUM(TotalCostPrice) as costprice, SUM(grandTotal) as grandprice', 'transaction', "transaction.statusOrder NOT IN (6, 99) AND transaction.delivery_date BETWEEN'". $startDate ."' AND '". $endsDate ."' ".$status_paid.' AND transaction.statusPaid = 1');
    // echo $total['Total'];
    // var_dump($MP);
    $stylecenter = array(
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'size'  => 14,
        )
    );
    $header = array(
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            'size'  => 12,
        )
    );

    $Excel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'LAPORAN PEMBUKUAN (KOMISI) BUNGA DAVI')
    ->mergeCells('A1:K1')
    ->getStyle("A1:K1")->applyFromArray($stylecenter);

    $Excel->setActiveSheetIndex(0)
    ->setCellValue('A3', 'NO')
    ->setCellValue('B3', 'No Invoice')
    ->setCellValue('C3', 'Pengirim')
    ->setCellValue('D3', 'Dibuat Oleh')
    ->setCellValue('E3', 'Tanggal Order')
    ->setCellValue('F3', 'Tanggal Pengiriman')
    ->setCellValue('G3', 'Status Pembayaran')
    ->setCellValue('H3', 'Tanggal Lunas')
    ->setCellValue('I3', 'Cost Price')
    ->setCellValue('J3', 'Selling Price')
    ->setCellValue('K3', 'MP %')
    ->getStyle("A3:K3")->applyFromArray($header);

    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('A')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('B')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('C')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('D')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('E')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('F')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('G')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('H')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('I')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('J')->setAutoSize(true);
    $Excel->setActiveSheetIndex(0)
    ->getColumnDimension('K')->setAutoSize(true);

    $Excel->getActiveSheet()->freezePane('A4');
    $nomor = 1;
    $loop = 4;
    while($row = $data->fetch(PDO::FETCH_LAZY)) {

        $statuspaid = 'UNPAID';
            if($row['statusPaid'] == 1) $statuspaid = 'PAID';

        $paydate = Date('Y-m-d', strtotime($row['PaidDate']));
        if(strtotime($row['PaidDate']) == false) {
            $paydate = 'unset';
        }
        $Excel->getActiveSheet()
        ->setCellValue('A'.$loop, $nomor++)
        ->setCellValue('B'.$loop, $row['transactionID'])
        ->setCellValue('C'.$loop, $row['CustomerName'])
        ->setCellValue('D'.$loop, $row['AdminName'])
        ->setCellValue('E'.$loop, $config->_formatdate($row['created_date']))
        ->setCellValue('F'.$loop, $row['delivery_date'])
        ->setCellValue('G'.$loop, $statuspaid)
        ->setCellValue('H'.$loop, $paydate)
        ->setCellValue('I'.$loop, $row['TotalCostPrice'])
        ->setCellValue('J'.$loop, $row['grandTotal'])
        ->setCellValue('K'.$loop, ceil($row['MP'] * 100));

        $loop++;
    }

    $costprice = $MP['costprice'];
    $sellingprice = $MP['grandprice'];
    
    $GrandMP = ceil(($sellingprice - $costprice) / $sellingprice);
    $page = $total['Total'] + 5;
    $Excel->setActiveSheetIndex(0)
    ->setCellValue('I'.$page, 'TOTAL COST PRICE :')
    ->setCellValue('I'.($page + 1), 'TOTAL SELL PRICE :')
    ->setCellValue('I'.($page + 2), 'TOTAL MP :')
    ->setCellValue('J'.$page, $costprice)
    ->setCellValue('J'.($page + 1), $sellingprice)
    ->setCellValue('J'.($page + 2), ($GrandMP * 100) );

    $filename = str_replace(' ', '_', 'Laporan Komisi '.$daterange);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');
    $writer->save('php://output');
}