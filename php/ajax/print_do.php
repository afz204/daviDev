<?php 
require '../../config/config.php';
require_once '../../assets/vendors/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
$dompdf = new Dompdf();

//data
$arrtime = [
    0 => '9am - 1pm',
    1 => '2pm - 5pm',
    2 => '6pm - 8pm',
    3 => '9pm - 0am',
    4 => '1am - 5am',
    5 => '6am - 8am'
];

$transactionID = $_GET['transactionID'];

$data = $config->getData('transaction.*, provinces.name as ProvinsiName, regencies.name as KotaName, districts.name as Kecamatan, villages.name as Kelurahan', 'transaction LEFT JOIN provinces ON provinces.id = transaction.provinsi_id LEFT JOIN regencies on regencies.id = transaction.kota_id LEFT JOIN districts ON districts.id = transaction.kecamata_id LEFT JOIN villages on villages.id = transaction.kelurahan_id', "transactionID = '". $transactionID ."' ");

$product = $config->runQuery("SELECT * FROM transaction_details WHERE id_trx = '". $transactionID ."'");
$product->execute();

$dataproduct = [];
    while($row = $product->fetch(PDO::FETCH_LAZY)) {
        $dataproduct[] = '<tr>
                        <td text-align="top" align="left" >'. $row['id_product'] .'</td>
                        <td style="text-transform: capitalize;">'. $row['product_name'] .'</td>
                        <td text-valign="top" align="center" >'. $row['product_qty'] .'</td>
                    </tr>';
    }
$dataproduct = implode(' ', $dataproduct);

$cardmsg = substr($data['card_isi'], 0, 47);
$cardmsg = $config->capitalize($cardmsg).'...';
$deliverytime = 'unset';
    if($data['delivery_time']) { $deliverytime = $arrtime[$data['delivery_time']]; }

