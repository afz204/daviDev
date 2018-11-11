<?php 

session_start();
require '../../config/api.php';
$config = new Admin();
$admin = $config->adminID();
require_once '../../assets/vendors/PHPExcel/Classes/PHPExcel.php';
$Excel = new PHPExcel();

if($_GET['type'] == 'kasout') {
    

    /** Include PHPExcel */
   
    $daterange = $_GET['date_range'];
    $adminid = $_GET['status_paid'];
    $rangeArray = explode("_",$daterange); 

    $status_paid = ' AND kas.admin_id = '.$adminid;
    if($adminid == 99) {
        $status_paid = '';
    }
    
    $DataQuery = " SELECT kas.id, kas.nama, kas.qty, kas.harga, kas.satuan, kas.ket, kas.created_at, kas.report_at, kas.status, users.name, cat.content as category, subcat.category as subCategory FROM kas_outs AS kas INNER JOIN users ON users.id = kas.admin_id
    LEFT OUTER JOIN satuans AS cat ON cat.id = kas.type
    LEFT OUTER JOIN satuans AS subcat ON subcat.id = kas.sub_type WHERE";
    $startDate = $rangeArray[0]. ' 00:00:00';
    $endsDate = $rangeArray[1]. ' 23:59:59';

    $DataQuery .=" kas.created_at BETWEEN '". $startDate ."' AND '". $endsDate ."' ".$status_paid." ORDER BY kas.id ASC ";
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
    ->setCellValue('A1', 'LAPORAN BELANJA PRODUKSI BUNGA DAVI')
    ->mergeCells('A1:K1')
    ->getStyle("A1:K1")->applyFromArray($stylecenter);

    $Excel->setActiveSheetIndex(0)
    ->setCellValue('A3', 'NO')
    ->setCellValue('B3', 'Category')
    ->setCellValue('C3', 'Sub Category')
    ->setCellValue('D3', 'Nama Pengeluaran')
    ->setCellValue('E3', 'Keterangan')
    ->setCellValue('F3', 'Quantity')
    ->setCellValue('G3', 'Satuan')
    ->setCellValue('H3', 'Harga')
    ->setCellValue('I3', 'Total')
    ->setCellValue('J3', 'Admin')
    ->setCellValue('K3', 'Created Date')
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

        $Excel->getActiveSheet()
        ->setCellValue('A'.$loop, $nomor++)
        ->setCellValue('B'.$loop, $row['category'])
        ->setCellValue('C'.$loop, $row['subCategory'])
        ->setCellValue('D'.$loop, $row['nama'])
        ->setCellValue('E'.$loop, $row['ket'])
        ->setCellValue('F'.$loop, $row['qty'])
        ->setCellValue('G'.$loop, $row['satuan'])
        ->setCellValue('H'.$loop, $row['harga'])
        ->setCellValue('I'.$loop, $row['qty'] * $row['harga'])
        ->setCellValue('J'.$loop, $row['name'])
        ->setCellValue('K'.$loop, $row['created_at']);

        $loop++;
    }
   
    $filename = str_replace(' ', '_', 'Laporan Belanja Produksi '.$daterange);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');
    $writer->save('php://output');
}