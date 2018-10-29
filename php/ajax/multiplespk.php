<?php
require '../../config/config.php';
require_once '../../assets/vendors/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
$dompdf = new Dompdf();

$transactionID = $_GET['transactionID'];
$transactionID = explode(',', $transactionID);

$total = count($transactionID);
$spk = [];


$contentHeader = '
  <html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>SPK</title>
    <style type="text/css">
        body{
                margin: 0 auto;
                width: 0 auto;
                height: 297mm;
                border: 0px solid;
            }
            .title {
            border: 0px solid;
            padding: 10px 0px 10px 10px;
            font-size: 18px;
            font-family: Arial, "sans-serif";
            font-weight: 600;
            border-bottom: 2px dashed #c1c1c1;
            background-color: #c1c1c1;
            }
            .images {
            border: 0px solid;
            padding: 20px 0px 20px 0px;
            text-align: center;
            }
            .images img{
            width: 490px;
            height: auto;
            padding: 2px;
            border: 1px dashed #c1c1c1;
            border-radius: 12px
            }
            .info{
            border: 0px solid;
            padding: 20px 0px 20px 40px;
            text-align: center;
            background-color: #c1c1c1;
            border-radius: 10px;
            margin: 0px 10px;
            height: 390px;
            }
            .table-info {
            text-align: center;
            border: 0px solid ;
            width: 600px;
            border-collapse: collapse;
            }
            .table-info tr td{
            border-bottom: 1px dashed;
            font-family: Arial, "sans-serif";
            height: 30px;
            vertical-align: top;
            font-size: 16px;
            }
            .table-info tr td[id="1"] {
            width: 60px;
            text-align: left;
            padding-left: 10px;
            font-weight: 600;
            }
            .table-info tr td[id="2"] {
            width: 2px;
            font-weight: 600;
            }
            .table-info tr td[id="3"] {
            width: 200px;
            text-align: justify;
            padding: 3px 10px;
            font-weight: 600;
            }
    </style>
  <body>
';
$contentFooter = '
  </body>
  </head>
  </html>
';

foreach($transactionID as $key => $val) {
//   if($total > 0) $html .= '<div style="page-break-before: always;"></div>';

    $arrtime = [
      0 => '9am - 1pm',
      1 => '2pm - 5pm',
      2 => '6pm - 8pm',
      3 => '9pm - 0am',
      4 => '1am - 5am',
      5 => '6am - 8am'
    ];

    $transactionID = $val;
    $data = $config->getData('*', 'transaction', "transactionID = '". $transactionID ."' ");
    $sql = "SELECT transaction.*, transaction_details.id_trx, transaction_details.florist_remarks, transaction_details.product_qty, products.name_product, products.images FROM transaction
          LEFT JOIN transaction_details ON transaction_details.id_trx = transaction.transactionID
          LEFT JOIN products ON products.product_id = transaction_details.id_product
          WHERE transaction.transactionID = :id";
    $stmt = $config->runQuery($sql);
    $stmt->execute(array(':id' => $transactionID));

    $dataproduct = [];
    while($row = $stmt->fetch(PDO::FETCH_LAZY)) {
      $dataproduct[] = '
          <div class="title">
          '. $row['transactionID'] .'
        </div>
        <div class="images">
          <img src="../../assets/images/product/'. $row['images'] .'" alt="">
        </div>
        <div class="info">
          <table class="table-info">
              <tr>
                  <td id="1" style="vertical-align: middle;">Invoice Number</td>
                  <td id="2" style="vertical-align: middle;">:</td>
                  <td id="3" style="vertical-align: middle;">'. $row['transactionID'] .'</td>
              </tr>
              <tr>
                  <td id="1">Remarks</td>
                  <td id="2">:</td>
                  <td id="3">
                  '. $row['florist_remarks'] .'
                  </td>
              </tr>
              <tr>
                  <td id="1" style="vertical-align: middle;">Quantity</td>
                  <td id="2" style="vertical-align: middle;">:</td>
                  <td id="3" style="vertical-align: middle;">'. $row['product_qty'] .'</td>
              </tr>
              <tr>
                  <td id="1" style="vertical-align: middle;">Delivery Date</td>
                  <td id="2" style="vertical-align: middle;">:</td>
                  <td id="3" style="vertical-align: middle;">'. $row['delivery_date'] .'</td>
              </tr>
              <tr>
                  <td id="1">Delivery Time</td>
                  <td id="2">:</td>
                  <td id="3">'. $arrtime[$row['delivery_time']] .'</td>
              </tr>
              <tr>
                  <td id="1">Alamat lengkap</td>
                  <td id="2">:</td>
                  <td id="3"style="text-transform: capitalize;">'. $data['alamat_penerima'].', '. $data['Kelurahan']. ', '. $data['Kecamatan']. ', '. $data['KotaName']. ', '. $data['ProvinsiName'] .'</td>
              </tr>
          </table>
        </div>    
    ';
    }
    $dataproduct = implode(' ', $dataproduct);
  $invoices[] = $dataproduct;
}
    $content = $contentHeader . implode( '<div style="page-break-before: always;"></div>' , $invoices ) . $contentFooter;
    // echo $content;

  $dompdf->loadHtml($content);
    
    // (Opsional) Mengatur ukuran kertas dan orientasi kertas
    $dompdf->setPaper(array(0, 0, 595.28, 841.89), 'portrait');
    
    // Menjadikan HTML sebagai PDF
    $dompdf->render();
    
    // Output akan menghasilkan PDF (1 = download dan 0 = preview)
    $dompdf->stream("SPK-". $val,array("Attachment"=>0));
?>