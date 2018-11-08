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
function kekata($x) {
    $x = abs($x);
    $angka = array("", "satu", "dua", "tiga", "empat", "lima",
    "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($x <12) {
        $temp = " ". $angka[$x];
    } else if ($x <20) {
        $temp = kekata($x - 10). " belas";
    } else if ($x <100) {
        $temp = kekata($x/10)." puluh". kekata($x % 10);
    } else if ($x <200) {
        $temp = " seratus" . kekata($x - 100);
    } else if ($x <1000) {
        $temp = kekata($x/100) . " ratus" . kekata($x % 100);
    } else if ($x <2000) {
        $temp = " seribu" . kekata($x - 1000);
    } else if ($x <1000000) {
        $temp = kekata($x/1000) . " ribu" . kekata($x % 1000);
    } else if ($x <1000000000) {
        $temp = kekata($x/1000000) . " juta" . kekata($x % 1000000);
    } else if ($x <1000000000000) {
        $temp = kekata($x/1000000000) . " milyar" . kekata(fmod($x,1000000000));
    } else if ($x <1000000000000000) {
        $temp = kekata($x/1000000000000) . " trilyun" . kekata(fmod($x,1000000000000));
    }     
        return $temp;
}
function terbilang($x, $style=4) {
    if($x<0) {
        $hasil = "minus ". trim(kekata($x));
    } else {
        $hasil = trim(kekata($x));
    }     
    switch ($style) {
        case 1:
            $hasil = strtoupper($hasil);
            break;
        case 2:
            $hasil = strtolower($hasil);
            break;
        case 3:
            $hasil = ucwords($hasil);
            break;
        default:
            $hasil = ucfirst($hasil);
            break;
    }     
    return $hasil;
}

$transactionID = $_GET['transactionID'];

$data = $config->getData('transaction.*, provinces.name as ProvinsiName, regencies.name as KotaName, districts.name as Kecamatan, villages.name as Kelurahan', 'transaction LEFT JOIN provinces ON provinces.id = transaction.provinsi_id LEFT JOIN regencies on regencies.id = transaction.kota_id LEFT JOIN districts ON districts.id = transaction.kecamata_id LEFT JOIN villages on villages.id = transaction.kelurahan_id', "transactionID = '". $transactionID ."' ");

$product = $config->runQuery("SELECT * FROM transaction_details WHERE id_trx = '". $transactionID ."'");
$product->execute();
$subtotal = $config->getData('SUM(product_price * product_qty) as Subtotal', 'transaction_details', "id_trx = '". $transactionID ."'");

$dataproduct = [];
    while($row = $product->fetch(PDO::FETCH_LAZY)) {
        $dataproduct[] = '<tr>
                        <td id="1" >'. str_replace('_', ' ', $row['product_name']) .'</td>
                        <td id="2" >'. $row['product_qty'] .'</td>
                        <td id="3" >'. $config->formatPrice($row['product_price']) .'</td>
                        <td id="4" >'. $config->formatPrice($row['product_qty'] * $row['product_price']) .'</td>
                    </tr>';
    }