$content = '
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <title>Delivery Order '. $transactionID .'</title>
   <body>
      <style type="text/css">
         body{
         font-size: 13px;
         font-family: "Arial", serif;
         width: 100%;
         }
         table.tg{border-spacing:0;line-height:100%;width:793px;padding: 0px; border-collapse: collapse;border:0px solid black;margin-bottom: 5px;}
         table.tg tr{border:0px solid black;vertical-align: top;}
         table.tg td.c1{width: 20%;font-weight: bold;  padding-top:2px;padding-left:10px;vertical-align: top;}
         table.tg td.c2{width: 1%;vertical-align: top;}
         .clearfix:after {
         content: "";
         display: table;
         clear: both;
         }
         table.tg3{border-spacing:0;width:793px;padding: 0px; border-collapse: collapse;border:1px solid black;margin-bottom: 5px;}
         table.tg3 td.c1{width:90px; font-weight: bold;vertical-align: top;}
         table.tg3 td.c2{width: 1%;vertical-align: top;}
         table.tg3 td{vertical-align: top;padding: 10px;}
         .main{overflow: hidden;width: 793px;}
         .content{width: 100%;height: 100%}
         textarea {
         width: 750px;
         height: auto;
         padding: 0px 5px;
         box-sizing: border-box;
         border: 2px solid #ccc;
         border-radius: 4px;
         background-color: #f8f8f8;
         resize: none;
         }
         h4 {
         /*border-top: 1px solid  #5D6975;*/
         color: #5D6975;
         font-size: 2.0em;
         line-height: 1.4em;
         font-weight: normal;
         text-align: left;
         margin: 0 10px ;
         text-decoration: none;
         }
      </style>
      <div style="font-family:Arial;width: 793px;margin-bottom:-100px;  font-size:12px;">
         <div style="width: 70%;float: left;">
            <left><img src="../../assets/images/logo.png" width="200px" height="60px" style="margin-left:0px;"></left>
         </div>
         <div style="width: 30%;float:right;">
            <h4>DELIVERY ORDER</h4>
         </div>
      </div>
      <div class="clearfix" style="clear: both;"></div>
      <div class="" style="margin-bottom: 10px;">
         Jl. Sasak II No.69, RT.2/RW.2, Klp. Dua, Kb. Jeruk, Kota Jakarta Barat, DKI Jakarta 11550, Indonesia<br>
         24 Hours Hotline: +62811133364 | Fax : 021 2253 0466 | Email : info@bungadavi.co.id <br>
         Website : http://www.bungadavi.co.id
      </div>
      <div class="clearfix" style="clear: both;"></div>
      <div style="margin-bottom: 10px;border-bottom: 1px solid #000000;"></div>
      <br>
      <table border="0" class="tg" style="border-collapse: collapse" cellpadding="0">
         <tr>
            <td width="80%" height="50px;">
               <table border="0" cellpadding="0" width="100%" height="100%" style="height:auto;border-collapse: collapse;border-bottom:0px solid;border-top:0px solid;">
                  <tr>
                     <td width="30%" class="c1" style="height:20px;, vertical-align: middle;">Invoice</td>
                     <td width="5%" class="c2" style="vertical-align: middle;">:</td>
                     <td width="45%" style="vertical-align: middle;">'. $data['transactionID'] .'</td>
                  </tr>
                  <tr>
                     <td class="c1" style="height:20px;, vertical-align: middle; ">Sender</td>
                     <td class="c2" style="vertical-align: middle;">:</td>
                     <td style="vertical-align: middle; text-transform: capitalize;">'. $config->capitalize($data['card_from']) .'</td>
                  </tr>
                  <tr>
                     <td class="c1"  style="height: 20px;">Recipient Name</td>
                     <td class="c2" style="vertical-align: middle; text-transform: capitalize;">:</td>
                     <td style="vertical-align: middle;">'. $config->capitalize($data['nama_penerima']) .' ('. $data['hp_penerima'] .')</td>
                  </tr>
                  <tr>
                     <td class="c1" style="height:20px;, vertical-align: middle;">Card Messages</td>
                     <td class="c2" style="vertical-align: middle;">:</td>
                     <td style="vertical-align: middle;">'. $cardmsg .'</td>
                  </tr>
               </table>
            </td>
            <td width="70%">
               <table border="0"   cellpadding="0" width="100%" height="100%" style="height:auto;border-collapse: collapse;border-bottom:0px solid;border-top:0px solid;">
                  <tr>
                     <td style="height: 20px; vartical-align: middle; font-weight: 600;">Date</td>
                     <td style="height: 20px; vartical-align: middle;">:</td>
                     <td style="height: 20px; vartical-align: middle;">&nbsp;'. $config->_formatdate($data['delivery_date']).'</td>
                  </tr>
                  <tr>
                     <td style="height: 20px; vartical-align: middle; font-weight: 600;">Time</td>
                     <td style="height: 20px; vartical-align: middle;">:</td>
                     <td style="height: 20px; vartical-align: middle;">&nbsp;'. $deliverytime .'</td>
                  </tr>
                  <tr>
                     <td style="height: 20px; vartical-align: middle; font-weight: 600;">Alamat</td>
                     <td style="height: 20px; vartical-align: middle;">:</td>
                     <td style="height: 20px; vartical-align: middle; font-weight: 600;">&nbsp;'. $data['alamat_penerima'].', '. $data['Kelurahan']. ', '. $data['Kecamatan']. ', '. $data['KotaName']. ', '. $data['ProvinsiName'] .'</td>
                  </tr>
                  <tr>
                     <td style="height: 20px; vartical-align: middle; font-weight: 600;">Remarks</td>
                     <td style="height: 20px; vartical-align: middle;">:</td>
                     <td style="height: 20px; vartical-align: middle;">&nbsp;'. $config->capitalize($data['delivery_marks']) .'</td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>
      <table class="tg3" border="1" bgcolor="buttontext">
         <thead>
            <tr style="text-color:#fffff; text-align: center">
               <th width="100px;">Item No</th>
               <th width="600px">Item Description</th>
               <th>Quantity</th>
            </tr>
         </thead>
         <tbody>'. $dataproduct .
         '</tbody>
      </table>
      <div class="clearfix" style="margin-bottom: 10px;"></div>
      <div class="info" style="border-bottom: 1px dashed #000000; margin-bottom: 20px; margin-top: 10px; font-weight: 600;">
         IMPORTANT! Acceptance by the signatory confirms that all goods mentioned above were received in good condition.
      </div>
      <div class="clearfix"></div>
      <div class="acc" style="width:100%;">
         <div class="acc1" style="width: 50%;float: left; border: 0px solid black;">
            <span style="margin:65px;">  Accepted By</span> <br><br><br><br><br>
            (___________________________)
         </div>
         <div class="acc1" style="width: 50%;clear:left;float: right;border: 0px solid black; text-align: right">
            <span style="margin:60px;"> Delivery By</span> <br><br><br><br><br>
            (___________________________)
         </div>
      </div>
      <div class="clearfix"></div>
   </body>
   </head>
</html>
';

// $html = file_get_contents("print_do.php");
// $dompdf->loadHtml($html);
$dompdf->loadHtml($content);

// (Opsional) Mengatur ukuran kertas dan orientasi kertas
$dompdf->setPaper(array(0, 0, 670, 450), 'portrait');

// Menjadikan HTML sebagai PDF
$dompdf->render();

// Output akan menghasilkan PDF (1 = download dan 0 = preview)
$dompdf->stream("Delivery-Order-". $data['transactionID'],array("Attachment"=>0));

?>