$dataproduct = implode(' ', $dataproduct);
$total = ($subtotal['Subtotal'] + $data['delivery_charge'] + $data['delivery_charge_time']) - 0;
$content = '
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <title>Invoice</title>
   <body>
      <style type="text/css">
         body{
         font-size: 13px;
         font-family: "Arial", serif;
         width: 100%;
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
         .info {
             width: 100%;
         }
         .info tr td[id="1"] {
            vertical-align: top;
            font-size: 13px;
            font-family: Arial, "sans-serif";
             padding-left: 2%;
            width: 15%;
            height: 20px;
         }
         .info tr td[id="2"] {
            vertical-align: top;
            font-size: 13px;
            font-family: Arial, "sans-serif";
             text-align: center;
            width: 2%;
            height: 20px;
         }
         .info tr td[id="3"] {
            vertical-align: top;
            font-size: 13px;
            font-family: Arial, "sans-serif";
            padding-left: 2%;
            width: 83%;
            height: 20px;
            text-transform: none;
         }
         .summary {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
         }
         .summary thead tr th[id="1"] {
             width: 65%;
             font-family: Arial, "sans-serif";
             text-align: center;
             height: 20px;
         }
         .summary thead tr th[id="2"] {
             width: 5%;
             font-family: Arial, "sans-serif";
             text-align: center;
             height: 20px;
         }
         .summary thead tr th[id="3"] {
             width: 15%;
             font-family: Arial, "sans-serif";
             text-align: right;
             padding-right: 10px;
             height: 20px;
         }
         .summary thead tr th[id="4"] {
             width: 20%;
             font-family: Arial, "sans-serif";
             text-align: right;
             padding-right: 10px;
             height: 20px;
         }
         .summary tbody tr td[id="1"] {
             font-family: Arial, "sans-serif";
             font-size: 13px;
             height: 20px;
             padding-left: 5px;
             text-transform: capitalize;
         }
         .summary tbody tr td[id="2"] {
             font-family: Arial, "sans-serif";
             font-size: 13px;
             height: 20px;
             text-align: center;
         }
         .summary tbody tr td[id="3"] {
             font-family: Arial, "sans-serif";
             font-size: 13px;
             height: 20px;
             text-align: right;
             padding-right: 10px;
         }
         .summary tbody tr td[id="4"] {
             font-family: Arial, "sans-serif";
             font-size: 13px;
             height: 20px;
             text-align: right;
             padding-right: 10px;
         }
         .footer {
            border-collapse: collapse;
            width: 100%;
         }
         .footer tr td[id="1"]{
             width: 60%;
             font-family: Arial, "sans-serif";
             border-bottom: 1px dashed #000000;
             font-weight: 600;
             font-style: italic;
             text-transform: capitalize;
             font-size: 14px;
         }
         .footer tr td[id="2"]{
             width: 30%;
             text-align: center;
             font-family: Arial, "sans-serif";
         }
         .footer tr td[id="3"]{
             width: 60%;
             font-weight: 600;
             font-size: 10px;
             font-family: Arial, "sans-serif";
         }
         .footer tr td[id="4"]{
             width: 30%;
             text-align: center;
             vertical-align: bottom;
             font-family: Arial, "sans-serif";
         }
         .invoice {
            font-family: Arial, "sans-serif";
         }
      </style>
      <div style="font-family:Arial;width: 793px;margin-bottom:-100px;  font-size:12px;">
         <div style="width: 70%;float: left;">
            <left><img src="../../assets/images/logo.png" width="150px" height="40px" style="margin-left:0px;"></left>
         </div>
         <div style="width: 20%;float:right;">
            <h4>INVOICE</h4>
         </div>
      </div>
      <div class="clearfix" style="clear: both;"></div>
      <div style="margin-bottom: 5px;">
         <table width="100%" border="0" style="border-collapse: collapse" cellpadding="0">
            <tr>
                <td width="70%" style="padding-right: 1px;">Jl. Sasak II No.69, RT.2/RW.2, Klp. Dua, Kb. Jeruk, Kota Jakarta Barat, DKI Jakarta 11550, Indonesia<br>
         24 Hours Hotline: +62811133364 | Fax : 021 2253 0466 | Email : info@bungadavi.co.id <br>
         Website : http://www.bungadavi.co.id</td>
                <td width="30%" style="padding-left: 2px;">
                    <table width="100%" border="0" style="border-collapse: collapse" >
                        <tr>
                            <td class="invoice" width="40%" style="font-size: 11px; padding: 2px; height:15px; vertical-align: top; font-weight: 500;">INVOICE</td>
                            <td class="invoice" width="2%">:</td>
                            <td class="invoice" width="60%" style="font-size: 12px; padding: 2px; vertical-align: top; font-weight: 600;">'. $data['transactionID'] .'</td>
                        </tr>
                        <tr>
                            <td class="invoice" style="font-size: 11px; padding: 2px; height:15px; vertical-align: top; font-weight: 500;">DELIVERY DATE</td>
                            <td class="invoice" style="font-size: 11px; padding: 2px; vertical-align: top; font-weight: 500;">:</td>
                            <td class="invoice" style="font-size: 12px; padding: 2px; vertical-align: top; font-weight: 600;">'. $config->_formatdate($data['delivery_date']) .'</td>
                        </tr>
                    </table>
                </td>
            </tr>
         </table>
      </div>
      <div style="border-bottom: 1px solid #000000;"></div>
      <table class="info" border="0" style="border-collapse: collapse; margin-top: 5px; margin-bottom: 5px;" cellpadding="0">
        <tr>
            <td id="1" >Invoice Name</td>
            <td id="2" >:</td>
            <td id="3" style="text-transform: capitalize;">'. $data['invoice_name'] .'</td>
        </tr>
        <tr>
            <td id="1" >Recipient Name</td>
            <td id="2" >:</td>
            <td id="3" style="text-transform: capitalize;">'. $data['nama_penerima'] .'</td>
        </tr>
        <tr>
            <td id="1">Delivery Address</td>
            <td id="2" >:</td>
            <td id="3">'. $data['alamat_penerima'].', '. $data['Kelurahan']. ', '. $data['Kecamatan']. ', '. $data['KotaName']. ', '. $data['ProvinsiName'] .'</td>
        </tr>
      </table>
      <div class="clearfix"></div>
      <table class="summary" border="1">
         <thead>
            <tr>
               <th id="1">Item Description</th>
               <th id="2">Qty</th>
               <th id="3">Price</th>
               <th id="4">Total</th>
            </tr>
         </thead>
         <tbody>'. $dataproduct .'
            <tr>
               <td id="3" colspan="3">Subtotal</td>
               <td id="3">'. $config->formatPrice($subtotal['Subtotal']) .'</td>
            </tr>
            <tr>
               <td id="3" colspan="3">Delivery Charge + Delivery Time</td>
               <td id="3">'. $config->formatPrice($data['delivery_charge'] + $data['delivery_charge_time']) .'</td>
            </tr>
            <tr>
               <td id="3" colspan="3">Discount</td>
               <td id="3">'. $config->formatPrice(0) .'</td>
            </tr>
            <tr>
               <td id="3" colspan="3">Grand Total</td>
               <td id="3">'. $config->formatPrice($total) .'</td>
            </tr>
         </tbody>
      </table>
      <table border="0" class="footer">
         <tr>
            <td id="1">Terbilang:  '. terbilang($total, 3) .' rupiah </td>
            <td id="2">Hormat Kami,</td>
         </tr>
         <tr>
            <td id="3">
                <table border="0" style="border-collapges: collapse;">
                    <tr>
                        <td>
                        <br>
                        Pembayaran Mohon Di Tujukan Kepada :<br>
                        REK. BCA 1113331333 .an CV. BUNGA DAVI KREASI<br>
                        Mohon Berita Pembayaran Dengan Nomor INVOICE 
                        Atau Konfirmasi Sertakan Bukti Pembayaran
                        </td>
                    </tr>
                </table>
            </td>
            <td id="4">
                <table border="0" style="border-collapges: collapse;">
                    <tr>
                        <td style="padding-left: 25px;">
                        <br>
                        <br>
                        <br>
                        (___________________________)
                        </td>
                    </tr>
                </table>
            </td>
         </tr>
      </table>
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
$dompdf->stream("Invoice-". $data['transactionID'],array("Attachment"=>0));